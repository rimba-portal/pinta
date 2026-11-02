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
        Schema::create('req_offerings', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('org_unit_id')->index()->constrained('org_units');
            $table->foreignId('org_team_id')->nullable()->index()->constrained('org_teams')->nullOnDelete();
            $table->unsignedBigInteger('form_id')->nullable()->index();
            $table->uuid('workflow_id')->nullable()->index();
            $table->string('visibility')->default('internal');
            $table->string('availability')->default('draft');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
        Schema::create('req_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->foreignUuid('offering_id')->constrained('req_offerings');
            $table->foreignId('requested_by')->nullable()->index()->constrained('staff')->nullOnDelete();
            $table->foreignId('org_unit_id')->nullable()->index()->constrained('org_units')->nullOnDelete();
            $table->foreignId('org_team_id')->nullable()->index()->constrained('org_teams')->nullOnDelete();
            $table->string('phase_code')->default('oir.01')->index();
            $table->unsignedBigInteger('form_submission_id')->nullable()->index();
            $table->uuid('workflow_run_id')->nullable()->index();
            $table->nullableMorphs('subject');
            $table->json('context')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('req_requests');
        Schema::dropIfExists('req_offerings');
    }
};
