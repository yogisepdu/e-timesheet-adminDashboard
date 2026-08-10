<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contractor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('code', 50)
                ->unique();

            $table->string('equipment_type', 150);

            $table->string('brand', 100)
                ->nullable();

            $table->string('model', 100)
                ->nullable();

            $table->string('registration_number', 100)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_units');
    }
};
