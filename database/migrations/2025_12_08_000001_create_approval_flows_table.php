<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('entity_type');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('priority')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['entity_type', 'is_active']);
        });

        Schema::create('approval_flow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('name');
            $table->string('mode')->default('any');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['approval_flow_id', 'position']);
        });

        Schema::create('approval_flow_step_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_step_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['approval_flow_step_id', 'user_id'], 'step_approver_unique');
        });

        Schema::create('approval_flow_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_id')->constrained()->cascadeOnDelete();
            $table->string('field');
            $table->string('operator');
            $table->string('value');
            $table->string('value2')->nullable();
            $table->timestamps();

            $table->index('approval_flow_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_flow_step_approvers');
        Schema::dropIfExists('approval_flow_steps');
        Schema::dropIfExists('approval_flow_conditions');
        Schema::dropIfExists('approval_flows');
    }
};
