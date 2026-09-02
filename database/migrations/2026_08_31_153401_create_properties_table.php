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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_category_id')->constrained('portfolio_categories')->onDelete('cascade');
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');

            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->string('tagline', 300)->nullable();
            $table->text('overview')->nullable();

            $table->text('accommodation_details')->nullable();
            $table->text('meetings_facilities_details')->nullable();
            $table->text('dining_leisure_details')->nullable();
            $table->text('sustainability_details')->nullable();

            $table->string('city', 150)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->integer('number_of_rooms')->unsigned()->nullable();
            $table->integer('max_event_capacity')->unsigned()->nullable();

            $table->string('website_url', 500)->nullable();
            $table->string('featured_image_url', 500)->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'portfolio_category_id', 'destination_id', 'country_id'], 'idx_property_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
