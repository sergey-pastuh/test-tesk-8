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
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('parent_id');

            $table->index(['user_id', 'status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('user_id');
            $table->dropIndex('status');
            $table->dropIndex('priority');
            $table->dropIndex('parent_id');

            $table->dropIndex(['user_id', 'status', 'priority']);
        });
    }
};
