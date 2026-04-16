<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('family_no')->nullable()->after('id')->index();
        });

        // Populate existing data
        $members = DB::table('members')->get();
        foreach ($members as $member) {
            if ($member->is_main) {
                DB::table('members')->where('id', $member->id)->update(['family_no' => $member->member_no]);
            } else {
                $parent = DB::table('members')->where('id', $member->parent_id)->first();
                if ($parent) {
                    DB::table('members')->where('id', $member->id)->update(['family_no' => $parent->member_no]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('family_no');
        });
    }
};
