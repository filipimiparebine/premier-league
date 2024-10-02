<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn([
                'year_period',
                'team_id',
                'points',
                'played_matches',
                'won',
                'drawn',
                'lost',
                'goal_difference'
            ]);
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn([
                'name'
            ]);
            $table->string('year_period');
            $table->foreignId('team_id')->nullable(false)->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('played_matches')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('goal_difference')->default(0);
        });
    }
};
