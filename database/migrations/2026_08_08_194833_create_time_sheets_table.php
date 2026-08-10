<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_sheets', function (Blueprint $table) {
            $table->foreignId('operator_id')
                ->nullable()
                ->change();

            $table->foreignId('equipment_unit_id')
                ->nullable()
                ->change();

            $table->foreignId('activity_id')
                ->nullable()
                ->change();

            $table->date('work_date')
                ->nullable()
                ->change();

            $table->time('start_time')
                ->nullable()
                ->change();

            $table->time('end_time')
                ->nullable()
                ->change();

            $table->decimal('total_hours', 8, 2)
                ->nullable()
                ->change();

            $table->decimal('hm_start', 12, 2)
                ->nullable()
                ->change();

            $table->decimal('hm_end', 12, 2)
                ->nullable()
                ->change();

            $table->decimal('total_hm', 12, 2)
                ->nullable()
                ->change();

            $table->string('location', 255)
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        // Draft dapat berisi data parsial. Karena itu rollback ke NOT NULL
        // sengaja tidak dilakukan otomatis agar data draft yang sudah ada
        // tidak menyebabkan kegagalan rollback.
    }
};
