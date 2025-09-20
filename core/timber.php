<?php

if(!defined('ABSPATH')){exit;}

use Timber\Site;

class StarterSite extends Site {
    public function __construct() {
        add_filter( 'timber/context', array( $this, 'add_to_context' ) );
        add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
        parent::__construct();
    }

    /**
     * This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context( $context ) {
        $context['site'] = $this;
        $context['assets'] = ASSETS_VERSION;
        $context['site_language'] = get_bloginfo('language');
        $context['svg_sprite'] = SVG_SPRITE_URL;
        $context['general_fields'] = cache_general_fields();
        $context['TEXTDOMAIN'] = TEXTDOMAIN;
        return $context;
    }

    function add_to_twig( $twig ) {
        /* this is where you can add your own functions to twig */
        $twig->addExtension( new \Twig\Extension\StringLoaderExtension() );
        $twig->addFilter( new \Twig\TwigFilter( 'pr', 'pr' ) );
        $twig->addFilter( new \Twig\TwigFilter( 'log', 'write_log' ) );
        $twig->addFunction( new \Twig\TwigFunction('get_pattern', 'get_pattern'));
        $twig->addFilter( new \Twig\TwigFilter( 'picture', 'render_picture_tag' ) );
        $twig->addFilter( new \Twig\TwigFilter( 'picture_src', 'render_picture_src' ) );
        $twig->addFunction( new \Twig\TwigFunction('get_option', 'get_option'));
        $twig->addFunction( new \Twig\TwigFunction('wp_editor', 'wp_editor'));
        $twig->addFunction( new \Twig\TwigFunction('checked', 'checked'));
        $twig->addFunction( new \Twig\TwigFunction('get_canvas', 'get_canvas'));
        $twig->addFunction( new \Twig\TwigFunction('get_canvases', 'get_canvases'));
        $twig->addFunction( new \Twig\TwigFunction('fix_phone_format', 'fix_phone_format'));
        $twig->addFunction( new \Twig\TwigFunction('short_phone_format', 'short_phone_format'));
        $twig->addFunction( new \Twig\TwigFunction('nice_phone_format', 'nice_phone_format'));
        return $twig;
    }
}

Timber\Timber::init();
Timber::$dirname = TIMBER_VIEWS;
new StarterSite();
