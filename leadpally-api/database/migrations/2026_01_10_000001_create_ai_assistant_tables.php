<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->default('AI Conversation');
            $table->string('purpose')->default('sales_assistant');
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->text('prompt');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_prompt_templates');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
