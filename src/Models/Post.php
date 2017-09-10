<?php

namespace LumenPress\Nimble\Models;

use LumenPress\Nimble\Concerns\RegisterTypes;
use LumenPress\Nimble\Concerns\HasPostPaginationAttributes;

class Post extends AbstractPost
{
    use HasPostPaginationAttributes, RegisterTypes;

    protected static $registeredTypes = [
        'post' => self::class,
        'page' => Page::class,
    ];

    protected $with = ['meta'];

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'post_type',
    ];

    protected $dates = [
        'post_date',
        'post_date_gmt',
        'post_modified',
        'post_modified_gmt',
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
        'post_content_filtered',
    ];

    protected $aliases = [
        'id'               => 'ID',
        'title'            => 'post_title',
        'slug'             => 'post_name',
        'excerpt'          => 'post_excerpt',
        'content'          => 'post_content',
        'parent_id'        => 'post_parent',
        'status'           => 'post_status',
        'type'             => 'post_type',
        'created_at_gmt'   => 'post_date_gmt',
        'created_at'       => 'post_date',
        'updated_at'       => 'post_modified',
        'updated_at_gmt'   => 'post_modified_gmt',
        'author_id'        => 'post_author',
        'mimeType'         => 'post_mime_type',
        'mime_type'        => 'post_mime_type',
        'password'         => 'post_password',
        'content_filtered' => 'post_content_filtered',
        'template'         => 'meta._wp_page_template',
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

    /**
     * Create a new model instance that is existing.
     *
     * @param array       $attributes
     * @param string|null $connection
     *
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;

        if (isset($attributes['post_type'])) {
            $model = $this->newInstance(['post_type' => $attributes['post_type']], true);
        } else {
            $model = $this->newInstance([], true);
        }

        $model->setRawAttributes($attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        return $model;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $attributes = (array) $attributes;

        $postType = isset($attributes['post_type']) ? $attributes['post_type'] : 'post';
        $class = static::getClassNameByType($postType, static::class);

        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $class($attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
    }
}
