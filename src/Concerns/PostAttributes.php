<?php

namespace LumenPress\Nimble\Concerns;

trait PostAttributes
{
    protected $_slug;

    /**
     * Mutator for postTitle attribute.
     *
     * @return void
     */
    public function setPostTitleAttribute($value)
    {
        $this->attributes['post_title'] = $value;
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
     * Mutator for post name attribute.
     *
     * @return void
     */
    public function setPostNameAttribute($value)
    {
        $this->_slug = $value;
        $this->attributes['post_name'] = $this->getUniquePostName(
            str_slug($value),
            $this->ID,
            $this->post_status,
            $this->post_type,
            $this->post_parent
        );
    }

    /**
     * Accessor for post content attribute.
     *
     * @return returnType
     */
    public function getPostContentAttribute($value)
    {
        return luemnpress_get_the_content($value);
    }

    /**
     * Accessor for post_date_gmt attribute.
     *
     * @return returnType
     */
    public function getPostDateGmtAttribute($value)
    {
        return $this->post_date->timezone('UTC');
    }

    /**
     * Accessor for post_modified_gmt attribute.
     *
     * @return returnType
     */
    public function getPostModifiedGmtAttribute($value)
    {
        return $this->post_modified->timezone('UTC');
    }

    /**
     * Accessor for link attribute.
     *
     * @return returnType
     */
    public function getLinkAttribute($value)
    {
        if (function_exists('get_permalink')) {
            return get_permalink($this->ID);
        }

        return sprintf('%s/%s/%s',
            getenv('APP_ENV') === 'testing' ? getenv('APP_SITEURL') : url(''),
            $this->post_type == 'page' ? '' : $this->post_type,
            $this->post_name);
    }

    protected function getUniquePostName($slug, $id = 0, $status = 'publish', $type = 'post', $parent = 0)
    {
        $i = 1;
        $tmp = $slug;
        while (static::where('post_type', $type)
                ->where('ID', '!=', $id)
                ->where('post_parent', $parent)
                ->where('post_status', $status)
                ->where('post_name', $slug)->count() > 0) {
            $slug = $tmp.'-'.(++$i);
        }

        return $slug;
    }

    protected function getGuessGuid()
    {
        if (! $this->ID) {
            return '';
        }
        if (getenv('APP_ENV') === 'testing') {
            $url = getenv('APP_SITEURL');
        } else {
            $url = url('');
        }
        switch ($this->post_type) {
            case 'page':
                return $url.'?page_id='.$this->ID;
            case 'post':
                return $url.'?p='.$this->ID;
            default:
                return $url.'?post_type='.$this->post_type.'&name='.$this->post_name;
        }
    }
}
