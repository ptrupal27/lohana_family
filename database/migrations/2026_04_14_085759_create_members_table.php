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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('mother_name')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->text('address');
            $table->string('district');
            $table->string('sub_district');
            $table->string('city_village');
            $table->string('pincode');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->date('date_of_birth');
            $table->string('occupation')->nullable();
            $table->string('hometown')->nullable();
            $table->string('relation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
