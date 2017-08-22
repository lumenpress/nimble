<?php 

namespace Lumenpress\ORM\Models;

class Taxonomy extends TermTaxonomy
{
    protected $with = ['term', 'meta'];

    protected $appends = [
        'taxonomy_id',
        'name',
        'slug',
        'group',
        'order',
        'link',
    ];

    protected $hidden = [
        'term',
        'term_taxonomy_id',
        // 'pivot'
    ];

    /**
     * [term description]
     * @return [type] [description]
     */
    public function term()
    {
        return $this->hasOne(TaxonomyTerm::class, 'term_id');
    }

    /**
     * Meta data relationship.
     *
     * @return Lumenpress\ORM\TermMetaCollection
     */
    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id');
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
     * Accessor for Link attribute.
     *
     * @return returnType
     */
    public function getLinkAttribute($value)
    {
        return get_term_link($this->term_id, $this->taxonomy);
    }

    /**
     * Mutator for order attribute.
     *
     * @return TaxonomyTerm
     */
    protected function getTerm($key)
    {
        return $this->term instanceof TaxonomyTerm ? $this->term->$key : null;
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    protected function setTerm($key, $value)
    {
        if (!$this->term) {
            $this->relations['term'] = new TaxonomyTerm;
        }
        $this->term->$key = $value;
    }

    public function save(array $options = array())
    {
        if (!$this->taxonomy) {
            throw new \Exception("Invalid taxonomy.");
        }
        if (!$this->term_id) {
            if (static::exists($this->taxonomy, $this->name)) {
                throw new \Exception('A term with the name provided already exists with this parent.');
                // return false;
            }
        }
        if (!$this->slug) {
            $this->slug = str_slug($this->name);
        }
        $this->count = static::taxonomy($this->taxonomy)->count();
        $this->term->save();
        $this->term_id = $this->term->term_id;
        return parent::save($options);
    }

}
