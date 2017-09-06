<?php

namespace Lumenpress\ORM\Collections;

use Lumenpress\ORM\Models\TermRelationships;

class TaxonomyCollection extends Collection
{
    protected $aliases = [
        'tag' => 'post_tag',
    ];

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_numeric($key)) {
            return array_key_exists($key, $this->items);
        }

        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }

        foreach ($this->items as $item) {
            if ($item->taxonomy == $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (is_numeric($key)) {
            return isset($this->items[$key]) ? $this->items[$key] : null;
        }

        $items = collect([]);

        foreach ($this->items as $item) {
            if ($item->taxonomy == $key) {
                $items->push($item);
            }
        }

        return $items->isEmpty() ? null : $items;
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($taxonomy, $names)
    {
        if (is_null($taxonomy) || is_numeric($taxonomy)) {
            if (! is_array($names)) {
                throw new \Exception('args invalid', 1);
            }

            if (is_null($taxonomy) && ! Arr::has($value, ['name', 'taxonomy'])) {
                throw new \Exception('value invalid', 1);
            }

            if (is_null($taxonomy)) {
                $taxonomy = count($this->items);
                $this->items[$taxonomy] = $this->related->newInstance(['taxonomy' => $taxonomy]);
            }

            $item = $this->items[$taxonomy];

            foreach ($names as $k => $v) {
                $item->$k = $v;
            }

            $this->changedKeys[$item->taxonomy.'>|<'.$item->name] = true;

            return;
        }

        $exists = [];

        foreach ($this->items as $index => $item) {
            if ($item->taxonomy == $taxonomy) {
                if (in_array($item->name, (array) $names)) {
                    $exists[] = $item->name;
                } else {
                    $this->extraItems[$item->id] = false;
                    unset($this->items[$index]);
                }
            }
        }

        $this->items = array_values($this->items);

        foreach ((array) $names as $name) {
            if (in_array($name, $exists)) {
                continue;
            }
            $class = get_class($this->related);
            if ($item = $class::exists($name, $taxonomy, 0)) {
                $this->extraItems[$item->id] = true;
            } else {
                $item = $this->related->newInstance(['taxonomy' => $taxonomy]);
                $item->taxonomy = $taxonomy;
                $item->name = $name;
                $this->changedKeys[$taxonomy.'>|<'.$name] = true;
            }
            $this->items[] = $item;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     *
     * @return void
     */
    public function offsetUnset($taxonomy)
    {
        if (is_numeric($taxonomy)) {
            $this->extraItems[$this->items[$taxonomy]->id] = false;
            unset($this->items[$taxonomy]);
        } else {
            foreach ($this->items as $index => $item) {
                if ($item->taxonomy == $taxonomy) {
                    $this->extraItems[$item->id] = false;
                    unset($this->items[$index]);
                }
            }
        }
        $this->items = array_values($this->items);
    }

    /**
     * [save description].
     *
     * @param [type] $objectId [description]
     *
     * @return [type] [description]
     */
    public function save()
    {
        if (! $this->relatedParent) {
            return false;
        }
        $flag = false;
        foreach ($this->items as $item) {
            if (isset($this->changedKeys[$item->taxonomy.'>|<'.$item->name])) {
                $flag = $item->save() || $flag;
                $this->extraItems[$item->id] = true;
            }
        }
        foreach ($this->extraItems as $taxonomyId => $new) {
            if ($new) {
                $flag = TermRelationships::create([
                        'object_id'        => $this->relatedParent->id,
                        'term_taxonomy_id' => $taxonomyId,
                    ]) || $flag;
            } else {
                $flag = TermRelationships::where('object_id', $this->relatedParent->id)
                        ->where('term_taxonomy_id', $taxonomyId)->delete() || $flag;
            }
        }
        $this->changedKeys = [];
        $this->extraItems = [];

        return $flag;
    }
}
