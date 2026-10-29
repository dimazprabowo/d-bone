<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_instances', function (Blueprint $table) {
            $table->id();
            $table->morphs('approvable');
            $table->foreignId('approval_flow_id')->nullable()->constrained()->nullOnDelete();
            $table->string('flow_name');
            $table->json('conditions_snapshot')->nullable();
            $table->string('status')->default('in_progress');
            $table->unsignedSmallInteger('current_step')->default(1);
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_instance_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_instance_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('name');
            $table->string('mode')->default('any');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['approval_instance_id', 'position']);
        });

        Schema::create('approval_instance_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_instance_step_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_instance_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approval_instance_step_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['approval_instance_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_instance_actions');
        Schema::dropIfExists('approval_instance_approvers');
        Schema::dropIfExists('approval_instance_steps');
        Schema::dropIfExists('approval_instances');
    }
};
