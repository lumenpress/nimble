<?php 

namespace Lumenpress\Models;

class PostMeta extends Meta
{

    protected $with = ['post'];

    /**
     * [$table description]
     * @var string
     */
    protected $table = 'postmeta';

    /**
     * [$relationKey description]
     * @var string
     */
    protected $objectKey = 'post_id';

    /**
     * PostMeta has one Post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function post()
    {
        // hasOne(RelatedModel, foreignKeyOnRelatedModel = postMeta_id, localKey = id)
        return $this->hasOne(Post::class, 'post_id');
    }

}
