<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index(['user_id', 'status'], 'bookings_user_status_index');
                $table->index(['event_id', 'status'], 'bookings_event_status_index');
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index('status', 'payments_status_index');
                $table->index(['user_id', 'status'], 'payments_user_status_index');
            });
        }

        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->index('status', 'events_status_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropIndex('bookings_user_status_index');
                $table->dropIndex('bookings_event_status_index');
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('payments_status_index');
                $table->dropIndex('payments_user_status_index');
            });
        }

        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropIndex('events_status_index');
            });
        }
    }
};
