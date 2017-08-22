<?php 

namespace Lumenpress\ORM\Builders;

use Illuminate\Support\Facades\Schema;

class TermBuilder extends Builder
{

    protected $aliases = [
        'tag' => 'post_tag',
        'id' => 'term_id',
        'order' => 'term_order',
        'group' => 'term_group',
        'taxonomy_id' => 'term_taxonomy_id'
    ];

    public function taxonomy($taxonomy)
    {
        if (isset($this->aliases[$taxonomy])) {
            $taxonomy = $this->aliases[$taxonomy];
        }
        $this->whereHas('tax', function($query) use ($taxonomy) {
            $query->where('taxonomy', $taxonomy);
        });
        return $this;
    }

    public function exists($taxonomy, $name, $parent = 0)
    {
        $builder = $this->where('taxonomy', $taxonomy);
        if (is_numeric($name)) {
            $builder->where('term_id', $name);
        } else {
            $builder->where('slug', str_slug($name));
        }
        return $builder->where('parent', $parent)->first();
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (isset($this->aliases[$column])) {
            $column = $this->aliases[$column];
        }
        if (in_array($column, ['term_taxonomy_id', 'taxonomy', 'description', 'parent', 'count'])) {
            return $this->whereHas('tax', function($query) use ($column, $operator, $value, $boolean)
            {
                $query->where($column, $operator, $value, $boolean);
            });
        }
        return parent::where($column, $operator, $value, $boolean);
    }


    /**
     * Add an "order by" clause to the query.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        if (isset($this->aliases[$column])) {
            $column = $this->aliases[$column];
        }
        if ($column == 'term_order' && !Schema::hasColumn('terms', $column)) {
            return $this;
        }
        return parent::orderBy($column, $direction);
    }
}