<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments') && ! Schema::hasColumn('payments', 'service_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('service_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'service_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('service_id');
            });
        }
    }
};
