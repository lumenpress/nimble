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
        $instance = $this->newRelatedInstance($parent);

        parent::__construct(
            $instance->newQuery(), 
            $parent,
            $this->getForeignKey($parent), 
            $parent->getKeyName()
        );
    }

    /**
     * [getAcfRelated description]
     * @return [type] [description]
     */
    protected function newRelatedInstance($parent)
    {
        return tap(new Meta, function ($instance) use ($parent) {
            $instance->setTableThroughParentTable($parent->getTable());

            if (! $instance->getConnectionName()) {
                $instance->setConnection($parent->getConnectionName());
            }
        });
    }

    protected function getForeignKey($parent)
    {
        switch ($parent->getTable()) {
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
