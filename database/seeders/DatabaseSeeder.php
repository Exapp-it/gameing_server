<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Payment;
use App\Models\Withdraw;
use App\Models\Strike;

//use App\Services\TomHorn\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::create([
        //     'login'       => 'admin',
        //     'password'    => Hash::make('admin'),
        //     'email'       => 'admin@admin.org',
        //     'fingerprint' => 'admin_fingerprint',
        //     'role'        => 'admin',
        //     'currency'    => 'RUB',
        //     'identity'    => md5('admin@admin.org')
        // ]);
        //$client = app()->make(Client::class);
        //$client->createIdentity(md5('admin@admin.org'), 'RUB');

        User::factory()
            ->has(Payment::factory()->count(4))
            ->has(Withdraw::factory()->count(4))
            ->count(20)
            ->create();
        
        // Strike::factory()
        //     ->count(200)
        //     ->create();
        // \App\Models\User::factory(10)->create();
    }
}
