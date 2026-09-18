<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('pinta.tables.workflow_instances'), function (Blueprint $t): void {
            $t->id();
            $t->string('definition_slug');
            $t->unsignedInteger('definition_version')->default(1);
            $t->nullableMorphs('subject');
            $t->nullableMorphs('initiator');
            $t->string('current_state');
            $t->string('status')->index();
            $t->json('context')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
        });
        Schema::create(config('pinta.tables.activity_instances'), function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_instance_id')->constrained(config('pinta.tables.workflow_instances'))->cascadeOnDelete();
            $t->string('definition_slug');
            $t->nullableMorphs('assignee');
            $t->string('status')->index();
            $t->json('payload')->nullable();
            $t->timestamp('due_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
        });
        Schema::create(config('pinta.tables.transitions'), function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_instance_id')->constrained(config('pinta.tables.workflow_instances'))->cascadeOnDelete();
            $t->string('from_state');
            $t->string('to_state');
            $t->string('action');
            $t->nullableMorphs('actor');
            $t->json('payload')->nullable();
            $t->timestamp('performed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('pinta.tables.transitions'));
        Schema::dropIfExists(config('pinta.tables.activity_instances'));
        Schema::dropIfExists(config('pinta.tables.workflow_instances'));
    }
};
