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
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-portfolio',
        'show_in_rest' => false
    ) );
    
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
    register_taxonomy( 'solutions', 'projects', $args );
    register_taxonomy( 'solutions', array( 'projects', 'programs' ), $args );
  
} 
add_action( 'init', 'interconnect_taxonomies', 0 ); 
function interconnect_taxonomies() { 
    register_taxonomy_for_object_type( 'solutions', 'programs' );
}