<?php

namespace Database\Factories;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::driver('md5')->make('password'), // MD5 for legacy compatibility
            'block' => 0,
            'sendEmail' => 1,
            'registerDate' => now(),
            'lastvisitDate' => now(),
            'activation' => '',
            'params' => '',
            'lastResetTime' => null,
            'resetCount' => 0,
            'otpKey' => '',
            'otep' => '',
            'requireReset' => 0,
        ];
    }

    /**
     * Indicate that the user is blocked.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function blocked()
    {
        return $this->state(function (array $attributes) {
            return [
                'block' => 1,
            ];
        });
    }

    /**
     * Indicate that the user is active (not blocked).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'block' => 0,
            ];
        });
    }
}
