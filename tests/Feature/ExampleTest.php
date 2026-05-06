<?php

namespace Tests\Feature;

use App\Models\Commune;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_and_admin_dashboard_are_accessible(): void
    {
        $this->get('/')->assertStatus(200);

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
        ])->assertSessionHasErrors(['photo', 'latitude', 'longitude', 'geolocation_verified']);

        $this->assertDatabaseCount('incidents', 0);
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
        ])->assertRedirect(route('incidents.public.create'));

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
        ])->assertRedirect(route('incidents.public.create'));

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
}
