<?php

namespace LumenPress\Nimble;

use LumenPress\Nimble\Models\Post;
use LumenPress\Nimble\Models\Option;
use LumenPress\Nimble\Models\Taxonomy;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    public function boot()
    {
        $this->loadConfiguration();

        if (! function_exists('add_action')) {
            return;
        }

        add_action('after_setup_theme', function () {
            $this->registerNavMenus();
        });

        add_action('init', function () {
            $this->registerPostTemplates();
            $this->registerObjects();
            $this->registerModels();
        }, 99999);
    }

    public function register()
    {
        //
    }

    /**
     * Check if we are running Lumen or not.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return stripos($this->app->version(), 'Lumen') !== false;
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        $path = __DIR__.'/../config/nimble.php';

        if (! $this->isLumen()) {
            $this->publishes([$path => config_path('nimble.php')], 'config');
        }

        $this->mergeConfigFrom($path, 'nimble');
    }

    public function registerNavMenus()
    {
        foreach ((array) config('nimble.nav_menus') as $location => $description) {
            register_nav_menu($location, $description);
        }
    }

    public function registerPostTemplates()
    {
        foreach ((array) config('nimble.post_templates') as $key => $options) {
            if (is_string($options)) {
                $options = ['name' => $options, 'post_type' => ['page']];
            }

            $name = $options['name'];
            $post_types = isset($options['post_type']) ? $options['post_type'] : ['page'];

            foreach ((array) $post_types as $post_type) {
                add_filter("theme_{$post_type}_templates",
                    function ($templates) use ($key, $name) {
                        $templates[$key] = $name;

                        return $templates;
                    });
            }
        }
    }

    public function registerObjects()
    {
        foreach ((array) config('nimble.taxonomies') as $taxonomy => $args) {
            if (isset($args['label'])) {
                $args['labels'] = $this->parseLabelArgs(['name' => $args['label']], 'taxonomy');
            } elseif (isset($args['labels'])) {
                $args['labels'] = $this->parseLabelArgs($args['labels'], 'taxonomy');
            }

            if (isset($GLOBALS['wp_taxonomies'][$taxonomy])) {
                foreach ($args as $key => $value) {
                    $GLOBALS['wp_taxonomies'][$taxonomy]->$key = $value;
                }
                continue;
            }

            $object_type = array_get($args, 'object_type', array_get($args, 'post_type', 'post'));

            register_taxonomy($taxonomy, $object_type, $args);
        }

        foreach ((array) config('nimble.post_types') as $post_type => $args) {
            if (isset($args['label'])) {
                $args['labels'] = $this->parseLabelArgs(['name' => $args['label']], 'post_type');
            } elseif (isset($args['labels'])) {
                $args['labels'] = $this->parseLabelArgs($args['labels'], 'post_type');
            }

            if (isset($GLOBALS['wp_post_types'][$post_type])) {
                foreach ($args as $key => $value) {
                    $GLOBALS['wp_post_types'][$post_type]->$key = $value;
                }
                continue;
            }

            register_post_type($post_type, $args);
        }

        flush_rewrite_rules();
    }

    public function registerModels()
    {
        global $wp_post_types, $wp_taxonomies;

        if (class_exists(config('nimble.option'))) {
            Option::setOptionClass(config('nimble.option'));
        }

        if (class_exists(config('nimble.term'))) {
            Taxonomy::setTermClass(config('nimble.term'));
        }

        foreach ($wp_post_types as $type => $object) {
            if (! property_exists($object, 'model')) {
                continue;
            }

            Post::register($type, $object->model);
        }

        foreach ($wp_taxonomies as $taxonomy => $object) {
            if (! property_exists($object, 'model')) {
                continue;
            }

            Taxonomy::register($taxonomy, $object->model);
        }
    }

    public function parseLabelArgs($labels, $object_type = 'post_type')
    {
        if (array_has($labels, ['name', 'singular_name'])) {
            $name = array_get($labels, 'name');
            $singular_name = array_get($labels, 'singular_name');
        } elseif (array_only($labels, ['name'])) {
            $name = array_get($labels, 'name');
            $singular_name = str_singular($name);
        } elseif (array_only($labels, ['singular_name'])) {
            $singular_name = array_get($labels, 'singular_name');
            $name = str_plural($singular_name);
        } else {
            $name = 'Posts';
            $singular_name = 'Post';
        }

        $lower_name = strtolower($name);
        $lower_singular_name = strtolower($singular_name);

        $defaults = [
            'post_type' => [
                'name' => $name,
                'singular_name' => $singular_name,
                'add_new' => 'Add New',
                'add_new_item' => "Add New $singular_name",
                'edit_item' => "Edit $singular_name",
                'new_item' => "New $singular_name",
                'view_item' => "View $singular_name",
                'view_items' => "View $name",
                'search_items' => "Search $name",
                'not_found' => "No $lower_name found.",
                'not_found_in_trash' => "No $lower_name found in Trash.",
                'parent_item_colon' => "Parent $singular_name:",
                'all_items' => "All $name",
                'archives' => "$singular_name Archives",
                'attributes' => "$singular_name Attributes",
                'insert_into_item' => "Insert into $lower_singular_name",
                'uploaded_to_this_item' => "Uploaded to this $lower_singular_name",
                'featured_image' => 'Featured Image',
                'set_featured_image' => 'Set featured image',
                'remove_featured_image' => 'Remove featured image',
                'use_featured_image' => 'Use as featured image',
                'filter_items_list' => "Filter $name list",
                'items_list_navigation' => "$name list navigation",
                'items_list' => "$name list",
            ],
            'taxonomy' => [
                'name' => $name,
                'singular_name' => $singular_name,
                'search_items' => "Search $name",
                'popular_items' => "Popular $name",
                'all_items' => "All $name",
                'parent_item' => "Parent $singular_name",
                'parent_item_colon' => "Parent $singular_name:",
                'edit_item' => "Edit $singular_name",
                'view_item' => "View $singular_name",
                'update_item' => "Update $singular_name",
                'add_new_item' => "Add New $singular_name",
                'new_item_name' => "New $singular_name Name",
                'separate_items_with_commas' => "Separate $lower_name with commas",
                'add_or_remove_items' => "Add or remove $lower_name",
                'choose_from_most_used' => "Choose from the most used $lower_name",
                'not_found' => "No $lower_name found.",
                'no_terms' => "No $lower_name",
                'items_list_navigation' => "$name list navigatio",
                'items_list' => "$name list",
            ],
        ];

        return array_merge($defaults[$object_type], $labels);
    }
}
