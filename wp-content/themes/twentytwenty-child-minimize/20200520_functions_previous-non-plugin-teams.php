<?php
/* enqueue scripts and style from parent theme */        
function twentytwenty_styles() {
	wp_enqueue_style( 'parent', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'twentytwenty_styles');

function minimize_guten_block_editor_assets() {
    wp_enqueue_style(
        'minimize-editor-style',
        get_stylesheet_directory_uri() . "/editor.css",
        array(),
        '1.0'
    );
}
add_action('enqueue_block_editor_assets', 'minimize_guten_block_editor_assets');


// BEGIN - CUSTOM POST TYPES
function codex_custom_init() {
	// FIRST POST TYPE
	register_post_type('team', array(
		'labels' => array(
	        'name' => __( 'Team' ),
	        'singular_name' => __( 'Team Member' ),
	        'add_new' => __( 'Add Team Member' ),
	        'add_new_item' => __( 'Add Team Member' ),
	        'new_item' => __( 'Add Team Member' ),
	        'edit_item' => __( 'Edit Team Member' )
		),
		'supports' => array( 
	    	'title',
			'editor',
			'author',
			'thumbnail'
	    ),
		'public' => true,
		'has_archive' => false,
		'menu_icon'   => 'dashicons-admin-users',
	) );
	
	// IMPORTANT: Remember this line!
	flush_rewrite_rules( false );
}
// INITIALIZE CUSTOM POST TYPE(S)
add_action('init', 'codex_custom_init', 1);



add_filter( 'post_type_labels_awsm_team_member', 'awsm_team_member_rename_labels' );
/**
* Rename default post type to news
*
* @param object $labels
* @hooked post_type_labels_post
* @return object $labels
*/
function awsm_team_member_rename_labels( $labels )
{
    # Menu
    $labels->menu_name = 'Team v2';

    return $labels;
}