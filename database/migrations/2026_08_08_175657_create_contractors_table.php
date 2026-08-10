<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)
                ->unique();

            $table->string('name', 150);

            $table->string('type', 20)
                ->default('external')
                ->index();

            $table->string('phone', 20)
                ->nullable();

            $table->text('address')
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
        Schema::dropIfExists('contractors');
    }
};
