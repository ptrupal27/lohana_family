<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'member_number')) {
                $table->renameColumn('member_number', 'member_no');
            } else {
                $table->string('member_no')->nullable()->after('id');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('member_no')->unique()->change();
            $table->unsignedBigInteger('parent_id')->nullable()->after('member_no');
            $table->boolean('is_main')->default(true)->after('parent_id');

            $table->foreign('parent_id')->references('id')->on('members')->onDelete('cascade');
        });

        Schema::dropIfExists('family_members');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['member_no', 'parent_id', 'is_main']);
        });
    }
};
