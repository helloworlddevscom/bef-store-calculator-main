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
        'name'              => _x( 'Solutions', 'taxonomy general name' ), 
        'singular_name'     => _x( 'Solution', 'taxonomy singular name' ), 
        'search_items'      => __( 'Search Solutions' ), 
        'all_items'         => __( 'All Solutions' ), 
        'parent_item'       => __( 'Parent Solution' ), 
        'parent_item_colon' => __( 'Parent Solution:' ), 
        'edit_item'         => __( 'Edit Solution' ), 
        'update_item'       => __( 'Update Solution' ), 
        'add_new_item'      => __( 'Add New Solution' ), 
        'new_item_name'     => __( 'New Solution Name' ), 
        'menu_name'         => __( 'Solutions' ), 
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
    register_taxonomy( 'solutions', array( 'projects', 'programs' ), $args );
  
} 
add_action( 'init', 'interconnect_taxonomies', 0 ); 
function interconnect_taxonomies() { 
    register_taxonomy_for_object_type( 'solutions', 'programs' );
}

// ACF > Adding fields to Menu Items
add_filter('wp_nav_menu_objects', 'my_wp_nav_menu_objects', 10, 2);

function my_wp_nav_menu_objects( $items, $args ) {
	
	// loop
	foreach( $items as &$item ) {
		
		// vars
		$icon = get_field('hover_content', $item);
		
		
		// append icon
		if( $icon ) {
			
			$item->title .= '<div class="menu-preview-box">'.$icon.'</div>';
			
		}
		
	}
	
	
	// return
	return $items;
	
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