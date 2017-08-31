<?php 

namespace Lumenpress\ORM\Relations;

use Lumenpress\ORM\Models\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasMeta extends HasMany
{
    /**
     * Create a new has one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return void
     */
    public function __construct(Model $parent)
    {
        $this->parent = $parent;

        $instance = $this->newRelatedInstance();

        parent::__construct(
            $instance->newQuery(), 
            $parent,
            $this->getForeignKey(), 
            $parent->getKeyName()
        );
    }

    protected function newRelatedInstance()
    {
        return tap(new Meta, function ($instance) {
            $instance->setTableThroughParentTable($this->parent->getTable());

            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->parent->getConnectionName());
            }
        });
    }

    protected function getForeignKey()
    {
        switch ($this->parent->getTable()) {
            case 'posts':
                return 'postmeta.post_id';
            case 'terms':
                return 'termmeta.term_id';
            case 'users':
                return 'usermeta.user_id';
            case 'comments':
                return 'commentmeta.comment_id';
        }
    }
}
