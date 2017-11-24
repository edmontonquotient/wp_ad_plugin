<?php
/**
 * Plugin Name: Edmonton Quotient Ads
 * Plugin URI: http://edmontonquotient.com/open-source
 * Description: Generates ad functionality for EQ
 * Version: 1.0
 * Author: Sally Poulsen
 * Author URI: http://edmontonquotient
 * Text Domain: Optional. Plugin's text domain for localization. Example: mytextdomain
 * Domain Path: Optional. Plugin's relative directory path to .mo files. Example: /locale/
 * Network: Optional. Whether the plugin can only be activated network wide. Example: true
 * License: MIT/Crockford
 */

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/**
 * Enqueue plugin style-file
 */

function eq_callout_stylesheet() {
    wp_register_style( 'eq_callout', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'eq_callout' );
}

add_action( 'wp_enqueue_scripts', 'eq_callout_stylesheet' );

/**
 * Register ads CPT
 */

function eq_ads_init_custom_post_type($post) {
    $labels = array(
        'name'                => _x( 'Advertising', 'Post Type General Name', 'eq' ),
        'singular_name'       => _x( 'Advertising', 'Post Type Singular Name', 'eq' ),
        'menu_name'           => __( 'Advertising', 'eq' ),
        'parent_item_colon'   => __( 'Parent Advertising', 'eq' ),
        'all_items'           => __( 'All Advertising', 'eq' ),
        'view_item'           => __( 'View Advertising', 'eq' ),
        'add_new_item'        => __( 'Add New Advertising', 'eq' ),
        'add_new'             => __( 'Add New', 'eq' ),
        'edit_item'           => __( 'Edit Advertising', 'eq' ),
        'update_item'         => __( 'Update Advertising', 'eq' ),
        'search_items'        => __( 'Search Advertising', 'eq' ),
        'not_found'           => __( 'Not Found', 'eq' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'eq' ),
    );
    
    $args = array(
        'label'               => __( 'Advertising', 'eq' ),
        'description'         => __( 'Advertising info', 'eq' ),
        'labels'              => $labels,
        'supports'            => array( 'title'),
        'taxonomies'          => array( 'types' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => false,
        'menu_icon'           => 'dashicons-clipboard',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    
    register_post_type( 'advertising', $args );
}

add_action( 'init', 'eq_ads_init_custom_post_type', 0 );

/*
*   Create zones to drop ads into
*/

function eq_ads_advertising_zones() {

    register_sidebar( array(
        'name'          => 'Sidebar Advertising',
        'id'            => 'sidebar_advertising',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => 'Mobile Advertising',
        'id'            => 'mobile_advertising',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
    ) );

}
add_action( 'widgets_init', 'eq_ads_advertising_zones' );

add_filter('widget_text', 'do_shortcode');


function eq_ads_shortcode_instructions_example() {
    global $my_admin_page;
    $screen = get_current_screen();

    if ( is_admin() && ($screen->id == 'edit-advertising') ) {
            echo '<div class="postbox" style="background:#0074a2;color:#fff;margin-top:20px;"><div class="inside">';
            echo 'To include an ad block, use one of the following shortcodes in any widget or WYSIWYG area:';
            echo '<br/><strong>[eq_sidebar_ads] | [eq_mobile_ads] | [eq_inline_ads]</strong>';
            echo '<br/>These shortcodes can be added to widgets or the body of any WYSIWYG editor.';
            echo '</div></div>';
    }

    if ( is_admin() && ($screen->id == 'advertising') ) {
            echo '<div class="postbox" style="background:#0074a2;color:#fff;margin-top:20px;"><div class="inside">';
            echo 'To include this ad as a single, inline ad block, use the following shortcodes in any widget or WYSIWYG area:';
            echo '<br/>[eq_inline_ads]';
            echo '<br/>These shortcodes can be added to widgets or the body of any WYSIWYG editor.';
            echo '</div></div>';
    }

}
add_action( 'admin_notices', 'eq_ads_shortcode_instructions_example' );


/*
*   Generate includes for the shortcodes
*/

function eq_ads_sidebar_ad_shortcode( $atts ){

    return include plugin_dir_path( __FILE__ ) . 'templates/box.php';
}

add_shortcode( 'eq_sidebar_ads', 'eq_ads_sidebar_ad_shortcode' );

function eq_mobile_ad_shortcode( $atts ){
    
    return include plugin_dir_path( __FILE__ ) . 'templates/banner.php';
}
add_shortcode( 'eq_mobile_ads', 'eq_mobile_ad_shortcode' );

function eq_inline_ad_shortcode( $atts ){

     $a = shortcode_atts( array(
        'align' => $atts['align']
    ), $atts );

    $ad_alignment = '';
    switch($atts['align']){
        case 'left':
            $ad_alignment = 'style="float: left"';
        break;
        case 'right':
            $ad_alignment = 'style="float: right"';
        break;
        case 'center':
            $ad_alignment = 'style="float: none; width: 100%;"';
        break;
        default:
            $ad_alignment='';
        break;
    }

    return include plugin_dir_path( __FILE__ ) . 'templates/in-story.php';
}

add_shortcode( 'eq_inline_ads', 'eq_inline_ad_shortcode' );

