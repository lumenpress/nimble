<?php 

namespace Lumenpress\Models\Builders;

class TermTaxonomyBuilder extends Builder
{

    protected $aliases = [
        'tag' => 'post_tag',
    ];

    public function type($type)
    {
        return $this->isTax($type);
    }

    public function isTax($taxonomy)
    {
        if (isset($this->aliases[$taxonomy])) {
            $taxonomy = $this->aliases[$taxonomy];
        }
        return $this->where('taxonomy', $taxonomy);
    }

    public function whereTerm($taxonomy, $value)
    {
        $this->isTax($taxonomy);
        if (is_null($value)) {
            return $this;
        }
        $this->whereHas('term', function($query) use ($value) {
            if (is_numeric($value)) {
                $query->where('term_id', $value);
            } elseif (is_string($value)) {
                $query->where('slug', $value);
            } elseif (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!is_array($v)) {
                        $v = [$v];
                    }
                    call_user_func_array([$query, 'where'], array_merge([$k], $v));
                }
            }
        });
        return $this;
    }

    public function orWhereTerm($taxonomy, $value)
    {
        $this->isTax($taxonomy);
        if (is_null($value)) {
            return $this;
        }
        $this->orWhereHas('term', function($query) use ($value) {
            if (is_numeric($value)) {
                $query->where('term_id', $value);
            } else {
                $query->where('slug', $value);
            }
        });
        return $this;
    }
}
