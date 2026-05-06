<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $communes = collect([
            ['name' => 'Banikoara', 'department' => 'Alibori'],
            ['name' => 'Gogounou', 'department' => 'Alibori'],
            ['name' => 'Kandi', 'department' => 'Alibori'],
            ['name' => 'Karimama', 'department' => 'Alibori'],
            ['name' => 'Malanville', 'department' => 'Alibori'],
            ['name' => 'Segbana', 'department' => 'Alibori'],

            ['name' => 'Boukoumbe', 'department' => 'Atacora'],
            ['name' => 'Cobly', 'department' => 'Atacora'],
            ['name' => 'Kerou', 'department' => 'Atacora'],
            ['name' => 'Kouande', 'department' => 'Atacora'],
            ['name' => 'Materi', 'department' => 'Atacora'],
            ['name' => 'Natitingou', 'department' => 'Atacora'],
            ['name' => 'Pehunco', 'department' => 'Atacora'],
            ['name' => 'Tanguieta', 'department' => 'Atacora'],
            ['name' => 'Toucountouna', 'department' => 'Atacora'],

            ['name' => 'Abomey-Calavi', 'department' => 'Atlantique', 'latitude' => 6.4485, 'longitude' => 2.3557],
            ['name' => 'Allada', 'department' => 'Atlantique'],
            ['name' => 'Kpomasse', 'department' => 'Atlantique'],
            ['name' => 'Ouidah', 'department' => 'Atlantique'],
            ['name' => 'So-Ava', 'department' => 'Atlantique'],
            ['name' => 'Toffo', 'department' => 'Atlantique'],
            ['name' => 'Tori-Bossito', 'department' => 'Atlantique'],
            ['name' => 'Ze', 'department' => 'Atlantique'],

            ['name' => 'Bembereke', 'department' => 'Borgou'],
            ['name' => 'Kalale', 'department' => 'Borgou'],
            ['name' => 'N Dali', 'department' => 'Borgou'],
            ['name' => 'Nikki', 'department' => 'Borgou'],
            ['name' => 'Parakou', 'department' => 'Borgou', 'latitude' => 9.3372, 'longitude' => 2.6303],
            ['name' => 'Perere', 'department' => 'Borgou'],
            ['name' => 'Sinende', 'department' => 'Borgou'],
            ['name' => 'Tchaourou', 'department' => 'Borgou'],

            ['name' => 'Bante', 'department' => 'Collines'],
            ['name' => 'Dassa-Zoume', 'department' => 'Collines'],
            ['name' => 'Glazoue', 'department' => 'Collines'],
            ['name' => 'Ouesse', 'department' => 'Collines'],
            ['name' => 'Savalou', 'department' => 'Collines'],
            ['name' => 'Save', 'department' => 'Collines'],

            ['name' => 'Aplahoue', 'department' => 'Couffo'],
            ['name' => 'Djakotomey', 'department' => 'Couffo'],
            ['name' => 'Dogbo', 'department' => 'Couffo'],
            ['name' => 'Klouekanme', 'department' => 'Couffo'],
            ['name' => 'Lalo', 'department' => 'Couffo'],
            ['name' => 'Toviklin', 'department' => 'Couffo'],

            ['name' => 'Bassila', 'department' => 'Donga'],
            ['name' => 'Copargo', 'department' => 'Donga'],
            ['name' => 'Djougou', 'department' => 'Donga'],
            ['name' => 'Ouake', 'department' => 'Donga'],

            ['name' => 'Cotonou', 'department' => 'Littoral', 'latitude' => 6.3703, 'longitude' => 2.3912],

            ['name' => 'Athieme', 'department' => 'Mono'],
            ['name' => 'Bopa', 'department' => 'Mono'],
            ['name' => 'Come', 'department' => 'Mono'],
            ['name' => 'Grand-Popo', 'department' => 'Mono'],
            ['name' => 'Houeyogbe', 'department' => 'Mono'],
            ['name' => 'Lokossa', 'department' => 'Mono'],

            ['name' => 'Adjarra', 'department' => 'Oueme'],
            ['name' => 'Adjohoun', 'department' => 'Oueme'],
            ['name' => 'Aguegues', 'department' => 'Oueme'],
            ['name' => 'Akpro-Misserete', 'department' => 'Oueme'],
            ['name' => 'Avrankou', 'department' => 'Oueme'],
            ['name' => 'Bonou', 'department' => 'Oueme'],
            ['name' => 'Dangbo', 'department' => 'Oueme'],
            ['name' => 'Porto-Novo', 'department' => 'Oueme', 'latitude' => 6.4969, 'longitude' => 2.6289],
            ['name' => 'Seme-Kpodji', 'department' => 'Oueme'],

            ['name' => 'Adja-Ouere', 'department' => 'Plateau'],
            ['name' => 'Ifangni', 'department' => 'Plateau'],
            ['name' => 'Ketou', 'department' => 'Plateau'],
            ['name' => 'Pobe', 'department' => 'Plateau'],
            ['name' => 'Sakete', 'department' => 'Plateau'],

            ['name' => 'Abomey', 'department' => 'Zou'],
            ['name' => 'Agbangnizoun', 'department' => 'Zou'],
            ['name' => 'Bohicon', 'department' => 'Zou'],
            ['name' => 'Cove', 'department' => 'Zou'],
            ['name' => 'Djidja', 'department' => 'Zou'],
            ['name' => 'Ouinhi', 'department' => 'Zou'],
            ['name' => 'Za-Kpota', 'department' => 'Zou'],
            ['name' => 'Zagnanado', 'department' => 'Zou'],
            ['name' => 'Zogbodomey', 'department' => 'Zou'],
        ])->mapWithKeys(fn (array $commune) => [
            $commune['name'] => Commune::updateOrCreate(['name' => $commune['name']], [
                'name' => $commune['name'],
                'department' => $commune['department'],
                'latitude' => $commune['latitude'] ?? null,
                'longitude' => $commune['longitude'] ?? null,
            ]),
        ]);

        $cotonou = $communes['Cotonou'];
        $calavi = $communes['Abomey-Calavi'];
        $lokossa = $communes['Lokossa'];

        User::whereIn('email', [
            'admin@smartcity.test',
            'agent@smartcity.test',
            'calavi@smartcity.test',
            'super.littoral@smartcity.test',
        ])->delete();

        $admin = User::firstOrCreate(
            ['email' => 'admin@mairie-cotonou.bj'],
            [
                'name' => 'Direction des Services Techniques',
                'phone' => '+229 21 30 12 45',
                'commune_id' => $cotonou->id,
                'password' => 'password',
                'role' => 'admin',
            ],
        );
        $admin->update(['commune_id' => $cotonou->id, 'department' => $cotonou->department, 'role' => 'admin']);

        $agent = User::firstOrCreate(
            ['email' => 'voirie@mairie-cotonou.bj'],
            [
                'name' => 'Service Voirie et Assainissement',
                'phone' => '+229 21 30 18 62',
                'commune_id' => $cotonou->id,
                'password' => 'password',
                'role' => 'agent',
            ],
        );
        $agent->update(['commune_id' => $cotonou->id, 'department' => $cotonou->department, 'role' => 'agent']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'super.littoral@smartcity.test'],
            [
                'name' => 'Super Admin Littoral',
                'phone' => '+229 21 30 00 01',
                'department' => 'Littoral',
                'password' => 'password',
                'role' => 'super_admin',
            ],
        );
        $superAdmin->update(['commune_id' => null, 'department' => 'Littoral', 'role' => 'super_admin']);

        $calaviAdmin = User::firstOrCreate(
            ['email' => 'technique@mairie-calavi.bj'],
            [
                'name' => 'Service Technique Municipal',
                'phone' => '+229 21 36 05 11',
                'commune_id' => $calavi->id,
                'password' => 'password',
                'role' => 'admin',
            ],
        );
        $calaviAdmin->update(['commune_id' => $calavi->id, 'department' => $calavi->department, 'role' => 'admin']);

        $lokossaAdmin = User::firstOrCreate(
            ['email' => 'admin@mairie-lokossa.bj'],
            [
                'name' => 'Service Technique Municipal de Lokossa',
                'phone' => '+229 22 41 10 00',
                'commune_id' => $lokossa->id,
                'password' => 'password',
                'role' => 'admin',
            ],
        );
        $lokossaAdmin->update(['commune_id' => $lokossa->id, 'department' => $lokossa->department, 'role' => 'admin']);

        $citizen = User::firstOrCreate(
            ['email' => 'citoyen@smartcity.test'],
            [
                'name' => 'Citoyen Demo',
                'phone' => '+229 01 90 00 30 00',
                'commune_id' => $cotonou->id,
                'password' => 'password',
                'role' => 'citoyen',
            ],
        );
        $citizen->update(['commune_id' => $cotonou->id, 'department' => $cotonou->department, 'role' => 'citoyen']);
    }
}
