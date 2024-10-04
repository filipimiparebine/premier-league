<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('season_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->nullable(false)->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable(false)->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('played_matches')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_leaderboards');
    }
};
