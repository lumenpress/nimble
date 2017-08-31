<?php 

namespace Lumenpress\ORM\Concerns;

use Lumenpress\ORM\Models\Term;

trait TaxonomyAttributes
{
    /**
     * Mutator for order attribute.
     *
     * @return TaxonomyTerm
     */
    protected function getTerm($key)
    {
        return $this->term instanceof Term ? $this->term->$key : null;
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    protected function setTerm($key, $value)
    {
        if (!$this->term) {
            $this->relations['term'] = new Term;
        }
        $this->term->$key = $value;
    }

    /**
     * Accessor for name attribute.
     *
     * @return returnType
     */
    public function getNameAttribute($value)
    {
        return $this->getTerm('name');
    }

    /**
     * Mutator for name attribute.
     *
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->setTerm('name', $value);
    }

    /**
     * Accessor for slug attribute.
     *
     * @return returnType
     */
    public function getSlugAttribute($value)
    {
        return $this->getTerm('slug');
    }

    /**
     * Mutator for slug attribute.
     *
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->setTerm('slug', $value);
    }

    /**
     * Accessor for group attribute.
     *
     * @return returnType
     */
    public function getGroupAttribute($value)
    {
        return $this->getTerm('group');
    }

    /**
     * Mutator for group attribute.
     *
     * @return void
     */
    public function setGroupAttribute($value)
    {
        $this->setTerm('group', $value);
    }

    /**
     * Accessor for order attribute.
     *
     * @return returnType
     */
    public function getOrderAttribute($value)
    {
        return $this->getTerm('order');
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    public function setOrderAttribute($value)
    {
        $this->setTerm('order', $value);
    }

    /**
     * Accessor for parentId attribute.
     *
     * @return returnType
     */
    public function getParentIdAttribute($value)
    {
        return array_get($this->attributes, 'parent', 0);
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