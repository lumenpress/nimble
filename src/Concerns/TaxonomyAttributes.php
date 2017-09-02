<?php 

namespace Lumenpress\ORM\Concerns;

use Lumenpress\ORM\Models\Term;

trait TaxonomyAttributes
{
    /**
     * [$termClass description]
     * @var [type]
     */
    protected $termClass = Term::class;

    /**
     * [term description]
     * @return [type] [description]
     */
    public function term()
    {
        return $this->hasOne($this->termClass, 'term_id', 'term_id');
    }

    public function getAttribute($key)
    {
        if (!is_null($value = parent::getAttribute($key))) {
            return $value;
        }

        return $this->getRelation('term')->getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, ['id', 'term_taxonomy_id', 'term_id', 'count', 'parent', 'description', 'taxonomy'])) {
            return parent::setAttribute($key, $value);
        }

        return $this->getRelation('term')->setAttribute($key, $value);
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation)
    {
        if ($relation == 'term' && !isset($this->relations[$relation])) {
            $class = $this->termClass;
            $this->relations[$relation] = new $class;
        }
        return $this->relations[$relation];
    }

    /**
     * Accessor for parentId attribute.
     *
     * @return returnType
     */
    public function getParentIdAttribute($value)
    {
        return isset($this->attributes['parent']) ? $this->attributes['parent'] : 0;
    }

    /**
     * Mutator for parentId attribute.
     *
     * @return void
     */
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent'] = $value;
    }
}