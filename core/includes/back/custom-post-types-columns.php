<?php

if(!defined('ABSPATH')){exit;}

add_filter('manage_edit-gallery_columns', function ($columns) {
    $new = [];
    $new['cb'] = $columns['cb'] ?? '<input type="checkbox" />';
    $new['title'] = __('Title');
    $new['location'] = __('Location');
    $new['content'] = __('Content');
    $new['description'] = __('Description');
    $new['date'] = __('Date');
    $new['photo'] = __('Photo');
    return $new;
}, 20);

add_action('manage_gallery_posts_custom_column', function ($column, $post_id) {
    switch ($column) {
        case 'photo':
            $img = get_field('photo', $post_id);
            $id = is_array($img) ? ($img['id'] ?? 0) : (int)$img;
            if ($id) echo wp_get_attachment_image($id, 'thumbnail', false, ['style'=>'max-width:60px;height:auto']);
            break;
        case 'location':
        case 'content':
        case 'description':
            $v = get_field($column, $post_id);
            echo esc_html(wp_trim_words(wp_strip_all_tags((string)$v), 8, '…'));
            break;
    }
}, 10, 2);
