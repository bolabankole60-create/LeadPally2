<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedInteger('score')->default(0)->after('status');
            $table->string('temperature')->default('cold')->after('score');
        });

        Schema::create('lead_score_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('field');
            $table->string('operator')->default('exists');
            $table->string('value')->nullable();
            $table->integer('points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lead_score_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('old_score')->default(0);
            $table->unsignedInteger('new_score')->default(0);
            $table->string('old_temperature')->nullable();
            $table->string('new_temperature')->nullable();
            $table->json('matched_rules')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_score_histories');
        Schema::dropIfExists('lead_score_rules');

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['score', 'temperature']);
        });
    }
};
