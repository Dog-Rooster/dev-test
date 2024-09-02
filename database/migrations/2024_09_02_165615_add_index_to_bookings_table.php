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
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['start_time']);
            $table->index('end_time');
            $table->index('attendee_email');
            $table->index(['start_datetime', 'end_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['start_time']);
            $table->dropIndex(['end_time']);
            $table->dropIndex(['attendee_email']);
            $table->dropIndex(['start_datetime', 'end_datetime']);
        });
    }
};
