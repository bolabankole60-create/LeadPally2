<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void {
    Schema::create('search_histories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('team_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->string('keyword')->nullable();
      $table->string('city');
      $table->string('country')->default('Nigeria');
      $table->unsignedInteger('results_count')->default(0);
      $table->timestamps();
    });
    Schema::create('search_results', function (Blueprint $table) {
      $table->id();
      $table->foreignId('search_history_id')->constrained('search_histories')->cascadeOnDelete();
      $table->foreignId('team_id')->constrained()->cascadeOnDelete();
      $table->string('provider')->default('google_places');
      $table->string('provider_id')->nullable();
      $table->string('name');
      $table->string('phone')->nullable();
      $table->string('website')->nullable();
      $table->text('address')->nullable();
      $table->decimal('rating', 3, 2)->nullable();
      $table->unsignedInteger('reviews_count')->default(0);
      $table->timestamps();
    });
    Schema::create('usage_credits', function (Blueprint $table) {
      $table->id();
      $table->foreignId('team_id')->constrained()->cascadeOnDelete();
      $table->string('action');
      $table->unsignedInteger('used')->default(0);
      $table->date('period_date');
      $table->timestamps();
      $table->unique(['team_id','action','period_date']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('usage_credits');
    Schema::dropIfExists('search_results');
    Schema::dropIfExists('search_histories');
  }
};
