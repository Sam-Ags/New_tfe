<?php

namespace Tests\Feature;

use App\Models\Commune;
use App\Models\Incident;
use App\Models\UploadedMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_and_admin_dashboard_are_accessible(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Signaler un incident')
            ->assertSee('/signaler?formulaire=1', false)
            ->assertDontSee('id="public-incident-form"', false);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200);
    }

    public function test_unregistered_user_cannot_login(): void
    {
        $this->post('/login', [
            'email' => 'inconnu@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_public_incident_requires_photo_and_location(): void
    {
        Commune::create([
            'name' => 'Cotonou',
            'department' => 'Littoral',
            'latitude' => 6.3703,
            'longitude' => 2.3912,
        ]);

        $this->post('/incidents', [
            'title' => 'Route dégradée',
            'urgency' => 'urgent',
        ])->assertSessionHasErrors(['photos', 'latitude', 'longitude', 'geolocation_verified']);

        $this->assertDatabaseCount('incidents', 0);
    }

    public function test_public_incident_ignores_empty_photo_slot_without_minimum_size_error(): void
    {
        $response = $this->post('/incidents', [
            'title' => 'Tas d ordures',
            'urgency' => 'normal',
            'latitude' => 6.6528,
            'longitude' => 1.7252,
            'geolocation_verified' => '1',
            'photos' => [
                UploadedFile::fake()->create('empty.jpg', 0, 'image/jpeg'),
            ],
        ]);

        $response
            ->assertSessionHasErrors('photos')
            ->assertSessionDoesntHaveErrors('photos.0');

        $messages = session('errors')->getBag('default')->all();

        $this->assertStringNotContainsString('kilobytes', implode(' ', $messages));
        $this->assertDatabaseCount('incidents', 0);
    }

    public function test_public_incident_reports_upload_error_without_missing_photo_message(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'oversize-upload-');
        file_put_contents($path, 'not-empty');

        $response = $this->post('/incidents', [
            'title' => 'Tas d ordures',
            'urgency' => 'normal',
            'latitude' => 6.6528,
            'longitude' => 1.7252,
            'geolocation_verified' => '1',
            'photos' => [
                new UploadedFile($path, 'camera.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true),
            ],
        ]);

        $response->assertSessionHasErrors('photos.0');

        $messages = session('errors')->getBag('default')->all();

        $this->assertStringNotContainsString('Ajoutez au moins une photo', implode(' ', $messages));
        $this->assertDatabaseCount('incidents', 0);

        @unlink($path);
    }

    public function test_public_incident_form_uses_https_action_behind_proxy(): void
    {
        $this->get('/signaler')
            ->assertRedirect(route('incidents.public.home'));

        $this->withServerVariables([
            'HTTP_HOST' => 'smartcitybenin.online',
            'HTTP_X_FORWARDED_HOST' => 'smartcitybenin.online',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
            'REMOTE_ADDR' => '10.0.0.10',
        ])
            ->get('http://smartcitybenin.online/signaler?formulaire=1')
            ->assertOk()
            ->assertSee('action="https://smartcitybenin.online/incidents"', false)
            ->assertSee('Retour a l&#039;accueil', false)
            ->assertSee('id="photo-camera" class="hidden" type="file" name="photos[]"', false)
            ->assertSee('id="photo-gallery" class="hidden" type="file" name="photos[]"', false)
            ->assertDontSee('action="http://smartcitybenin.online/incidents"', false);
    }

    public function test_uploaded_media_can_be_served_as_image(): void
    {
        $media = UploadedMedia::create([
            'uuid' => '11111111-1111-4111-8111-111111111111',
            'original_name' => 'incident.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 4,
            'contents' => base64_encode('test'),
        ]);

        $this->get(route('media.show', $media->uuid))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertSee('test', false);
    }

    public function test_guest_can_submit_public_incident_with_title_urgency_and_photo(): void
    {
        $commune = Commune::create([
            'name' => 'Cotonou',
            'department' => 'Littoral',
            'latitude' => 6.3703,
            'longitude' => 2.3912,
        ]);

        $this->post('/incidents', [
            'title' => 'Route dégradée',
            'urgency' => 'urgent',
            'latitude' => 6.3812,
            'longitude' => 2.4018,
            'geolocation_verified' => '1',
            'geolocation_accuracy' => 18,
            'location_country' => 'Bénin',
            'location_city' => 'Cotonou',
            'location_zone' => 'Akpakpa',
            'location_address' => 'Akpakpa, Cotonou, Littoral, Bénin',
            'photo' => UploadedFile::fake()->image('incident.jpg'),
        ])->assertRedirect(route('incidents.public.home'));

        $this->assertDatabaseHas('incidents', [
            'title' => 'Route dégradée',
            'user_id' => null,
            'commune_id' => $commune->id,
            'citizen_name' => 'Citoyen anonyme',
            'district' => 'Akpakpa - Cotonou - Bénin',
            'description' => 'Akpakpa, Cotonou, Littoral, Bénin',
            'status' => 'en_attente',
        ]);
        $incident = Incident::first();
        $this->assertSame(6.3812, $incident->latitude);
        $this->assertSame(2.4018, $incident->longitude);
        $this->assertSame(1, Incident::count());
    }

    public function test_guest_can_submit_public_incident_with_photos_array(): void
    {
        $commune = Commune::create([
            'name' => 'Lokossa',
            'department' => 'Mono',
            'latitude' => 6.6533,
            'longitude' => 1.7190,
        ]);

        $this->post('/incidents', [
            'title' => 'Tas d ordures',
            'urgency' => 'normal',
            'latitude' => 6.6528,
            'longitude' => 1.7252,
            'geolocation_verified' => '1',
            'geolocation_accuracy' => 25,
            'location_country' => 'Benin',
            'location_city' => 'Lokossa',
            'location_zone' => 'Centre',
            'location_address' => 'Centre, Lokossa, Mono, Benin',
            'photos' => [
                UploadedFile::fake()->image('incident-camera.jpg'),
            ],
        ])->assertRedirect(route('incidents.public.home'));

        $this->assertDatabaseHas('incidents', [
            'title' => 'Tas d ordures',
            'commune_id' => $commune->id,
            'district' => 'Centre - Lokossa - Benin',
        ]);
    }

    public function test_public_incident_is_sent_to_the_commune_identified_by_gps_address(): void
    {
        Commune::create([
            'name' => 'Cotonou',
            'department' => 'Littoral',
            'latitude' => 6.3703,
            'longitude' => 2.3912,
        ]);

        $calavi = Commune::create([
            'name' => 'Abomey-Calavi',
            'department' => 'Atlantique',
            'latitude' => 6.4485,
            'longitude' => 2.3557,
        ]);

        $this->post('/incidents', [
            'title' => 'Lampadaire en panne',
            'urgency' => 'normal',
            'latitude' => 6.4485,
            'longitude' => 2.3557,
            'geolocation_verified' => '1',
            'geolocation_accuracy' => 25,
            'location_country' => 'Benin',
            'location_city' => 'Abomey-Calavi',
            'location_zone' => 'Zogbadje',
            'location_address' => 'Zogbadje, Abomey-Calavi, Atlantique, Benin',
            'photo' => UploadedFile::fake()->image('lampadaire.jpg'),
        ])->assertRedirect(route('incidents.public.home'));

        $this->assertDatabaseHas('incidents', [
            'title' => 'Lampadaire en panne',
            'commune_id' => $calavi->id,
            'district' => 'Zogbadje - Abomey-Calavi - Benin',
        ]);
    }

    public function test_public_incident_is_rejected_when_gps_zone_is_not_supported(): void
    {
        Commune::create([
            'name' => 'Cotonou',
            'department' => 'Littoral',
            'latitude' => 6.3703,
            'longitude' => 2.3912,
        ]);

        $this->post('/incidents', [
            'title' => 'Caniveau bouché',
            'urgency' => 'urgent',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'geolocation_verified' => '1',
            'geolocation_accuracy' => 30,
            'location_country' => 'France',
            'location_city' => 'Paris',
            'location_zone' => 'Centre',
            'location_address' => 'Centre, Paris, France',
            'photo' => UploadedFile::fake()->image('incident.jpg'),
        ])->assertSessionHasErrors('location');

        $this->assertDatabaseCount('incidents', 0);
    }

    public function test_admin_dashboard_does_not_show_incidents_outside_admin_commune(): void
    {
        $cotonou = Commune::create([
            'name' => 'Cotonou',
            'department' => 'Littoral',
            'latitude' => 6.3703,
            'longitude' => 2.3912,
        ]);

        $lokossa = Commune::create([
            'name' => 'Lokossa',
            'department' => 'Mono',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'commune_id' => $cotonou->id,
            'department' => 'Littoral',
        ]);

        $incident = Incident::create([
            'citizen_name' => 'Citoyen anonyme',
            'commune_id' => $lokossa->id,
            'title' => 'Plainte de Lokossa',
            'category' => 'route',
            'district' => 'Lokossa',
            'description' => 'Incident hors commune.',
            'latitude' => 6.6533,
            'longitude' => 1.7190,
            'urgency' => 'urgent',
            'priority' => 'moyenne',
            'status' => 'en_attente',
        ]);

        $admin->dashboardIncidents()->attach($incident);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Plainte de Lokossa');
    }

    public function test_admin_confirmation_moves_agent_proof_to_resolved_statistics(): void
    {
        $lokossa = Commune::create([
            'name' => 'Lokossa',
            'department' => 'Mono',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'commune_id' => $lokossa->id,
            'department' => 'Mono',
        ]);

        $agent = User::factory()->create([
            'name' => 'Agent Lokossa',
            'role' => 'agent',
            'commune_id' => $lokossa->id,
            'department' => 'Mono',
        ]);

        $incident = Incident::create([
            'citizen_name' => 'Citoyen anonyme',
            'commune_id' => $lokossa->id,
            'title' => 'Debut inondation',
            'category' => 'inondation',
            'district' => 'Lokossa',
            'description' => 'Preuve envoyee par agent.',
            'latitude' => 6.6533,
            'longitude' => 1.7190,
            'urgency' => 'urgent',
            'priority' => 'elevee',
            'status' => 'en_validation',
            'assigned_agent_id' => $agent->id,
            'assigned_to' => $agent->name,
            'completion_photo_path' => 'uploads/completions/proof.jpg',
            'completion_submitted_at' => now(),
        ]);

        $admin->dashboardIncidents()->attach($incident);

        $this->actingAs($admin)
            ->patch(route('incidents.update', $incident), [
                'status' => 'resolu',
                'assigned_agent_id' => $agent->id,
            ])
            ->assertRedirect(route('incidents.index'));

        $incident->refresh();
        $this->assertSame('resolu', $incident->status);
        $this->assertNotNull($incident->resolved_at);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Aucune plainte active dans le tableau de bord')
            ->assertSee('Résolus')
            ->assertSee('>1<', false);
    }
}
