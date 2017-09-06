<?php

namespace Lumenpress\ORM\Tests\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Lumenpress\ORM\Tests\database\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('comment_ID');
            $table->bigInteger('comment_post_ID')->unsigned()->default(0);
            $table->string('comment_author');
            $table->string('comment_author_email', 100)->default('');
            $table->string('comment_author_url', 200)->default('');
            $table->string('comment_author_IP', 100)->default('');
            $table->dateTime('comment_date')->default('0000-00-00 00:00:00');
            $table->dateTime('comment_date_gmt')->default('0000-00-00 00:00:00');
            $table->text('comment_content');
            $table->integer('comment_karma')->default(0);
            $table->string('comment_approved', 20)->default(1);
            $table->string('comment_agent')->default('');
            $table->string('comment_type', 20)->default('');
            $table->bigInteger('comment_parent')->unsigned()->default(0);
            $table->bigInteger('user_id')->unsigned()->default(0);
            $table->index('comment_post_ID', 'comment_post_ID');
            $table->index(['comment_approved', 'comment_date_gmt'], 'comment_approved_date_gmt');
            $table->index('comment_date_gmt', 'comment_date_gmt');
            $table->index('comment_parent', 'comment_parent');
            $table->index('comment_author_email', 'comment_author_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
