<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'family_no' => null,
            'member_no' => null,
            'parent_id' => null,
            'is_main' => true,
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->firstName(),
            'mother_name' => $this->faker->firstName(),
            'last_name' => 'Lohana',
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'address' => $this->faker->address(),
            'area' => 'Varachha',
            'city_village' => 'Surat',
            'pincode' => '395006',
            'mobile' => $this->faker->numerify('##########'),
            'alternate_mobile' => $this->faker->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'date_of_birth' => $this->faker->date(),
            'occupation' => $this->faker->jobTitle(),
            'hometown' => $this->faker->city(),
            'relation' => null,
        ];
    }
}
