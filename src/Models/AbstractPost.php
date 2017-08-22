<?php

namespace Lumenpress\ORM\Models;

abstract class AbstractPost extends Model
{
    protected static $registeredPostTypes = [
        'post' => Post::class,
        'page' => Page::class
    ];

    const CREATED_AT = 'post_date';

    const UPDATED_AT = 'post_modified';

    protected $table = 'posts';

    protected $primaryKey = 'ID';

    protected $foreignKey = 'post_id';

    protected $dates = [
        'post_date', 
        'post_date_gmt', 
        'post_modified', 
        'post_modified_gmt'
    ];

    protected $hidden = [
        // 'post_title',
        // 'post_name',
        // 'post_excerpt',
        // 'post_content',
        // 'post_parent',
        // 'post_status',
        // 'guid',
        // 'post_date_gmt',
        // 'post_date',
        // 'post_modified',
        // 'post_modified_gmt',
        // 'post_author',
        // 'comment_count',
        // 'post_mime_type',
        // 'post_type',
        // 'ping_status',
        // 'comment_status',
        // 'post_password',
        // 'pinged',
        // 'to_ping',
        // 'post_content_filtered'
    ];

    public function __construct(array $attributes = [])
    {
        $this->append('id');

        parent::__construct($attributes);

        $this->id = 0;
        $this->post_title = 'Untitle';
        $this->post_parent = 0;
        $this->menu_order = 0;
        $this->post_status = 'publish';
        $this->comment_status = 'closed';
        $this->post_author = (int) lumenpress_get_current_user_id();
        $this->post_type = property_exists($this, 'postType') ? $this->postType : 'post';
    }

    public function meta($key = null)
    {
        $query = $this->hasMany(PostMeta::class, 'post_id');
        if ($key) {
            $query->where('meta_key', $key);
        }
        return $query;
    }

    /**
     * Mutator for post_title attribute.
     *
     * @return void
     */
    public function setIdAttribute($value)
    {
        $this->attributes['ID'] = $value;
    }

    /**
     * Accessor for Title attribute.
     *
     * @return returnType
     */
    public function getIdAttribute($value)
    {
        return array_get($this->attributes, 'ID', 0);
    }

    /**
     * Accessor for post name attribute.
     *
     * @return returnType
     */
    public function getPostNameAttribute($value)
    {
        return is_null($value) ? '' : $value;
    }

    /**
     * Mutator for post name attribute.
     *
     * @return void
     */
    public function setPostNameAttribute($value)
    {
        $this->attributes['post_name'] = $this->getUniquePostName(
            str_slug($value), 
            $this->id,
            $this->status, 
            $this->post_type, 
            $this->post_parent
        );
    }

    public function getUniquePostName($slug, $id = 0, $status = 'publish', $type = 'post', $parent = 0)
    {
        $i = 1;
        $tmp = $slug;
        while (static::where('post_type', $type)
            ->where('ID', '!=', $id)
            ->where('post_parent', $parent)
            ->where('post_status', $status)
            ->where('post_name', $slug)->count() > 0) {
            $slug = $tmp . '-' . (++$i);
        }
        return $slug;
    }

    public function save(array $options = [])
    {
        if (empty($this->post_name)) {
            $this->setPostNameAttribute($this->title);
        }
        return parent::save($options);
    }

    public static function registerType($type, $class)
    {
        static::$registeredPostTypes[$type] = $class;
    }

    public static function getPostClassByType($type)
    {
        return array_get(static::$registeredPostTypes, $type, Post::class);
    }
}
