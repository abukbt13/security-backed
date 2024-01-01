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
        Schema::create('court_cases', function (Blueprint $table) {
            $table->id();
            $table->integer('key_id');
            $table->integer('defendant_id');
            $table->string('defendant_name');
            $table->integer('plaintiff_id');
            $table->string('plaintiff_name');
            $table->string('case_name');
            $table->string('description');
            $table->string('status')->default('active');
            $table->string('type_of_case');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_cases');
    }
};
