<?php 

namespace Lumenpress\ORM\Models\Concerns;

trait HasPostAttributes
{
    /**
     * Mutator for post_title attribute.
     *
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->post_title = $value;
    }

    /**
     * Accessor for Title attribute.
     *
     * @return returnType
     */
    public function getTitleAttribute($value)
    {
        return $this->post_title;
    }

    /**
     * Mutator for slug attribute.
     *
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->post_name = $value;
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
     * Mutator for content attribute.
     *
     * @return void
     */
    public function setContentAttribute($value)
    {
        $this->post_content = $value;
    }

    /**
     * Accessor for content attribute.
     *
     * @return returnType
     */
    public function getContentAttribute($value)
    {
        return luemnpress_get_the_content($this->post_content);
    }

    /**
     * Mutator for excerpt attribute.
     *
     * @return void
     */
    public function setExcerptAttribute($value)
    {
        $this->attributes['post_excerpt'] = $value;
    }

    /**
     * Accessor for excerpt attribute.
     *
     * @return returnType
     */
    public function getExcerptAttribute($value)
    {
        return is_null($this->post_excerpt) ? '' : $this->post_excerpt;
    }

    /**
     * Mutator for status attribute.
     *
     * @return void
     */
    public function setStatusAttribute($value)
    {
        $this->post_status = $value;
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
     * Mutator for type attribute.
     *
     * @return void
     */
    public function setTypeAttribute($value)
    {
        $this->post_type = $value;
    }

    /**
     * Accessor for type attribute.
     *
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return $this->post_type;
    }

    /**
     * Mutator for parentId attribute.
     *
     * @return void
     */
    public function setParentIdAttribute($value)
    {
        $this->post_parent = $value;
    }

    /**
     * Accessor for parentId attribute.
     *
     * @return returnType
     */
    public function getParentIdAttribute($value)
    {
        return $this->post_parent;
    }

    /**
     * Mutator for link attribute.
     *
     * @return void
     */
    public function setLinkAttribute($value)
    {
        $this->attributes['guid'] = $value;
    }

    /**
     * Accessor for link attribute.
     *
     * @return returnType
     */
    public function getLinkAttribute($value)
    {
        return $this->id !== 0 ? lumenpress_get_permalink($this->id) 
            : url(($this->type === 'page' ? '' : $this->type).'/'.$this->slug);
    }

}