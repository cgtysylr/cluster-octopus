<?php

namespace Database\Factories;

use App\Models\NetworkConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NetworkConnection>
 */
class NetworkConnectionFactory extends Factory
{

    protected $model = NetworkConnection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return ['source'      => 'node'.$this->faker->numberBetween(1, 10),
                'destination' => 'node'.$this->faker->numberBetween(1, 10),
                'port'        => $this->faker->randomElement([8472, 9099, 51820, 51821]),
                'status'      => $this->faker->boolean(),
                'description' => $this->faker->randomElement(['VXLAN Tunnel', 'CNI Health Check', 'WireGuard IPv4',
                                                              'WireGuard IPv6/dual-stack'])];
    }
}
