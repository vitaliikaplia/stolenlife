<?php

if(!defined('ABSPATH')){exit;}

function custom_body_classes($classes) {

    global $post;

    if ( !empty($post) and post_password_required( $post->ID ) ) {
        $classes[] = 'password-protected';
    }

    return $classes;
}

add_filter('body_class', 'custom_body_classes');
