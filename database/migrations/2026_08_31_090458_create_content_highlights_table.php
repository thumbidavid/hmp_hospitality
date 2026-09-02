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
        Schema::create('content_highlights', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type'); // 'property' or 'destination'
            $table->unsignedBigInteger('subject_id');
            $table->enum('type', ['reason_to_visit', 'experience_attraction', 'key_experience']);
            $table->string('text', 255);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['subject_type', 'subject_id', 'type'], 'idx_highlight_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_highlights');
    }
};
