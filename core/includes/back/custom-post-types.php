<?php

if(!defined('ABSPATH')){exit;}

register_post_type('redirect-rules', array(
        'label' => __('Redirect rules', TEXTDOMAIN),
        'description' => '',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => '/options-general.php',
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => false, 'with_front' => false),
        'query_var' => true,
        'has_archive' => false,
        'supports' => array('author'),
        'labels' => array (
            'name' => __('Redirect rules', TEXTDOMAIN),
            'singular_name' => __('Redirect rule', TEXTDOMAIN),
            'menu_name' => __('Redirect rules', TEXTDOMAIN),
            'add_new' => __('Add redirect rule', TEXTDOMAIN),
            'add_new_item' => __('Add new redirect rule', TEXTDOMAIN),
            'edit' => __('Edit', TEXTDOMAIN),
            'edit_item' => __('Edit redirect rule', TEXTDOMAIN),
            'new_item' => __('New redirect rule', TEXTDOMAIN),
            'view' => __('View', TEXTDOMAIN),
            'view_item' => __('View redirect rule', TEXTDOMAIN),
            'search_items' => __('Search for redirect rules', TEXTDOMAIN),
            'not_found' => __('No redirect rules found', TEXTDOMAIN),
            'not_found_in_trash' => __('No redirect rules found in trash', TEXTDOMAIN),
            'parent' => __('Parent redirect rule', TEXTDOMAIN)
        )
    )
);
