<?php

namespace Lumenpress\ORM\Builders;

use Illuminate\Support\Facades\Schema;

class TermBuilder extends Builder
{

    protected $aliases = [
        'id' => 'term_id',
        'order' => 'term_order',
        'group' => 'term_group',
    ];

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (isset($this->aliases[$column])) {
            $column = $this->aliases[$column];
        }
        return parent::where($column, $operator, $value, $boolean);
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  string $column
     * @param  string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        if (isset($this->aliases[$column])) {
            $column = $this->aliases[$column];
        }
        // if ($column == 'term_order' && !Schema::hasColumn('terms', $column)) {
        //     return $this;
        // }
        return parent::orderBy($column, $direction);
    }
}