<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketPluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_plugins', function (Blueprint $table) {
            $table->id();
            $table->string("plugin_name");
            $table->unsignedInteger("author");
            $table->unsignedTinyInteger("type")->default(0);
            $table->unsignedTinyInteger("status")->default(0);
            $table->unsignedInteger("download_times")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_plugins');
    }
}