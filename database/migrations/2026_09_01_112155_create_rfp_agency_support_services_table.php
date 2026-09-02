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
        Schema::create('rfp_agency_support_services', function (Blueprint $table) {
            $table->foreignId('rfp_id')->constrained('rfp_submissions')->onDelete('cascade');
            $table->foreignId('agency_support_service_id')->constrained('agency_support_services')->onDelete('cascade');
            $table->primary(['rfp_id', 'agency_support_service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfp_agency_support_services');
    }
};
