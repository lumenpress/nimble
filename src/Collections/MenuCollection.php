<?php 

namespace Lumenpress\ORM\Collections;

use Illuminate\Database\Eloquent\Model;

class MenuCollection extends AbstractCollection
{

    protected $locations = [];

    public function __construct(array $items = [])
    {
        $menus = [];
        foreach ($items as $key => $menu) {
            $menus[$menu->term_id] = $menu;
        }
        $this->locations = get_nav_menu_locations();
        parent::__construct($menus);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_string($key)) {
            return array_key_exists($key, $this->locations);
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
    public function offsetGet($key)
    {
        if (is_string($key)) {
            $key = $this->locations[$key];
        }
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $menu)
    {
        
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($taxonomy)
    {
        
    }

    /**
     * [save description]
     * @param  [type] $objectId [description]
     * @return [type]           [description]
     */
    public function save()
    {
        
    }
}
