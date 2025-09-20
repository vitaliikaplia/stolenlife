<?php

function get_canvas($id){

    $fields = cache_fields($id);

    $canvas = array(
        'id' => $id,
        'title' => get_the_title($id),
        'content' => $fields['content'],
        'location' => $fields['location'],
        'limited' => $fields['description'],
        'src' => $fields['photo']['url'],
        'alt' => $fields['photo']['alt'],
    );

    return $canvas;

}

function get_canvases(){

    $args = array(
        'post_type' => 'gallery',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'id',
        'order' => 'ASC',
    );

    $query = new WP_Query($args);
    $canvases = array();

    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $id = get_the_ID();
            $canvases[] = get_canvas($id);
        }
    }

    wp_reset_postdata();

    return $canvases;

}
