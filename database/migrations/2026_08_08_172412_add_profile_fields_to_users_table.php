<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();

            $table->string('username', 50)
                ->nullable()
                ->unique()
                ->after('name');

            $table->string('role', 20)
                ->default('pengawas')
                ->index()
                ->after('password');

            $table->string('position', 100)
                ->nullable()
                ->after('role');

            $table->string('phone', 20)
                ->nullable()
                ->after('position');

            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->after('phone');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'role',
                'position',
                'phone',
                'is_active',
                'last_login_at',
            ]);

            $table->string('email')->nullable(false)->change();
        });
    }
};
