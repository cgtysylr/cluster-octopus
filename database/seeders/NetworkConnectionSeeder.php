<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NetworkConnection;

class NetworkConnectionSeeder extends Seeder
{
    public function run()
    {
        NetworkConnection::factory(10)->create();
    }
}
