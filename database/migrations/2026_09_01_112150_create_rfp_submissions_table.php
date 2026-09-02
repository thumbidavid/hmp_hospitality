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
        Schema::create('rfp_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 30)->unique();

            // Step 1: Buyer profile details
            $table->string('full_name', 150);
            $table->string('job_title', 150)->nullable();
            $table->string('company_name', 200)->nullable();
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->foreignId('buyer_country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('buyer_type_id')->nullable()->constrained('buyer_types')->onDelete('set null');
            $table->enum('preferred_communication_method', ['email', 'telephone', 'video_call', 'whatsapp'])->nullable();

            // Step 2: Requirement type
            $table->enum('requirement_type', [
                'business_travel',
                'group_accommodation',
                'conference_or_meeting',
                'incentive_programme',
                'association_event',
                'government_ngo_programme',
                'leisure_group',
                'long_stay_accommodation',
                'venue_only_event',
                'destination_enquiry'
            ])->nullable();

            // Step 3: Programme specifications
            $table->string('programme_name', 200)->nullable();
            $table->string('preferred_destination', 200)->nullable();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->onDelete('set null');
            $table->boolean('is_destination_flexible')->default(false);

            $table->date('arrival_date')->nullable();
            $table->date('departure_date')->nullable();
            $table->boolean('is_dates_flexible')->default(false);

            $table->integer('number_of_attendees')->unsigned()->nullable();
            $table->integer('number_of_rooms')->unsigned()->nullable();
            $table->integer('number_of_room_nights')->unsigned()->nullable();

            $table->text('meeting_room_requirements')->nullable();
            $table->integer('venue_capacity')->unsigned()->nullable();
            $table->text('fnb_requirements')->nullable();
            $table->text('transfer_airport_requirements')->nullable();

            $table->decimal('budget_amount', 12, 2)->nullable();
            $table->enum('currency', ['USD', 'KES', 'ZAR', 'EUR', 'GBP', 'NGN', 'MAD'])->default('USD');

            $table->text('accessibility_requirements')->nullable();
            $table->text('sustainability_requirements')->nullable();
            $table->text('additional_requirements')->nullable();

            $table->date('proposal_deadline')->nullable();
            $table->date('decision_date')->nullable();
            $table->string('attachment_url', 500)->nullable(); // URL linked via uploaded brief ID

            // Consent, Status & Tracking
            $table->boolean('privacy_policy_accepted')->default(false);
            $table->enum('status', ['new', 'contacted', 'quoted', 'won', 'lost'])->default('new');
            $table->string('source', 100)->default('website');

            $table->timestamps();

            $table->index('status', 'idx_rfp_status');
            $table->index('created_at', 'idx_rfp_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfp_submissions');
    }
};
