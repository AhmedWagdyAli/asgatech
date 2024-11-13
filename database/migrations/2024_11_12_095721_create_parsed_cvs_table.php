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
        Schema::create('parsed_cvs', function (Blueprint $table) {
            $table->id();
            $table->string('job_title')->index();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->decimal('match_rating', 5, 2)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parsed_c_v_s');
    }
};
