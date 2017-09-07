<?php

namespace Lumenpress\Fluid\Tests\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Lumenpress\Fluid\Tests\database\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->bigIncrements('option_id');
            $table->string('option_name', 191)->default('');
            $table->longText('option_value');
            $table->string('autoload', 20)->default('yes');
            $table->unique('option_name', 'option_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
