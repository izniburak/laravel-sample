<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id');
            $table->unsignedTinyInteger('home_score');
            $table->unsignedTinyInteger('away_score');
            $table->unsignedTinyInteger('home_red_card');
            $table->unsignedTinyInteger('away_red_card');
            $table->unsignedTinyInteger('home_yellow_card');
            $table->unsignedTinyInteger('away_yellow_card');
            $table->unsignedTinyInteger('home_corner');
            $table->unsignedTinyInteger('away_corner');
            $table->unsignedTinyInteger('home_faul');
            $table->unsignedTinyInteger('away_faul');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_stats');
    }
}
