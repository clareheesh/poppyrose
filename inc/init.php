<?php

/**
 *
 * Sets up the theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs befor the init hook.
 * The init hook is too late for some features, such as indicating support for post thumbnails.
 *
 **/
// Enable support for Post Thumbnails on posts and pages.
add_theme_support('post-thumbnails');
add_theme_support('woocommerce');
add_action('after_theme_setup', 'spirit_fingers_setup');
if (!function_exists('spirit_fingers_setup')) {
    function spirit_fingers_setup()
    {
        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');


        // Add theme support for HTML5 markup
        add_theme_support('html5', array(
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
            'widgets'
        ));

        //Add theme support for Post Formats
        add_theme_support('post-formats', array(
            'aside',
            'image',
            'video',
            'quote',
            'link'
        ));

        // Add theme support for the document title tag
        add_theme_support('title-tag');

        // Add theme support for custom backgrounds
        add_theme_support('custom-background');

        // Add theme support for custom header
        add_theme_support('custom-header');
    }
}


/**
 * Register nav menus - this theme uses wp_nav_menu() in one location
 **/
add_action('init', 'sf_menu');
if (!function_exists('sf_menu')) {
    function sf_menu()
    {
        register_nav_menu('primary', __('Primary Menu', 'spirit-fingers'));
    }
}


/** Set default image link to none */
update_option('image_default_link_type', 'none');


/**
 *
 * Register widgetized area and update sidebar with default widgets.
 *
 **/
add_action('widgets_init', 'sf_widgets_init');
if (!function_exists('sf_widgets_init')) {
    function sf_widgets_init()
    {
        register_sidebar(array(
            'name' => __('Sidebar', 'spirit-fingers'),
            'id' => 'sidebar',
            'description' => __('Default Sidebar.', 'spirit-fingers'),
            'class' => '',
            'before_widget' => '<div class="sidebar-container">',
            'after_widget' => '</div>',
            'before_title' => '<span class="sidebar-title">',
            'after_title' => '</span><div class="dots"></div>',
        ));
    }
}


// Enqueue Stylesheets and Scripts
add_action('wp_enqueue_scripts', 'sf_bootstrap_scripts');
if (!function_exists('sf_bootstrap_scripts')) {
    function sf_bootstrap_scripts()
    {
        // Normalize
//        wp_enqueue_style('normalize', get_stylesheet_directory_uri() . '/assets/css/normalize.css');

        // Google Fonts
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:400,300,100,600|Roboto:400,600,700' );
//        wp_enqueue_style('animate', get_stylesheet_directory_uri() . '/assets/css/animate.css' );

        // Bootstrap
        wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js', array('jquery'), '3.3.5', true);
//        wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');

        // DataTables
//        wp_enqueue_script('data-tables-js', '//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', array('jquery'), '', true);
//        wp_enqueue_style('data-tables-css', '//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css');

        // Font Awesome
        wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');

        // Match Height
        wp_enqueue_script('match-height', get_stylesheet_directory_uri() . '/assets/js/jquery.matchHeight.js', array('jquery'), '', true);

        // Select 2
//        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'), '', true);

        // Javascript
        wp_enqueue_script('sf_javascript', get_stylesheet_directory_uri() . '/assets/js/sf_javascript.js', array('jquery'), '1.0', true);
        if (is_product())
            wp_enqueue_script('order_form_js', get_stylesheet_directory_uri() . '/assets/js/order_form.js', array('jquery'), '1.0', true);

        //Organised Styles
//        wp_enqueue_style('responsive-menu-style', get_stylesheet_directory_uri() . '/assets/css/menu.css');
//        wp_enqueue_style('default-style', get_stylesheet_directory_uri() . '/assets/css/default.css');

        // Always queue last, editable stylesheet
        wp_enqueue_style('sf-style', get_stylesheet_uri());
    }
}


/**
 *
 * Add editor style
 *
 **/
add_action('admin_init', 'sf_add_editor_styles');
if (!function_exists('sf_add_editor_styles')) {
    function sf_add_editor_styles()
    {
        add_editor_style(get_template_directory_uri() . '/assets/css/tinymce-style.css');
    }
}


/**
 *
 * Change the excerpt length to 200 words
 *
 **/
add_filter('excerpt_length', 'custom_excerpt_length', 999);
if (!function_exists('custom_excerpt_length')) {
    function custom_excerpt_length($length)
    {
        return 50;
    }
}


/**
 *
 * Change default exceprt more to ...
 *
 */
function new_excerpt_more($more)
{
    return '...';
}

add_filter('excerpt_more', 'new_excerpt_more');


/**
 * Copyright symbol shortcode
 */
function this_year_shortcode($atts)
{
    return date('Y');
}

add_shortcode('this_year', 'this_year_shortcode');


/**
 * Add Custom Header and Footer page
 */
if (function_exists('acf_add_options_page')) {

    $args = array(
        'page_title' => 'Theme Options',
        'position' => '60.1',
        'icon_url' => 'dashicons-desktop',
        'menu_slug' => 'theme-options',
    );

    $args_form = array(
        'page_title' => 'Form Options',
        'position' => '60.2',
        'icon-url' => 'dashicons-forms',
        'menu_slug' => 'form-options',
    );

    acf_add_options_page($args);
    acf_add_options_page($args_form);
}



/**
 * Turn the Wordpress widget into a shortcode
 */
add_shortcode('show_instagram_feed','poppy_get_instagram_feed');
function poppy_get_instagram_feed() {


    ob_start();

    the_widget('null_instagram_widget', array(
	    'size' => 'small',
	    'number' => 12,
	    'target' => '_blank',
        'username' => 'poppyrosebrisbane'
    ), []);

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}