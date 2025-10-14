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
        Schema::create('barangays', function (Blueprint $table) {
            $table->string('barangay_id', 10)->primary();
            $table->string('name', 100);
            $table->string('city', 100);
            $table->string('district', 50)->nullable(); // NEW: District field
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();

            // FIXED: Status enum values match assignment
            $table->enum('disaster_status', ['safe', 'warning', 'critical', 'emergency'])->default('safe');

            // NEW: Contact information fields
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 100)->nullable();

            // NEW: Affected families counter
            $table->integer('affected_families')->default(0);

            $table->text('needs_summary')->nullable();
            $table->string('blockchain_address', 50)->unique()->nullable();
            $table->timestamps();
        });

        // Add foreign key constraint to users table after barangays table is created
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
        });
        Schema::dropIfExists('barangays');
    }
};
