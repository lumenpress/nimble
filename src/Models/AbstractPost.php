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

    protected $_slug;

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
        $builder = $this->hasMany(PostMeta::class, 'post_id');
        if ($key) {
            $builder->where('meta_key', $key);
        }
        return $builder;
    }

    /**
     * Mutator for ID attribute.
     *
     * @return void
     */
    public function setIdAttribute($value)
    {
        $this->attributes['ID'] = $value;
    }

    /**
     * Accessor for ID attribute.
     *
     * @return returnType
     */
    public function getIdAttribute($value)
    {
        return array_get($this->attributes, 'ID', 0);
    }

    /**
     * Mutator for postTitle attribute.
     *
     * @return void
     */
    public function setPostTitleAttribute($value)
    {
        $this->attributes['post_title'] = $value;
        $this->setPostNameAttribute($value);
    }

    /**
     * Accessor for postType attribute.
     *
     * @return returnType
     */
    public function getPostTypeAttribute($value)
    {
        return $value;
    }

    /**
     * Mutator for postType attribute.
     *
     * @return void
     */
    public function setPostTypeAttribute($value)
    {
        $this->attributes['post_type'] = $value;
        if ($this->_slug) {
            $this->setPostNameAttribute($this->_slug);
        }
    }

    /**
     * Accessor for post status attribute.
     *
     * @return returnType
     */
    public function getPostStatusAttribute($value)
    {
        return $value;
    }

    /**
     * Mutator for post status attribute.
     *
     * @return void
     */
    public function setPostStatusAttribute($value)
    {
        $this->attributes['post_status'] = $value;
        if ($this->_slug) {
            $this->setPostNameAttribute($this->_slug);
        }
    }


    /**
     * Accessor for post parent attribute.
     *
     * @return returnType
     */
    public function getPostParentAttribute($value)
    {
        return $value;
    }

    /**
     * Mutator for post parent attribute.
     *
     * @return void
     */
    public function setPostParentAttribute($value)
    {
        $this->attributes['post_parent'] = $value;
        if ($this->_slug) {
            $this->setPostNameAttribute($this->_slug);
        }
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
        $this->_slug = $value;
        $this->attributes['post_name'] = $this->getUniquePostName(
            str_slug($value), 
            $this->id,
            $this->post_status, 
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

   /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        if (method_exists($value, 'setObject')) {
            $value->setObject($this);
        }

        $this->relations[$relation] = $value;

        return $this;
    }

    public function save(array $options = [])
    {
        if (!$this->_slug) {
            $this->setPostNameAttribute($this->post_name ?: $this->title);
        }
        if (!parent::save($options)) {
            return false;
        }
        $this->meta->save();
        return true;
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
