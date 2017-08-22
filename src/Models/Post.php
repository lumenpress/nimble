<?php 

namespace Lumenpress\ORM;

use Lumenpress\ORM\Builders\PostBuilder;
use Lumenpress\Acf\Concerns\HasAdvancedCustomFields;
use Lumenpress\Acf\Collections\FieldCollection;

class Post extends Model
{
    use HasAdvancedCustomFields;

    const CREATED_AT = 'post_date';

    const UPDATED_AT = 'post_modified';

    protected static $postTaxonomy = [];

    public static $postTypes = [
        'post' => Post::class,
        'page' => Page::class
    ];

    /**
     * [$table description]
     * @var string
     */
    protected $table = 'posts';

    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'ID';

    protected $foreignKey = 'post_id';

    /**
     * [$dates description]
     * @var [type]
     */
    protected $dates = [
        'post_date', 
        'post_date_gmt', 
        'post_modified', 
        'post_modified_gmt'
    ];

    /**
     * [$with description]
     * @var array
     */
    protected $with = [
        // 'tax', 
        // 'meta', 
        // 'acf'
    ];

    /**
     * [$appends description]
     * @var [type]
     */
    protected $appends = [
        'title',
        'slug',
        'content',
        'excerpt',
        'type',
        'link',
        // 'template',
        'date',
    ];

    protected $fillable = [
        'post_title',
        'post_name',
        'post_content',
        'post_excerpt',
        'post_type',
        'post_parent',
        'post_author',
        'to_ping',
        'pinged',
        'post_content_filtered',
    ];

    protected $hidden = [
        'post_title',
        'post_content',
        'post_name',
        'post_excerpt',
        // 'post_type',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->post_author = (int) lumenpress_get_current_user_id();
        $this->post_parent = 0;
        $this->id = 0;
        $this->post_status = 'publish';
        $this->post_type = isset($this->postType) ? $this->postType : 'post';
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $builder = new PostBuilder($query);

        if (property_exists($this, 'postType')) {
            $builder->type($this->postType);
        }

        return $builder;
    }

    /**
     * Meta relationship.
     *
     * @return Lumenpress\ORM\PostMetaCollection
     */
    public function meta($key = null)
    {
        $query = $this->hasMany(PostMeta::class, $this->foreignKey);
        if ($key) {
            $query->where('meta_key', $key);
        }
        return $query;
    }

    /**
     * Taxonomy relationship.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tax($taxonomy = null)
    {
        $query = $this->belongsToMany($this->getTaxonomyClassByPost(), 
            'term_relationships', 'object_id', 'term_taxonomy_id');
        if ($taxonomy) {
            $query->type($taxonomy);
        }
        return $query;
    }

    /**
     * Parent post.
     *
     * @return Post
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'post_parent');
    }

    public function getTaxonomyClassByPost()
    {
        if (isset(static::$postTaxonomy[$this->type])) {
            return static::$postTaxonomy[$this->type];
        }
        return Taxonomy::class;
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Mutator for parent attribute.
     *
     * @return void
     */
    public function setParentAttribute($value)
    {
        $this->attributes['post_parent'] = $value;
    }

    /**
     * Accessor for id attribute.
     *
     * @return returnType
     */
    public function getIdAttribute($value)
    {
        return isset($this->attributes['ID']) ? $this->attributes['ID'] : null;
    }

    /**
     * Mutator for Id attribute.
     *
     * @return void
     */
    public function setIdAttribute($value)
    {
        $this->attributes['ID'] = $value;
    }

    /**
     * Accessor for title attribute.
     *
     * @return returnType
     */
    public function getTitleAttribute($value)
    {
        return $this->post_title;
    }

    /**
     * Mutator for title attribute.
     *
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['post_title'] = $value;
    }

    /**
     * Accessor for content attribute.
     *
     * @return returnType
     */
    public function getContentAttribute($value)
    {
        return apply_filters( 'the_content', $this->post_content );
    }

    /**
     * Mutator for content attribute.
     *
     * @return void
     */
    public function setContentAttribute($value)
    {
        $this->attributes['post_content'] = $value;
    }

    /**
     * Accessor for slug attribute.
     *
     * @return returnType
     */
    public function getSlugAttribute($value)
    {
        return $this->post_name;
    }

    /**
     * Mutator for slug attribute.
     *
     * @return void
     */
    public function setSlugAttribute($value)
    {
        if (!$this->type) {
            throw new \Exception("The post_type variable is not declared", 1);
        }
        $this->attributes['post_name'] = $this->getUniquePostSlug(
            str_slug($value), 
            $this->id, 
            $this->status, 
            $this->type, 
            $this->post_parent
        );
    }

    public function getUniquePostSlug($slug, $id = 0, $status = 'publish', $type = 'post', $parent = 0)
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
     * Accessor for excerpt attribute.
     *
     * @return returnType
     */
    public function getExcerptAttribute($value)
    {
        return $this->post_excerpt;
    }

    /**
     * Accessor for Type attribute.
     *
     * @return returnType
     */
    public function getTypeAttribute($value)
    {
        return $this->post_type;
    }

    /**
     * Mutator for type attribute.
     *
     * @return void
     */
    public function setTypeAttribute($value)
    {
        $this->attributes['post_type'] = $value;
    }

    /**
     * Accessor for status attribute.
     *
     * @return returnType
     */
    public function getStatusAttribute($value)
    {
        return $this->post_status;
    }

    /**
     * Mutator for status attribute.
     *
     * @return void
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['post_status'] = $value;
    }

    /**
     * Accessor for date attribute.
     *
     * @return returnType
     */
    public function getDateAttribute($value)
    {
        return $this->post_date;
    }

    /**
     * Mutator for postDate attribute.
     *
     * @return void
     */
    public function setDateAttribute($value)
    {
        $this->attributes['post_date'] = $value;
    }

    /**
     * Accessor for page_template attribute.
     *
     * @return returnType
     */
    public function getTemplateAttribute($value)
    {
        return $this->meta->_wp_page_template ?: 'default';
    }

    /**
     * Mutator for page_template attribute.
     *
     * @return void
     */
    public function setTemplateAttribute($value)
    {
        $this->meta->_wp_page_template = $value;
    }

    /**
     * Accessor for link attribute.
     *
     * @return returnType
     */
    public function getLinkAttribute($value)
    {
        return get_permalink($this->id);
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
        if ($relation === 'acf') {
            if ($value instanceof FieldCollection) {
                $value->setFields($this->getAcfFieldObjects());
            }
        }
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Accessor for prev post attribute.
     *
     * @return returnType
     */
    public function getPreviousAttribute($value)
    {
        return static::where('post_type', 'post')
            ->where('post_status', 'publish')
            // ->where('ID', '>', 23)
            ->where('post_date', '<', (string)$this->post_date)
            ->orderBy('menu_order', 'asc')
            ->orderBy('post_date', 'desc')
            ->orderBy('ID', 'desc')
            ->first();
    }

    /**
     * Accessor for prev post attribute.
     *
     * @return returnType
     */
    public function getNextAttribute($value)
    {
        return static::where('post_type', 'post')
            ->where('post_status', 'publish')
            // ->where('ID', '>', 23)
            ->where('post_date', '>', (string)$this->post_date)
            ->orderBy('menu_order', 'desc')
            ->orderBy('post_date', 'asc')
            ->orderBy('ID', 'asc')
            ->first();
    }

    public function save(array $options = array())
    {
        $this->slug = $this->getUniquePostSlug(
            isset($this->slug) ? $this->slug : $this->title, 
            $this->id, $this->status, $this->type, $this->post_parent
        );

        if (!parent::save($options)) {
            return false;
        }

        if (!$this->post_date_gmt) {
            $this->post_date_gmt = $this->post_date->tz('UTC');
        }

        $this->post_modified_gmt = $this->post_modified->tz('UTC');
        $this->guid = $this->link;

        if ($this->acf->isNeedSave()) {
            $this->acf->save($this);
        }

        if ($this->meta->isNeedSave()) {
            $this->meta->save($this);
        }

        if ($this->tax->isNeedSave()) {
            $this->tax->save($this);
        }

        return parent::save();
    }

    public function __toString()
    {
        return $this->title ?: '';
    }

    public static function registerType($type, $class)
    {
        static::$postTypes[$type] = $class;
    }

    public static function getPostClassByType($type)
    {
        return array_get(static::$postTypes, $type, Post::class);
    }
}
