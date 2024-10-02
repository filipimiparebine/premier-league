<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->nullable(false)->constrained()->onDelete('cascade');
            $table->integer('week_number');
            $table->foreignId('home_team_id')->nullable(false)->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->nullable(false)->constrained('teams')->onDelete('cascade');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weeks');
    }
};
