<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainMembers = [];

        // Create 30 main members
        for ($i = 0; $i < 30; $i++) {
            $memberNo = Member::generateNextMainMemberNo();
            $familyNo = str_replace('-1', '', $memberNo);

            $mainMembers[] = Member::factory()->create([
                'member_no' => $memberNo,
                'family_no' => $familyNo,
                'is_main' => true,
                'parent_id' => null,
                'relation' => 'Self',
                'gender' => 'Male', // Main members are usually male in this context, but can be random too.
            ]);
        }

        // Create 70 family members
        for ($i = 0; $i < 70; $i++) {
            $parent = $mainMembers[array_rand($mainMembers)];

            // We need to refresh the parent to get the latest children count for numbering
            $parent->refresh();
            $memberNo = $parent->generateNextFamilyMemberNo();

            Member::factory()->create([
                'member_no' => $memberNo,
                'family_no' => $parent->family_no,
                'is_main' => false,
                'parent_id' => $parent->id,
                'relation' => fake()->randomElement(['Wife', 'Son', 'Daughter', 'Father', 'Mother']),
            ]);
        }
    }
}
