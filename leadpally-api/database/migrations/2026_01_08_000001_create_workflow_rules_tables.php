<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workflow_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('event_name');
            $table->json('criteria')->nullable();
            $table->json('steps');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('event_name');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_runs');
        Schema::dropIfExists('workflow_rules');
    }
};
