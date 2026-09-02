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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type'); // 'property' or 'destination'
            $table->unsignedBigInteger('subject_id');
            $table->string('label', 150); // e.g. "Fact Sheet", "Destination Guide"
            $table->string('file_url', 500);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['subject_type', 'subject_id'], 'idx_document_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
