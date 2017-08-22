<?php 

namespace Lumenpress\ORM\Collections;

use Illuminate\Database\Eloquent\Model;

class MenuItemCollection extends AbstractCollection
{

    public $actives = [];

    protected $active = false;

    public function __construct(array $items = [])
    {
        parent::__construct($this->flatToTree($items));
    }

    public function flatToTree($items, $main = [], & $active = false)
    {
        if (empty($items)) {
            return [];
        }
        if (empty($main)) {
            $main = [];
            foreach ($items as $item) {
                $main[$item->parent_id][] = $item;
            }
            $items = isset($main[0]) ? $main[0] : [];
        }
        $this->active = false;
        foreach ($items as $key => $item) {
            if ($item->current) {
                $this->active = true;
            }
            if (isset($main[$item->ID])) {
                $items[$key]->sub_menu = $this->flatToTree($main[$item->ID], $main);
                $items[$key]->current = $items[$key]->current ? : $this->active;
            }
        }
        return $items;
    }

    public function save(Model $model)
    {
        # code...
    }

}
