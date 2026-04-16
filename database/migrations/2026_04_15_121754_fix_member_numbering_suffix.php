<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $members = DB::table('members')->get();

            // First, clear temporarily rename member_no to avoid unique constraints
            foreach ($members as $member) {
                DB::table('members')
                    ->where('id', $member->id)
                    ->update([
                        'member_no' => 'TEMP_'.$member->id.'_'.$member->member_no,
                    ]);
            }

            // Now reassign correctly
            $mainMembers = DB::table('members')->where('is_main', true)->get();

            foreach ($mainMembers as $main) {
                // The family base was original family_no (e.g., GLS-S-001)
                $familyBase = $main->family_no;
                if (! $familyBase) {
                    // Fallback if family_no was not set
                    $familyBase = str_replace(['TEMP_'.$main->id.'_'], '', $main->member_no);
                }

                // Update Main
                DB::table('members')
                    ->where('id', $main->id)
                    ->update([
                        'family_no' => $familyBase,
                        'member_no' => $familyBase.'-1',
                    ]);

                // Update Children
                $children = DB::table('members')
                    ->where('parent_id', $main->id)
                    ->orderBy('id', 'asc')
                    ->get();

                $i = 2;
                foreach ($children as $child) {
                    DB::table('members')
                        ->where('id', $child->id)
                        ->update([
                            'family_no' => $familyBase,
                            'member_no' => $familyBase.'-'.$i,
                        ]);
                    $i++;
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};
