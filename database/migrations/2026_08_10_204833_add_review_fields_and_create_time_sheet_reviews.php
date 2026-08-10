<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_sheets', function (Blueprint $table): void {
            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('review_notes')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by');
        });

        Schema::create('time_sheet_reviews', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('time_sheet_id')
                ->constrained('time_sheets')
                ->cascadeOnDelete();

            $table->foreignId('reviewer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 30)->index();
            $table->string('previous_status', 30);
            $table->string('new_status', 30)->index();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'time_sheet_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_sheet_reviews');

        Schema::table('time_sheets', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn('reviewed_at');
        });
    }
};
