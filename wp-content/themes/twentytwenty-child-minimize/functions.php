<?php
/* enqueue scripts and style from parent theme */        
function twentytwenty_styles() {
	wp_enqueue_style( 'parent', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'twentytwenty_styles');


// REMOVE INLINE STYLES
// From: https://wordpress.org/support/topic/remove-inline-styles-5/
add_action( 'wp_enqueue_scripts', function() {
	$styles = wp_styles();
	$styles->add_data( 'twentytwenty-style', 'after', array() );
}, 20 );


// LOGIN SCREEN > LOGO
function my_login_logo() { ?>
    <style type="text/css">
	    body.login div#login::before {
		    display: block;
		    width: 100%;
		    height: 160px;
		    content: "";
		    background-color: #FFF;
		    box-sizing: border-box;
		    padding: 20px;
		    background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/bef-logo-3COLOR-RGB-small_copy.png); /* RETINA SIZED */
            background-size: contain;
		    background-position: center center;
		    background-repeat: no-repeat;
		    background-origin: content-box, content-box;
	    }
	    body.login div#login h1::before {
		    content: "website by";
		    font-size: 12px;
		    font-weight: 300;
	    }
	    body.login div#login h1 {
		    margin-top: 10px;
	    }
        body.login div#login h1 a {
	        width: 100%;
	        height: 50px;
	        margin: 0px auto 25px auto;
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/screenshot.png); /* RETINA SIZED */
            background-size: cover;
            background-size: 270px auto;
		    background-position: center center;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


// LOGIN SCREEN > URL AND IMAGE TITLE
function my_login_logo_url() {
    return 'https://minimize.com/';
}
add_filter( 'login_headerurl', 'my_login_logo_url' );
function my_login_logo_url_title() {
    return "Minimize";
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


// ADMIN CSS
function minimize_guten_block_editor_assets() {
	wp_enqueue_style( 'typography-styles', '//cloud.typography.com/6375996/6588612/css/fonts.css' );
    wp_enqueue_style(
        'minimize-editor-style',
        get_stylesheet_directory_uri() . "/editor.css",
        array(),
        '1.0'
    );
}
add_action('enqueue_block_editor_assets', 'minimize_guten_block_editor_assets');


// RENAME MENU LABEL/TITLE FOR AWSM TEAM PLUGIN
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
    $labels->menu_name = 'Team';

    return $labels;
}


// BEGIN - CUSTOM POST TYPES
function codex_custom_init() {
    
    // NEXT POST TYPE
    register_post_type('programs', array(
        'labels' => array(
            'name' => __( 'Programs' ),
            'singular_name' => __( 'Program' ),
            'add_new' => __( 'Add New Program' ),
            'add_new_item' => __( 'Add New Program' ),
            'new_item' => __( 'Add New Program' ),
            'edit_item' => __( 'Edit Program' )
        ),
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail'
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-admin-site-alt3',
        'show_in_rest' => false
    ) );
    
    // NEXT POST TYPE
    register_post_type('partners', array(
        'labels' => array(
            'name' => __( 'Partners' ),
            'singular_name' => __( 'Partner' ),
            'add_new' => __( 'Add New Partner' ),
            'add_new_item' => __( 'Add New Partner' ),
            'new_item' => __( 'Add New Partner' ),
            'edit_item' => __( 'Edit Partner' )
        ),
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail'
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-star-filled',
        'show_in_rest' => false
    ) );
    
    // NEXT POST TYPE
    register_post_type('testimonials', array(
        'labels' => array(
            'name' => __( 'Testimonials' ),
            'singular_name' => __( 'Testimonial' ),
            'add_new' => __( 'Add New Testimonial' ),
            'add_new_item' => __( 'Add New Testimonial' ),
            'new_item' => __( 'Add New Testimonial' ),
            'edit_item' => __( 'Edit Testimonial' )
        ),
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail'
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-testimonial',
        'show_in_rest' => false
    ) );
    
    // NEXT POST TYPE
    register_post_type('case-studies', array(
        'labels' => array(
            'name' => __( 'Case Studies' ),
            'singular_name' => __( 'Case Study' ),
            'add_new' => __( 'Add New Case Study' ),
            'add_new_item' => __( 'Add New Case Study' ),
            'new_item' => __( 'Add New Case Study' ),
            'edit_item' => __( 'Edit Case Study' )
        ),
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail'
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-book',
        'show_in_rest' => false
    ) );
    
    // NEXT POST TYPE
    register_post_type('resources', array(
        'labels' => array(
            'name' => __( 'Resources' ),
            'singular_name' => __( 'Resource' ),
            'add_new' => __( 'Add New Resource' ),
            'add_new_item' => __( 'Add New Resource' ),
            'new_item' => __( 'Add New Resource' ),
            'edit_item' => __( 'Edit Resource' )
        ),
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail'
        ),
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-paperclip',
        'show_in_rest' => false
    ) );
    
    // NEXT POST TYPE
    register_post_type('projects', array(
        'labels' => array(
            'name' => __( 'Projects' ),
            'singular_name' => __( 'Project' ),
            'add_new' => __( 'Add New Project' ),
            'add_new_item' => __( 'Add New Project' ),
            'new_item' => __( 'Add New Project' ),
            'edit_item' => __( 'Edit Project' )
        ),
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail'
        ),
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-portfolio',
        'show_in_rest' => false
    ) );

    // IMPORTANT: Remember this line!
    flush_rewrite_rules( false );
}
// INITIALIZE CUSTOM POST TYPE(S)
add_action('init', 'codex_custom_init', 1);


// BEGIN - CUSTOM TAXONOMIES 
add_action( 'init', 'build_taxonomies', 0 ); 
function build_taxonomies() { 
     
    // PROJECT CATEGORIES 
    $labels = array( 
        'name'              => _x( 'Program Solutions', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Solution', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Solutions' ), 
        'all_items'         => __( 'All Solutions' ), 
        'parent_item'       => __( 'Parent Solution' ), 
        'parent_item_colon' => __( 'Parent Solution:' ), 
        'edit_item'         => __( 'Edit Solution' ), 
        'update_item'       => __( 'Update Solution' ), 
        'add_new_item'      => __( 'Add New Solution' ), 
        'new_item_name'     => __( 'New Solution Name' ), 
        'menu_name'         => __( 'Program Solutions' ), 
    ); 
    $args = array( 
        'hierarchical'      => true, 
        'labels'            => $labels, 
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true, 
        'rewrite'           => array( 'slug' => 'solutions' ), 
        'public' => true, 
    ); 
    register_taxonomy( 'solutions', 'programs', $args );
    
    // NEXT TAXONOMY 
    $labels = array( 
        'name'              => _x( 'Resource Types', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Resource Type', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Resource Types' ), 
        'all_items'         => __( 'All Resource Types' ), 
        'parent_item'       => __( 'Parent Resource Type' ), 
        'parent_item_colon' => __( 'Parent Resource Type:' ), 
        'edit_item'         => __( 'Edit Resource Type' ), 
        'update_item'       => __( 'Update Resource Type' ), 
        'add_new_item'      => __( 'Add New Resource Type' ), 
        'new_item_name'     => __( 'New Resource Type Name' ), 
        'menu_name'         => __( 'Resource Types' ), 
    ); 
    $args = array( 
        'hierarchical'      => true, 
        'labels'            => $labels, 
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true, 
        'rewrite'           => array( 'slug' => 'resource-types' ), 
        'public' => false, 
    ); 
    register_taxonomy( 'resource_types', 'resources', $args );
    
    // NEXT TAXONOMY 
    $labels = array( 
        'name'              => _x( 'Project Solution Types', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Solution Type', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Solution Types' ), 
        'all_items'         => __( 'All Solution Types' ), 
        'parent_item'       => __( 'Parent Solution Type' ), 
        'parent_item_colon' => __( 'Parent Solution Type:' ), 
        'edit_item'         => __( 'Edit Solution Type' ), 
        'update_item'       => __( 'Update Solution Type' ), 
        'add_new_item'      => __( 'Add New Solution Type' ), 
        'new_item_name'     => __( 'New Solution Type' ), 
        'menu_name'         => __( 'Project Solution Types' ), 
    ); 
    $args = array( 
        'hierarchical'      => true, 
        'labels'            => $labels, 
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true, 
        'rewrite'           => array( 'slug' => 'solution-types' ), 
        'public' => false, 
    ); 
    register_taxonomy( 'solution_types', 'projects', $args );
    
    // NEXT TAXONOMY 
    $labels = array( 
        'name'              => _x( 'Project Types', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Project Type', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Project Types' ), 
        'all_items'         => __( 'All Project Types' ), 
        'parent_item'       => __( 'Parent Project Type' ), 
        'parent_item_colon' => __( 'Parent Project Type:' ), 
        'edit_item'         => __( 'Edit Project Type' ), 
        'update_item'       => __( 'Update Project Type' ), 
        'add_new_item'      => __( 'Add New Project Type' ), 
        'new_item_name'     => __( 'New Project Type' ), 
        'menu_name'         => __( 'Project Types' ), 
    ); 
    $args = array( 
        'hierarchical'      => true, 
        'labels'            => $labels, 
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true, 
        'rewrite'           => array( 'slug' => 'project-types' ), 
        'public' => false, 
    ); 
    register_taxonomy( 'project_types', 'projects', $args );
    
    // NEXT TAXONOMY 
    $labels = array( 
        'name'              => _x( 'Regions', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Region', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Regions' ), 
        'all_items'         => __( 'All Regions' ), 
        'parent_item'       => __( 'Parent Region' ), 
        'parent_item_colon' => __( 'Parent Region:' ), 
        'edit_item'         => __( 'Edit Region' ), 
        'update_item'       => __( 'Update Region' ), 
        'add_new_item'      => __( 'Add New Region' ), 
        'new_item_name'     => __( 'New Region' ), 
        'menu_name'         => __( 'Regions' ), 
    ); 
    $args = array( 
        'hierarchical'      => true, 
        'labels'            => $labels, 
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true, 
        'rewrite'           => array( 'slug' => 'regions' ), 
        'public' => false, 
    ); 
    register_taxonomy( 'regions', 'projects', $args );
    
    // NEXT TAXONOMY 
    $labels = array( 
        'name'              => _x( 'Standard / Verification Types', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Standard / Verification Type', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Standard / Verification Types' ), 
        'all_items'         => __( 'All Standard / Verification Types' ), 
        'parent_item'       => __( 'Parent Standard / Verification Type' ), 
        'parent_item_colon' => __( 'Parent Standard / Verification Type:' ), 
        'edit_item'         => __( 'Edit Standard / Verification Type' ), 
        'update_item'       => __( 'Update Standard / Verification Type' ), 
        'add_new_item'      => __( 'Add New Standard / Verification Type' ), 
        'new_item_name'     => __( 'New Standard / Verification Type Name' ), 
        'menu_name'         => __( 'Standard / Verification Types' ), 
    ); 
    $args = array( 
        'hierarchical'      => true, 
        'labels'            => $labels, 
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true, 
        'rewrite'           => array( 'slug' => 'standard-verification-types' ), 
        'public' => false, 
    ); 
    register_taxonomy( 'standard_verification_types', 'projects', $args );
  
} 
add_action( 'init', 'interconnect_taxonomies', 0 ); 
function interconnect_taxonomies() { 
    register_taxonomy_for_object_type( 'solutions', 'programs' );
    register_taxonomy_for_object_type( 'resource_types', 'resources' );
    register_taxonomy_for_object_type( 'solution_types', 'projects' );
    register_taxonomy_for_object_type( 'project_types', 'projects' );
    register_taxonomy_for_object_type( 'regions', 'projects' );
    register_taxonomy_for_object_type( 'standard_verification_types', 'projects' );
}



// Editor access to Menus only under Appearance
// From: (Author = Millionloeaves) https://wordpress.org/support/topic/allow-editors-to-modify-menus/
// Allow editors to see access the Menus page under Appearance but hide other options
// Note that users who know the correct path to the hidden options can still access them
function editor_access_menu() {
 	$user = wp_get_current_user();
	
	// Check if the current user is an Editor
	if ( in_array( 'editor', (array) $user->roles ) ) {
		
		// They're an editor, so grant the edit_theme_options capability if they don't have it
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			$role_object = get_role( 'editor' );
			$role_object->add_cap( 'edit_theme_options' );
		}
		
		// Hide the Themes page
	    remove_submenu_page( 'themes.php', 'themes.php' );
 
	    // Hide the Widgets page
	    remove_submenu_page( 'themes.php', 'widgets.php' );

	    // Hide the Customize page
	    remove_submenu_page( 'themes.php', 'customize.php' );
 
	    // Remove Customize from the Appearance submenu
	    global $submenu;
	    unset($submenu['themes.php'][6]);
	    
	    // *** The following from separate resource: https://wordpress.stackexchange.com/a/190184
	    foreach($submenu['themes.php'] as $menu_index => $theme_menu){
	        if($theme_menu[0] == 'Header' || $theme_menu[0] == 'Background')
	        unset($submenu['themes.php'][$menu_index]);
	    }
	}
}
 
add_action('admin_menu', 'editor_access_menu', 10);


/** https://www.billerickson.net/disabling-gutenberg-certain-templates/
/**
 * Disable Editor
 *
 * @package      ClientName
 * @author       Bill Erickson
 * @since        1.0.0
 * @license      GPL-2.0+
**/

/**
 * Templates and Page IDs without editor
 *
 */
function ea_disable_editor( $id = false ) {

	$excluded_templates = array(
		'templates/modules.php',
		'templates/contact.php',
		'templates/template-home-v2.php',
		'templates/template-who-we-are.php',
		'templates/template-resources.php',
		'templates/template-portfolio.php',
		'templates/template-contact.php',
		'templates/template-careers.php',
		'templates/template-home-store.php'
	);

	$excluded_ids = array(
		// get_option( 'page_on_front' )
	);

	if( empty( $id ) )
		return false;

	$id = intval( $id );
	$template = get_page_template_slug( $id );

	return in_array( $id, $excluded_ids ) || in_array( $template, $excluded_templates );
}

/**
 * Disable Gutenberg by template
 *
 */
function ea_disable_gutenberg( $can_edit, $post_type ) {

	if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
		return $can_edit;

	if( ea_disable_editor( $_GET['post'] ) )
		$can_edit = false;

	return $can_edit;

}
add_filter( 'gutenberg_can_edit_post_type', 'ea_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'ea_disable_gutenberg', 10, 2 );

/**
 * Disable Classic Editor by template
 *
 */
function ea_disable_classic_editor() {

	$screen = get_current_screen();
	if( 'page' !== $screen->id || ! isset( $_GET['post']) )
		return;

	if( ea_disable_editor( $_GET['post'] ) ) {
		remove_post_type_support( 'page', 'editor' );
	}

}
add_action( 'admin_head', 'ea_disable_classic_editor' );