<?php 

namespace Lumenpress\Models;

use Lumenpress\Models\Builders\TermBuilder;

class Term extends TaxonomyTerm
{
    /**
     * [$with description]
     * @var [type]
     */
    protected $with = ['meta'];

    /**
     * [$appends description]
     * @var [type]
     */
    protected $appends = [
        'taxonomy_id',
        'taxonomy',
        'description',
        'parent',
        'count',
        'group',
        'order',
        'link',
    ];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = [
        'term_id',
        'term_order',
        'term_group',
        'tax',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tax()
    {
        return $this->hasOne(TermTaxonomy::class, $this->primaryKey);
    }

    /**
     * Meta data relationship.
     *
     * @return Lumenpress\Models\TermMetaCollection
     */
    public function meta()
    {
        return $this->hasMany(TermMeta::class, $this->primaryKey);
    }

    /**
     * Relationship with Posts model.
     *
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function posts()
    {
        return $this->tax->posts();
    }

    /**
     * Accessor for TaxonomyId attribute.
     *
     * @return returnType
     */
    public function getTaxonomyIdAttribute($value)
    {
        return $this->getTaxonomy('taxonomy_id');
    }

    /**
     * Mutator for taxonomyId attribute.
     *
     * @return void
     */
    public function setTaxonomyIdAttribute($value)
    {
        $this->setTaxonomy('taxonomy_id', $value);
    }

    /**
     * Accessor for taxonomy attribute.
     *
     * @return returnType
     */
    public function getTaxonomyAttribute($value)
    {
        return $this->getTaxonomy('taxonomy');
    }

    /**
     * Mutator for taxonomy attribute.
     *
     * @return void
     */
    public function setTaxonomyAttribute($value)
    {
        $this->setTaxonomy('taxonomy', $value);
    }

    /**
     * Accessor for description attribute.
     *
     * @return returnType
     */
    public function getDescriptionAttribute($value)
    {
        return $this->getTaxonomy('description');
    }

    /**
     * Mutator for description attribute.
     *
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->setTaxonomy('description', $value);
    }

    /**
     * Accessor for parent attribute.
     *
     * @return returnType
     */
    public function getParentAttribute($value)
    {
        return $this->getTaxonomy('parent');
    }

    /**
     * Mutator for parent attribute.
     *
     * @return void
     */
    public function setParentAttribute($value)
    {
        $this->setTaxonomy('parent', $value);
    }

    /**
     * Accessor for count attribute.
     *
     * @return returnType
     */
    public function getCountAttribute($value)
    {
        return $this->tax->count;
    }

    /**
     * Mutator for count attribute.
     *
     * @return void
     */
    public function setCountAttribute($value)
    {
        $this->setTaxonomy('count', $value);
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
    protected function getTaxonomy($key)
    {
        return $this->tax instanceof TermTaxonomy ? $this->tax->$key : null;
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    protected function setTaxonomy($key, $value)
    {
        if (!$this->tax) {
            $this->relations['tax'] = new TermTaxonomy;
        }
        $this->tax->$key = $value;
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
        if (!parent::save($options)) {
            return false;
        };
        $this->tax->term_id = $this->term_id;
        return $this->tax->save();
    }

}
