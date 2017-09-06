<?php

namespace Lumenpress\ORM\Tests\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Lumenpress\ORM\Tests\database\Schema;

class CreateCommentmetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentmeta', function (Blueprint $table) {
            $table->bigIncrements('meta_id');
            $table->bigInteger('comment_id')->default(0);
            $table->string('meta_key')->nullable();
            $table->longText('meta_value')->nullable();
            $table->index('comment_id', 'comment_id');
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
        Schema::dropIfExists('commentmeta');
    }
}
