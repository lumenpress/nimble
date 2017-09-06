<?php

namespace Lumenpress\ORM\Tests\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Lumenpress\ORM\Tests\database\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->bigIncrements('term_id');
            $table->string('name', 200)->default('');
            $table->string('slug', 200)->default('');
            $table->bigInteger('term_group')->default(0);
            $table->index('name', 'name');
            $table->index('slug', 'slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
