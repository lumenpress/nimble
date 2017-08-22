<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\PostBuilder;
use Lumenpress\Acf\Concerns\HasAdvancedCustomFields;
use Lumenpress\Acf\Collections\FieldCollection;

class Post extends AbstractPost
{
    use Concerns\HasPostPaginationAttributes, 
        Concerns\HasPostAttributes;

    protected $postType = 'post';

    protected $with = ['meta'];

    protected $appends = [
        'title',
        'slug',
        'content',
        'excerpt',
        'type',
        'status',
        'link',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new PostBuilder($query);
    }

    /**
     * Post belongs to Tax.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax($taxonomy = null)
    {
        $builder = $this->belongsToMany(Taxonomy::class, 
            'term_relationships', 'object_id', 'term_taxonomy_id');
        if ($taxonomy) {
            $builder->type($taxonomy);
        }
        return $builder;
    }

    /**
     * Accessor for template attribute.
     *
     * @return returnType
     */
    public function getTemplateAttribute($value)
    {
        return $this->meta->_wp_page_template;
    }

    /**
     * Mutator for template attribute.
     *
     * @return void
     */
    public function setTemplateAttribute($value)
    {
        if ($value === 'default') {
            unset($this->meta->_wp_page_template);
        } else {
            $this->meta->_wp_page_template = $value;
        }
    }

    public function save(array $options = [])
    {
        if (!parent::save($options)) {
            return false;
        }
        $this->tax->save();
        return true;
    }
}
