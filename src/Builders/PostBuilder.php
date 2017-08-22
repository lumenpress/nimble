<?php 

namespace Lumenpress\ORM\Builders;

use Lumenpress\ORM\Models\Page;

class PostBuilder extends Builder
{
    protected $aliases = [
        'title' => 'post_title',
        'slug' => 'post_name',
        'type' => 'post_type',
        'date' => 'post_date',
        'meta.template' => 'meta._wp_page_template',
    ];

    public function slug($slug)
    {
        return $this->where('post_name', $slug);
    }

    public function path($path)
    {
        $paths = explode('/', $path);
        $parentId = 0;
        $post = null;
        foreach ($paths as $slug) {
            if (!$slug) {
                continue;
            }
            $query = clone $this;
            $post = $query->parent($parentId)->slug($slug)->first();
            $parentId = isset($post->id) ? $post->id : 0;
        }
        return $post;
    }

    public function type($type)
    {
        if (is_array($type)) {
            return $this->whereIn('post_type', $type);
        }
        return $this->where('post_type', $type);
    }

    public function status($status)
    {
        if (is_array($status)) {
            return $this->whereIn('post_status', $status);
        }
        return $this->where('post_status', $status);
    }

    public function parent($parentId)
    {
        return $this->where('post_parent', $parentId);
    }

    /**
     * [whereMeta description]
     *
     * $builder->whereMeta('key', 'value');
     * 
     * $builder->whereMeta('key', 'op', 'value');
     * 
     * $builder->whereMeta('key', 'in', ['value1', 'value2']);
     *
     * $builder->whereMeta(
     *     [$operator = null, $value = null, $boolean = 'and'], 
     *     [$operator = null, $value = null, $boolean = 'and']
     * );
     *
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function whereMeta($key, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_callable($key)) {
            return $this->has('meta', '>=', 1, $boolean, $key);
        }
        if (is_null($value)) {
            $value = $operator;
        }
        return $this->has('meta', '>=', 1, $boolean, 
            function ($query) use ($key, $value)
            {
                $query->whereKeyValue($key, $value);
            });
    }

    public function orWhereMeta($key, $operator = null, $value = null)
    {
        return $this->whereMeta($key, $operator, $value, 'or');
    }

    /**
     * [whereTax description]
     *
     * $buider->whereTax('tag', 12);
     * 
     * $buider->whereTax('tag', 'slug');
     *
     * $buider->whereTax('tag', [
     *     'term_id' => 12,
     *     'slug' => 'slug',
     *     'meta.value' => 'abc',
     * ]);
     *
     * $buider->whereTax('tag', [
     *     'slug' => ['slug1', 'slug2'],
     *     ['slug', 'like', '%slug%']
     * ]);
     * 
     * @param  [type] $taxonomy [description]
     * @param  [type] $value    [description]
     * @param  string $field    [description]
     * @return [type]           [description]
     */
    public function whereTax($taxonomy, $value = null, $boolean = 'and')
    {
        if (is_callable($taxonomy)) {
            return $this->has('taxonomies', '>=', 1, $boolean, $taxonomy);
        }
        return $this->has('taxonomies', '>=', 1, $boolean, 
            function($query) use ($taxonomy, $value)
            {
                $query->whereTerm($taxonomy, $value);
            });
    }

    public function orWhereTax($taxonomy, $value = null)
    {
        return $this->whereTax($taxonomy, $value, 'or');
    }

    /**
     * [orderBy description]
     *
     * $buidler->orderBy('column', 'asc')
     *
     * $buidler->orderBy('meta.column', 'asc')
     * 
     * @param  [type] $column [description]
     * @param  string $order  [description]
     * @return [type]         [description]
     */
    public function orderBy($column, $order = 'asc')
    {
        if (isset($this->aliases[$column])) {
            $column = $this->aliases[$column];
        }
        if (stripos($column, 'meta.') !== 0) {
            return parent::orderBy($column, $order);
        }
        $column = str_replace('meta.', '', $column);
        return $this->join('postmeta', function($join) use ($column)
        {
            $join->on('posts.ID', '=', 'postmeta.post_id');
            $join->where('meta_key', $column);
        })
        ->groupBy('posts.ID')
        ->orderBy('postmeta.meta_value', $order);
    }

}
