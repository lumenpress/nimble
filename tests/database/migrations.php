<?php 

use Illuminate\Database\Schema\Blueprint;
use Lumenpress\ORM\Tests\Database\Schema;

Schema::dropIfExists('comments');
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

Schema::dropIfExists('commentmeta');
Schema::create('commentmeta', function (Blueprint $table) {
    $table->bigIncrements('meta_id');
    $table->bigInteger('comment_id')->default(0);
    $table->string('meta_key')->nullable();
    $table->longText('meta_value')->nullable();
    $table->index('comment_id', 'comment_id');
    $table->index('meta_key', 'meta_key');
});

Schema::dropIfExists('options');
Schema::create('options', function (Blueprint $table) {
    $table->bigIncrements('option_id');
    $table->string('option_name', 191)->default('');
    $table->longText('option_value');
    $table->string('autoload', 20)->default('yes');
    $table->unique('option_name', 'option_name');
});

Schema::dropIfExists('posts');
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('ID');
    $table->bigInteger('post_author')->unsigned()->default(0);
    $table->dateTime('post_date')->default('0000-00-00 00:00:00');
    $table->dateTime('post_date_gmt')->default('0000-00-00 00:00:00');
    $table->longText('post_content');
    $table->text('post_title');
    $table->text('post_excerpt');
    $table->string('post_status', 20)->default('publish');
    $table->string('comment_status', 20)->default('open');
    $table->string('ping_status', 20)->default('open');
    $table->string('post_password')->default('');
    $table->string('post_name', 200)->default('');
    $table->text('to_ping');
    $table->text('pinged');
    $table->dateTime('post_modified')->default('0000-00-00 00:00:00');
    $table->dateTime('post_modified_gmt')->default('0000-00-00 00:00:00');
    $table->longText('post_content_filtered');
    $table->bigInteger('post_parent')->unsigned()->default(0);
    $table->string('guid')->default('');
    $table->integer('menu_order')->default(0);
    $table->string('post_type', 20)->default('post');
    $table->string('post_mime_type', 100)->default('');
    $table->bigInteger('comment_count')->default(0);
    $table->index('post_name', 'post_name');
    $table->index(['post_type', 'post_status', 'post_date', 'ID'], 'type_status_date');
    $table->index('post_parent', 'post_parent');
    $table->index('post_author', 'post_author');
});

Schema::dropIfExists('postmeta');
Schema::create('postmeta', function (Blueprint $table) {
    $table->bigIncrements('meta_id');
    $table->bigInteger('post_id')->default(0);
    $table->string('meta_key')->nullable();
    $table->longText('meta_value')->nullable();
    $table->index('post_id', 'post_id');
    $table->index('meta_key', 'meta_key');
});

Schema::dropIfExists('terms');
Schema::create('terms', function (Blueprint $table) {
    $table->bigIncrements('term_id');
    $table->string('name', 200)->default('');
    $table->string('slug', 200)->default('');
    $table->bigInteger('term_group')->default(0);
    $table->index('name', 'name');
    $table->index('slug', 'slug');
});

Schema::dropIfExists('termmeta');
Schema::create('termmeta', function (Blueprint $table) {
    $table->bigIncrements('meta_id');
    $table->bigInteger('term_id')->default(0);
    $table->string('meta_key')->nullable();
    $table->longText('meta_value')->nullable();
    $table->index('term_id', 'term_id');
    $table->index('meta_key', 'meta_key');
});

Schema::dropIfExists('term_relationships');
Schema::create('term_relationships', function (Blueprint $table) {
    $table->bigInteger('object_id')->unsigned()->default(0);
    $table->bigInteger('term_taxonomy_id')->unsigned()->default(0);
    $table->integer('term_order')->default(0);
    $table->primary(['object_id', 'term_taxonomy_id']);
    $table->index('term_taxonomy_id', 'term_taxonomy_id');
});

Schema::dropIfExists('term_taxonomy');
Schema::create('term_taxonomy', function (Blueprint $table) {
    $table->bigIncrements('term_taxonomy_id');
    $table->bigInteger('term_id')->unsigned()->default(0);
    $table->string('taxonomy', 32)->default('');
    $table->longText('description');
    $table->bigInteger('parent')->unsigned()->default(0);
    $table->bigInteger('count')->default(0);
    $table->index(['term_id', 'taxonomy'], 'term_id_taxonomy');
    $table->index('taxonomy', 'taxonomy');
});

Schema::dropIfExists('usermeta');
Schema::create('usermeta', function (Blueprint $table) {
    $table->bigIncrements('meta_id');
    $table->bigInteger('user_id')->default(0);
    $table->string('meta_key')->nullable();
    $table->longText('meta_value')->nullable();
    $table->index('user_id', 'user_id');
    $table->index('meta_key', 'meta_key');
});

Schema::dropIfExists('users');
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('ID');
    $table->string('user_login', 60)->default('');
    $table->string('user_pass')->default('');
    $table->string('user_nicename', 50)->default('');
    $table->string('user_email', 100)->default('');
    $table->string('user_url', 100)->default('');
    $table->dateTime('user_registered')->default('0000-00-00 00:00:00');
    $table->string('user_activation_key')->default('');
    $table->integer('user_status')->default(0);
    $table->string('display_name')->default('');
    $table->index('user_login', 'user_login_key');
    $table->index('user_nicename', 'user_nicename');
    $table->index('user_email', 'user_email');
});
