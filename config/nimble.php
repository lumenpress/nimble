<?php 

return [
    'post_templates' => [
        /*
        'home' => [
            'name'      => 'Home Page',
            'post_type' => 'post',  // string|array
        ]
        */
    ],

    'post_types' => [
        /*
        'slug' => [
            'labels'              => [
                'name'               => __( 'Plural Name', 'text-domain' ),
                'singular_name'      => __( 'Singular Name', 'text-domain' ),
                'add_new'            => _x( 'Add New Singular Name', 'text-domain', 'text-domain' ),
                'add_new_item'       => __( 'Add New Singular Name', 'text-domain' ),
                'edit_item'          => __( 'Edit Singular Name', 'text-domain' ),
                'new_item'           => __( 'New Singular Name', 'text-domain' ),
                'view_item'          => __( 'View Singular Name', 'text-domain' ),
                'search_items'       => __( 'Search Plural Name', 'text-domain' ),
                'not_found'          => __( 'No Plural Name found', 'text-domain' ),
                'not_found_in_trash' => __( 'No Plural Name found in Trash', 'text-domain' ),
                'parent_item_colon'  => __( 'Parent Singular Name:', 'text-domain' ),
                'menu_name'          => __( 'Plural Name', 'text-domain' ),
            ],
            'model'               => LumenPress\Nimble\Models\Post::class,
            'hierarchical'        => false,
            'description'         => 'description',
            'taxonomies'          => [],
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => null,
            'menu_icon'           => null,
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => true,
            'capability_type'     => 'post',
            'supports'            => [
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'trackbacks',
                'comments',
                'revisions',
                'page-attributes',
                'post-formats',
            ],
        ]
        */
    ],

    'taxonomies' => [
        /*
        'taxonomy-slug' => [
            'labels'            => [
                'name'                  => _x( 'Plural Name', 'Taxonomy plural name', 'text-domain' ),
                'singular_name'         => _x( 'Singular Name', 'Taxonomy singular name', 'text-domain' ),
                'search_items'          => __( 'Search Plural Name', 'text-domain' ),
                'popular_items'         => __( 'Popular Plural Name', 'text-domain' ),
                'all_items'             => __( 'All Plural Name', 'text-domain' ),
                'parent_item'           => __( 'Parent Singular Name', 'text-domain' ),
                'parent_item_colon'     => __( 'Parent Singular Name', 'text-domain' ),
                'edit_item'             => __( 'Edit Singular Name', 'text-domain' ),
                'update_item'           => __( 'Update Singular Name', 'text-domain' ),
                'add_new_item'          => __( 'Add New Singular Name', 'text-domain' ),
                'new_item_name'         => __( 'New Singular Name Name', 'text-domain' ),
                'add_or_remove_items'   => __( 'Add or remove Plural Name', 'text-domain' ),
                'choose_from_most_used' => __( 'Choose from most used Plural Name', 'text-domain' ),
                'menu_name'             => __( 'Singular Name', 'text-domain' ),
            ],
            'model'             => LumenPress\Nimble\Models\Taxonomy::class,
            'object_type'        => [],
            'public'            => true,
            'show_in_nav_menus' => true,
            'show_admin_column' => false,
            'hierarchical'      => false,
            'show_tagcloud'     => true,
            'show_ui'           => true,
            'query_var'         => true,
            'rewrite'           => true,
            'query_var'         => true,
            'capabilities'      => [],
        ],
        */
    ],

    // 'term' => LumenPress\Nimble\Models\Term::class,
];
