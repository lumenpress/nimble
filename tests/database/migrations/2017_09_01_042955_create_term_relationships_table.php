<?php

namespace Lumenpress\Fluid\Tests\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Lumenpress\Fluid\Tests\database\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateTermRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_relationships', function (Blueprint $table) {
            $table->bigInteger('object_id')->unsigned()->default(0);
            $table->bigInteger('term_taxonomy_id')->unsigned()->default(0);
            $table->integer('term_order')->default(0);
            $table->primary(['object_id', 'term_taxonomy_id']);
            $table->index('term_taxonomy_id', 'term_taxonomy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_relationships');
    }
}
