<?php

namespace Lumenpress\Fluid\Concerns;

trait HasPostPaginationAttributes
{
    protected function queryAdjacentRelation()
    {
        return static::where('post_type', $this->post_type)
            ->where('post_status', $this->post_status)
            ->where('post_date', '<', (string) $this->post_date);
    }

    /**
     * Accessor for prev post attribute.
     *
     * @return returnType
     */
    public function getPreviousAttribute($value)
    {
        return static::queryAdjacentRelation()
            ->orderBy('menu_order', 'asc')
            ->orderBy('post_date', 'desc')
            ->orderBy('ID', 'desc')
            ->first();
    }

    /**
     * Accessor for next post attribute.
     *
     * @return returnType
     */
    public function getNextAttribute($value)
    {
        return static::queryAdjacentRelation()
            ->orderBy('menu_order', 'desc')
            ->orderBy('post_date', 'asc')
            ->orderBy('ID', 'asc')
            ->first();
    }
}
