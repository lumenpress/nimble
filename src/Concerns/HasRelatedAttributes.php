<?php 

namespace Lumenpress\ORM\Concerns;

trait HasRelatedAttributes
{
    protected $relatedParent;

    protected $relatedClass;

    protected $related;

    public function setRelatedParent(&$relatedParent)
    {
        $this->relatedParent = $relatedParent;
        return $this;
    }

    public function setRelatedClass($relatedClass)
    {
        $this->relatedClass = $relatedClass;
        return $this;
    }

    public function setRelated($related)
    {
        $this->related = $related;
        return $this;
    }

    public static function create(array $models, $relatedClass)
    {
        return (new static($models))->setRelatedClass($relatedClass);;
    }
}
