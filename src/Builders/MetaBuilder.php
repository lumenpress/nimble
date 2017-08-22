<?php 

namespace Lumenpress\ORM\Builders;

class MetaBuilder extends Builder
{
    /**
     * Get meta_value column's value from the first result.
     *
     * @param  string  $column
     * @return mixed
     */
    public function value($column = 'meta_value')
    {
        return parent::value($column);
        // if (in_array($column, ['meta_value', 'post_id', 'term_id'])) {
        //     return parent::value($column);
        // }
        // return parent::value('meta_value') ?: $column;
    }

    public function updateValue($value)
    {
        $attributes = ['meta_value' => $value];
        return $this->update($attributes);
    }
}
