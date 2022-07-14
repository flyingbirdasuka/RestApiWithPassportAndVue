<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $verified = $this->faker->randomElement(array(User::VERIFIED_USER, User::UNVERIFIED_USER));
        if($verified == '1'){
            $verified_token = null;
        } else{
            $verified_token = User::generateVerificationCode();
        }

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $this->password = bcrypt('secret'),
            'remember_token' => Str::random(10),
            'verified' => $verified,
            'verification_token' => $verified_token,
            'admin' => $verified = $this->faker->randomElement(array(User::ADMIN_USER, User::REGULAR_USER)),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
