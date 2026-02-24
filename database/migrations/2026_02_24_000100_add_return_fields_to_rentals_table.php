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
        Schema::table('rentals', function (Blueprint $table): void {
            if (! Schema::hasColumn('rentals', 'returned_at')) {
                $table->timestamp('returned_at')->nullable();
            }

            if (! Schema::hasColumn('rentals', 'penalty_cents')) {
                $table->integer('penalty_cents')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table): void {
            if (Schema::hasColumn('rentals', 'penalty_cents')) {
                $table->dropColumn('penalty_cents');
            }
        });
    }
};
