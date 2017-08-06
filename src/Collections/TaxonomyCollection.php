<?php 

namespace Lumenpress\Models\Collections;

use Illuminate\Database\Eloquent\Model;

class TaxonomyCollection extends AbstractCollection
{
    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($taxonomy)
    {
        if (is_string($taxonomy)) {
            foreach ($this->items as $item) {
                if ($item->taxonomy == $taxonomy) {
                    return true;
                }
            }
            return false;
        } else {
            return array_key_exists($key, $this->items);
        }
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($taxonomy)
    {
        if (is_string($taxonomy)) {
            $items = collect([]);
            foreach ($this->items as $item) {
                if ($item->taxonomy == $taxonomy) {
                    $items->push($item);
                }
            }
            return $items;
        } else {
            return collect($this->items[$key]);
        }
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($taxonomy, $names)
    {
        if (is_string($taxonomy)) {
            $exists = [];

            foreach ($this->items as $index => $item) {
                if ($item->taxonomy == $taxonomy) {
                    if (in_array($item->name, (array)$names)) {
                        $exists[] = $item->name;
                    } else {
                        $this->extraItems[$item->taxonomy_id] = false;
                        unset($this->items[$index]);
                    }
                }
            }

            $this->items = array_values($this->items);

            // d(count($this->items));

            foreach ((array)$names as $name) {
                if (in_array($name, $exists)) {
                    continue;
                }
                $ItemClass = $this->itemClass;
                if ($item = $ItemClass::exists($taxonomy, $name)) {
                    $this->extraItems[$item->taxonomy_id] = true;
                } else {
                    $item = new $ItemClass;
                    $item->taxonomy = $taxonomy;
                    $item->name = $name;
                    $this->changedKeys[$taxonomy.'>|<'.$name] = true;
                }
                $this->items[] = $item;
            }
            // d(count($this->items));
        } else {
            parent::offsetSet($taxonomy, $value);
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($taxonomy)
    {
        if (is_string($taxonomy)) {
            foreach ($this->items as $index => $item) {
                if ($item->taxonomy == $taxonomy) {
                    $this->extraItems[$item->taxonomy_id] = false;
                    unset($this->items[$index]);
                }
            }
        } else {
            $this->extraItems[$this->items[$taxonomy]->taxonomy_id] = false;
            unset($this->items[$taxonomy]);
        }
        $this->items = array_values($this->items);
    }

    /**
     * [save description]
     * @param  [type] $objectId [description]
     * @return [type]           [description]
     */
    public function save(Model $object)
    {
        $flag = false;
        foreach ($this->items as $item) {
            if (isset($this->changedKeys[$item->taxonomy.'>|<'.$item->name])) {
                $flag = $item->save() || $flag;
                $this->extraItems[$item->taxonomy_id] = true;
            }
        }
        foreach ($this->extraItems as $taxonomyId => $new ) {
            $table = \DB::table('term_relationships');
            if ($new) {
                $flag = $table->insert([
                    'object_id' => $object->getKey(),
                    'term_taxonomy_id' => $taxonomyId
                ]) || $flag;
            } else {
                $flag = $table->where('object_id', $object->getKey())
                    ->where('term_taxonomy_id', $taxonomyId)->delete() || $flag;;
            }
        }
        $this->changedKeys = [];
        $this->extraItems = [];
        return $flag;
    }
}
