<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(config('pinta.tables.workflow_instances'), fn(Blueprint $t) => $t->nullableMorphs('owner_team'));
        Schema::create(config('pinta.tables.activity_assignments'), function (Blueprint $t) {
            $t->id();
            $t->foreignId('activity_instance_id')->constrained(config('pinta.tables.activity_instances'))->cascadeOnDelete();
            $t->morphs('assignee');
            $t->timestamp('assigned_at');
            $t->timestamp('claimed_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->index(['assignee_type', 'assignee_id', 'completed_at'], 'work_assignment_inbox');
        });
        Schema::create(config('pinta.tables.workflow_links'), function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_workflow_instance_id')->constrained(config('pinta.tables.workflow_instances'))->cascadeOnDelete();
            $t->foreignId('child_workflow_instance_id')->constrained(config('pinta.tables.workflow_instances'))->cascadeOnDelete();
            $t->string('trigger_event');
            $t->timestamps();
            $t->unique(['parent_workflow_instance_id', 'child_workflow_instance_id'], 'work_links_unique');
        });
        Schema::create(config('pinta.tables.workflow_outputs'), function (Blueprint $t) {
            $t->id();
            $t->foreignId('workflow_instance_id')->constrained(config('pinta.tables.workflow_instances'))->cascadeOnDelete();
            $t->string('key');
            $t->json('value')->nullable();
            $t->nullableMorphs('record');
            $t->timestamp('produced_at');
            $t->timestamps();
            $t->unique(['workflow_instance_id', 'key']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists(config('pinta.tables.workflow_outputs'));
        Schema::dropIfExists(config('pinta.tables.workflow_links'));
        Schema::dropIfExists(config('pinta.tables.activity_assignments'));
        Schema::table(config('pinta.tables.workflow_instances'), fn(Blueprint $t) => $t->dropMorphs('owner_team'));
    }
};
