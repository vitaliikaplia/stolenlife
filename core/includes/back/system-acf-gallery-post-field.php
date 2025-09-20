<?php

if(!defined('ABSPATH')){exit;}

if (class_exists('ACF')) {

    class ACF_Field_Gallery_Post_V5 extends acf_field {
        public function __construct() {
            $this->name     = 'gallery_post';
            $this->label    = __('Gallery Post', 'acf');
            $this->category = 'relational';
            $this->defaults = [
                    'allow_null' => 0,
            ];
            parent::__construct();
        }

        public function render_field_settings($field) {
            acf_render_field_setting($field, [
                    'label'        => __('Allow Null?', 'acf'),
                    'type'         => 'radio',
                    'name'         => 'allow_null',
                    'layout'       => 'horizontal',
                    'choices'      => [1 => __('Yes', 'acf'), 0 => __('No', 'acf')],
            ]);
        }

        public function render_field($field) {
            $value = (int)$field['value'];
            $selected = '';
            if ($value) {
                $p = get_post($value);
                if ($p && $p->post_type === 'gallery') {
                    $img = get_field('photo', $p->ID);
                    $id  = is_array($img) ? ($img['id'] ?? 0) : (int)$img;
                    $src = $id ? wp_get_attachment_image_url($id, 'thumbnail') : '';
                    $selected = sprintf(
                            '<option value="%d" selected data-thumb="%s">%s</option>',
                            $p->ID,
                            esc_url($src ?: ''),
                            esc_html(get_the_title($p))
                    );
                }
            }
            printf(
                    '<select class="acf-gallery-post-select" name="%s" id="%s" data-allow-null="%s" data-ajax-url="%s">%s</select>',
                    esc_attr($field['name']),
                    esc_attr($field['id']),
                    esc_attr($field['allow_null'] ? '1' : '0'),
                    esc_url(admin_url('admin-ajax.php')),
                    $selected
            );
        }
    }

    new ACF_Field_Gallery_Post_V5();

    add_action('acf/input/admin_enqueue_scripts', function () {
        wp_enqueue_script('acf-input'); // у ACF тут є select2
        wp_add_inline_script('acf-input', '
        (function($){
            function init($el){
                if(!$el.length) return;
                $el.select2({
                    width: "100%",
                    allowClear: $el.data("allow-null") == 1,
                    placeholder: "—",
                    ajax: {
                        url: ajaxurl,
                        dataType: "json",
                        delay: 200,
                        cache: true,
                        data: function(params){
                            return {
                                action: "acf_gallery_post_search",
                                q: params.term || "",
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params){
                            params.page = params.page || 1;
                            return { results: data.results || [], pagination: { more: !!data.more } };
                        }
                    },
                    templateResult: function(repo){
                        if(!repo.id) return repo.text;
                        var $c = $(\'<span style="display:flex;align-items:center;gap:8px"></span>\');
                        if(repo.thumb){
                            $c.append($(\'<img>\').attr({src: repo.thumb, alt:""}).css({width:32,height:32,objectFit:"cover"}));
                        }
                        $c.append($(\'<span>\').text(repo.text));
                        return $c;
                    },
                    templateSelection: function(repo){ return repo.text || repo.id; },
                    escapeMarkup: function(m){ return m; }
                });
            }
            function boot(scope){
                $(scope).find("select.acf-gallery-post-select").each(function(){ init($(this)); });
            }
            acf.add_action("ready append", function($el){ boot($el); });
        })(jQuery);
    ');
    });

    add_action('wp_ajax_acf_gallery_post_search', function () {
        if (!current_user_can('edit_posts')) wp_send_json_error();
        $s     = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $ppp   = 20;

        $args = [
                'post_type'           => 'gallery',
                'post_status'         => 'publish',
                's'                   => $s,
                'posts_per_page'      => $ppp,
                'paged'               => $page,
                'suppress_filters'    => false,
                'no_found_rows'       => false,
                'fields'              => 'ids',
        ];

        $q = new WP_Query($args);
        $results = [];

        foreach ($q->posts as $pid) {
            $img = get_field('photo', $pid);
            $id  = is_array($img) ? ($img['id'] ?? 0) : (int)$img;
            $src = $id ? wp_get_attachment_image_url($id, 'thumbnail') : '';
            $results[] = [
                    'id'    => $pid,
                    'text'  => get_the_title($pid),
                    'thumb' => $src ?: '',
            ];
        }

        $more = ($page * $ppp) < (int)$q->found_posts;
        wp_send_json(['results' => $results, 'more' => $more]);
    });

}
