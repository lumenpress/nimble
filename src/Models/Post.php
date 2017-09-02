<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Concerns\RegisterTypes;
use Lumenpress\ORM\Concerns\HasPostPaginationAttributes;

class Post extends AbstractPost
{
    use HasPostPaginationAttributes, RegisterTypes;

    protected static $registeredTypes = [
        'post' => Post::class,
        'page' => Page::class
    ];

    protected $with = ['meta'];

    protected $dates = [
        'post_date', 
        'post_date_gmt', 
        'post_modified', 
        'post_modified_gmt'
    ];

    protected $hidden = [
        'ID',
        'post_title',
        'post_name',
        'post_excerpt',
        'post_content',
        'post_parent',
        'post_status',
        'guid',
        'post_date_gmt',
        'post_date',
        'post_modified',
        'post_modified_gmt',
        'post_author',
        'comment_count',
        'post_mime_type',
        'post_type',
        'ping_status',
        'comment_status',
        'post_password',
        'pinged',
        'to_ping',
        'post_content_filtered'
    ];

    protected $aliases = [
        'id' => 'ID',
        'title' => 'post_title',
        'slug' => 'post_name',
        'excerpt' => 'post_excerpt',
        'content' => 'post_content',
        // 'parentId' => 'post_parent',
        'parent_id' => 'post_parent',
        'status' => 'post_status',
        'type' => 'post_type',
        // 'link' => 'guid',
        'date_gmt' => 'post_date_gmt',
        'date' => 'post_date',
        'modified' => 'post_modified',
        'modified_gmt' => 'post_modified_gmt',
        // 'authorId' => 'post_author',
        'author_id' => 'post_author',
        'mimeType' => 'post_mime_type',
        'mime_type' => 'post_mime_type',
        'password' => 'post_password',
        // 'commentCount' => 'comment_count',
        // 'pingStatus' => 'ping_status',
        // 'commentStatus' => 'comment_status',
        // 'pinged' => 'pinged',
        // 'toPing' => 'to_ping',
        'content_filtered' => 'post_content_filtered',
        'template' => 'meta._wp_page_template',
    ];

    /**
     * Post belongs to Tax.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax($taxonomy = null)
    {
        $builder = $this->belongsToMany(PostTaxonomy::class, 
            'term_relationships', 'object_id', 'term_taxonomy_id');
        if ($taxonomy) {
            $builder->type($taxonomy);
        }
        return $builder;
    }
}
