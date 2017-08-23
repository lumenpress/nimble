<?php 
namespace Lumenpress\ORM\Collections\Concerns;

trait HasRelationships
{
    protected $relatedParent;

    protected $relatedClass;

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

    public static function create(array $models, $relatedClass)
    {
        return (new static($models))->setRelatedClass($relatedClass);;
    }
}
