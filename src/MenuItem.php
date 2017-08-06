<?php 

namespace Lumenpress\Models;

use Lumenpress\Models\Builders\TermBuilder;

class MenuItem extends Post
{
    
    protected $postType = 'nav_menu_item';

    protected $with = ['meta'];

    protected $hidden = [
        'post_title',
        'post_content',
        'post_name',
        'post_excerpt',
        'post_type',
        'acf',
        'tax'
    ];

    protected $currentActive;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->append('type', 'classes', 'object', 'object_id', 'xfn', 'target', 'parent_id', 'current');
    }

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\Models\Collections\MenuCollection
     */
    public function newCollection(array $models = [])
    {
        return Collections\MenuItemCollection::create($models, static::class);
    }

    // /**
    //  * Accessor for subMenu attribute.
    //  *
    //  * @return returnType
    //  */
    // public function getSubMenuAttribute($value)
    // {
    //     if (!$this->meta->_menu_item_menu_item_parent) {
    //         return;
    //     }
    //     // $meta = PostMeta::where('meta_key', '_menu_item_menu_item_parent')
    //     //     ->where('meta_value', $this->meta->_menu_item_menu_item_parent)->get();
    // }

    /**
     * Accessor for menuItemParent attribute.
     *
     * @return returnType
     */
    public function getParentIdAttribute($value)
    {
        return (int) $this->meta->_menu_item_menu_item_parent;
    }

    /**
     * Accessor for title attribute.
     *
     * @return returnType
     */
    public function getTitleAttribute($value)
    {
        if ($this->post_title) {
            return $this->post_title;
        }
        switch ($this->type) {
            case 'post_type':
                return get_the_title($this->object_id);
                break;
            case 'taxonomy':
                return data_get(get_term($this->object_id), 'name');
                break;
            default:
                break;
        }
        return;
    }

    /**
     * Accessor for link attribute.
     *
     * @return returnType
     */
    public function getLinkAttribute($value)
    {
        switch ($this->type) {
            case 'post_type':
                return get_permalink((int) $this->object_id);
                break;
            case 'taxonomy':
                return get_term_link((int) $this->object_id, $this->object);
                break;
            case 'custom':
                return $this->meta->_menu_item_url;
                break;
            default:
                break;
        }
    }

    /**
     * Accessor for classes attribute.
     *
     * @return returnType
     */
    public function getClassesAttribute($value)
    {
        return implode(' ', $this->meta->_menu_item_classes);
    }

    /**
     * Accessor for objectId attribute.
     *
     * @return returnType
     */
    public function getObjectIdAttribute($value)
    {
        return (int)$this->meta->_menu_item_object_id;
    }

    /**
     * Accessor for _menu_item_object attribute.
     *
     * @return returnType
     */
    public function getObjectAttribute($value)
    {
        return $this->meta->_menu_item_object;
    }

    /**
     * Accessor for _menu_item_type attribute.
     *
     * @return returnType
     */
    public function getTypeAttribute($value)
    {
        return $this->meta->_menu_item_type;
    }

    /**
     * Accessor for target attribute.
     *
     * @return returnType
     */
    public function getTargetAttribute($value)
    {
        return $this->meta->_menu_item_target;
    }

    /**
     * Accessor for current attribute.
     *
     * @return returnType
     */
    public function getCurrentAttribute($value)
    {
        if (is_bool($this->currentActive)) {
            return $this->currentActive;
        }

        global $wp_query, $wp_rewrite;

        $queried_object = $wp_query->get_queried_object();
        $queried_object_id = (int) $wp_query->queried_object_id;

        $active_object = '';
        $active_ancestor_item_ids = array();
        $active_parent_item_ids = array();
        $active_parent_object_ids = array();
        $possible_taxonomy_ancestors = array();
        $possible_object_parents = array();
        $home_page_id = (int) get_option( 'page_for_posts' );
        if (
            $this->object_id == $queried_object_id &&
            (
                ( ! empty( $home_page_id ) && 'post_type' == $this->type && $wp_query->is_home && $home_page_id == $this->object_id ) ||
                ( 'post_type' == $this->type && $wp_query->is_singular ) ||
                ( 'taxonomy' == $this->type && ( $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ) && $queried_object->taxonomy == $this->object )
            )
        ) {
            return $this->currentActive = true;
        } elseif (
            'post_type_archive' == $this->type &&
            is_post_type_archive([$this->object])
        ) {
            return $this->currentActive = true;
        } elseif ( 'custom' == $this->object && isset( $_SERVER['HTTP_HOST'] ) ) {
            $_root_relative_current = untrailingslashit( $_SERVER['REQUEST_URI'] );

            //if it is the customize page then it will strips the query var off the url before entering the comparison block.
            if ( is_customize_preview() ) {
                $_root_relative_current = strtok( untrailingslashit( $_SERVER['REQUEST_URI'] ), '?' );
            }
            $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_root_relative_current );
            $raw_item_url = strpos( $this->link, '#' ) ? substr( $this->link, 0, strpos( $this->link, '#' ) ) : $this->link;
            $item_url = set_url_scheme( untrailingslashit( $raw_item_url ) );
            $_indexless_current = untrailingslashit( preg_replace( '/' . preg_quote( $wp_rewrite->index, '/' ) . '$/', '', $current_url ) );

            if ( $raw_item_url && in_array( $item_url, array( $current_url, $_indexless_current, $_root_relative_current ) ) ) {
                return $this->currentActive = true;
            }
        }
        return $this->currentActive = false;
    }

    /**
     * Mutator for current attribute.
     *
     * @return void
     */
    public function setCurrentAttribute($value)
    {
        $this->currentActive = $value;
    }

    /**
     * Accessor for xfn attribute.
     *
     * @return returnType
     */
    public function getXfnAttribute($value)
    {
        return $this->meta->_menu_item_xfn;
    }

}
