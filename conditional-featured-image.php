<?php
/*
* Plugin Name:     Conditional Featured Image
* Plugin URI:      https://github.com/Zee-Nana/Conditionally-Display-image-on-postpage
* Description:     Allows you to choose if the featured image should be shown in the single view for each post or page.
* Version:         1.0
* Author:          Zainab Abdulraheem
* Author URI:      https://github.com/Zee-Nana
* License:         GPL2
*/

 // If this file is called directly, abort.
 if (!defined( 'ABSPATH' ) ) { 
	die;
}

class cfi_display_featured_image{

};
// Add custom meta box for displaying the featured image option
function cfi_add_featured_image_meta_box() {
    add_meta_box(
        'cfi_featured_image_meta_box',
        'Conditional Featured Image',
        'cfi_render_featured_image_meta_box',
        'post',
        'side',
        'high'
    );
    add_meta_box(
        'cfi_featured_image_meta_box',
        'Conditional Featured Image',
        'cfi_render_featured_image_meta_box',
        'page',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'cfi_add_featured_image_meta_box');

// Render the meta box HTML
function cfi_render_featured_image_meta_box($post) {
    $display_featured_image = get_post_meta($post->ID, 'cfi_display_featured_image', true);
    wp_nonce_field('cfi_save_featured_image', 'cfi_featured_image_nonce');

    echo '<p><label for="cfi_display_featured_image">';
    echo '<input type="checkbox" id="cfi_display_featured_image" name="cfi_display_featured_image" value="1" ' . checked($display_featured_image, 1, false) . '/>';
    echo ' Display featured image on single view</label></p>';
}

// Save the meta box data
function cfi_save_featured_image_meta_box($post_id) {
    if (!isset($_POST['cfi_featured_image_nonce']) || !wp_verify_nonce($_POST['cfi_featured_image_nonce'], 'cfi_save_featured_image')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['cfi_display_featured_image']) && $_POST['cfi_display_featured_image'] == 1) {
        update_post_meta($post_id, 'cfi_display_featured_image', 1);
    } else {
        delete_post_meta($post_id, 'cfi_display_featured_image');
    }
}
add_action('save_post', 'cfi_save_featured_image_meta_box');

// Filter the featured image display on single view
function cfi_filter_featured_image($html, $post_id, $post_thumbnail_id, $size, $attr) {
    $display_featured_image = get_post_meta($post_id, 'cfi_display_featured_image', true);
    
    if ($display_featured_image && is_single()) {
        return $html;
    }
    
    return '';
}
add_filter('post_thumbnail_html', 'cfi_filter_featured_image', 10, 5);

if ( class_exists( 'cfi_display_featured_image' ) ){
    $Cfi_Display_Featured_Image = new cfi_display_featured_image();
    }

function activate(){
    // generate a CPT
    $this->custom_post_type();
    //flush rewrite rules
    flush_rewrite_rules();
}

function deactivate(){
    //flush rewrite rules
    flush_rewrite_rules();
}

//activation
//register_activation_hook( __FILE__, array($learnPlugin, 'activate') );


//deactivate
//register_deactivation_hook( __FILE__, array($learnPlugin, 'deactivate') );

