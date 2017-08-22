<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\PostBuilder;
use Lumenpress\Acf\Concerns\HasAdvancedCustomFields;
use Lumenpress\Acf\Collections\FieldCollection;

class Post extends AbstractPost
{
    use Concerns\HasPostPaginationAttributes;

    protected $postType = 'post';

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
        'link' => 'guid',
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
        'content_filtered' => 'post_content_filtered'
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
