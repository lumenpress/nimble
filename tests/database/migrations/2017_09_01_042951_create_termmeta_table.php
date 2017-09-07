<?php

namespace Lumenpress\Fluid\Tests\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Lumenpress\Fluid\Tests\database\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateTermmetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('termmeta', function (Blueprint $table) {
            $table->bigIncrements('meta_id');
            $table->bigInteger('term_id')->default(0);
            $table->string('meta_key')->nullable();
            $table->longText('meta_value')->nullable();
            $table->index('term_id', 'term_id');
            $table->index('meta_key', 'meta_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('termmeta');
    }
}
