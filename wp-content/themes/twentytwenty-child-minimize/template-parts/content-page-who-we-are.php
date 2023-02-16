<?php
/**
 * The default template for Who We Are Page from Full Width Template
 *
 * Used for both singular and index.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php

	get_template_part( 'template-parts/entry-header' );

	?>

	<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">

		<div class="entry-content">
			
			<?php 
			$image = get_field('hero_banner_image');
			if( !empty( $image ) ): ?>
				<?php $hero_bg = esc_url($image['url']); ?>
			<?php else: ?>
				<?php $hero_bg = "https://befnew1.wpengine.com/wp-content/uploads/2020/02/road-1072823_1920.jpg"; ?>
			<?php endif; ?>
			
			
			<div class="wp-block-cover alignfull" style="background-image: url( <?php echo $hero_bg; ?>  )">
				<div class="wp-block-cover__inner-container">
					<p class="has-text-align-center has-large-font-size"></p>
				</div>
			</div>
			
			
			<div class="alignfull solutions-intro-section">
			
				<?php if( get_field('intro_header') ): ?>
					<h2><?php the_field('intro_header'); ?></h2>
				<?php endif; ?>
				
				<?php 
				$image = get_field('intro_icon');
				if( !empty( $image ) ): ?>
				    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
				<?php endif; ?>
				
				<?php if( get_field('intro_subheader') ): ?>
					<h5><?php the_field('intro_subheader'); ?></h5>
				<?php endif; ?>
				
				<?php if( get_field('intro_body') ): ?>
					<p><?php the_field('intro_body'); ?></p>
				<?php endif; ?>
			
			</div>
			
			
			<div id="core-values" class="alignfull core-values-section">
				
				<?php if( get_field('core_values_header') ): ?>
					<h5><?php the_field('core_values_header'); ?></h5>
				<?php endif; ?>
				
				<?php 
				$image = get_field('core_values_image');
				if( !empty( $image ) ): ?>
				    <img src="<?php echo esc_url($image['url']); ?>" style="height: <?php the_field('core_values_image_height'); ?>; width: auto; margin: 0 auto;" alt="<?php echo esc_attr($image['alt']); ?>" />
				<?php endif; ?>
			
			</div>
			
			
			<div class="alignfull great-place-section">
				
				<?php if( get_field('great_place_to_work_header') ): ?>
					<h2><?php the_field('great_place_to_work_header'); ?></h2>
				<?php endif; ?>
				
				<?php if( get_field('great_place_to_work_text') ): ?>
					<p><?php the_field('great_place_to_work_text'); ?></p>
				<?php endif; ?>
				
				<?php if( have_rows('great_place_to_work_images') ): ?>
					<?php $count = count(get_field('great_place_to_work_images')); ?>
				    <div class="great-place-images-wrapper">
					    <style>
						    .great-place-images-wrapper div {
							    flex-basis: calc(100% / <?php echo $count ?>);
						    }
						</style>
				    <?php while( have_rows('great_place_to_work_images') ): the_row(); 
				        $image = get_sub_field('image');
				        ?>
				        <div>
				            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
				        </div>
				    <?php endwhile; ?>
				    </div>
				<?php endif; ?>
			
			</div>
			
			

			<div id="team" class="wp-block-group alignfull has-background" style="background-color:#58585a">
				
				<div class="wp-block-group__inner-container">
				
					<?php if( get_field('team_header') ): ?>
						<h2 class="has-text-align-center has-background-color has-text-color"><?php the_field('team_header'); ?></h2>
					<?php endif; ?>
					
					<?php echo do_shortcode('[awsmteam id="90"]'); ?>
					
					
					
					
					<?php
					$featured_posts = get_field('board_of_directors');
					if( $featured_posts ): ?>
					    <div class="board-of-directors-section">
						    
						    <?php if( get_field('board_of_directors_header') ): ?>
								<h2 class="has-text-align-center has-background-color has-text-color"><?php the_field('board_of_directors_header'); ?></h2>
							<?php endif; ?>
						    
						    
					    <?php foreach( $featured_posts as $post ): 
					
					        // Setup this post for WP functions (variable must be named $post).
					        setup_postdata($post); ?>
					        
					        
					        <div class="board-of-director-item has-text-align-center">
					            <h3 class="director-name"><?php the_title(); ?></h3>
					            
					            
					            <?php 
						            $meta = get_post_meta(get_the_ID(), '', true);
									//print_r($meta);
									//Array ( [key_1] => Array ( [0] => value_1 ), [key_2] => Array ( [0] => value_2 ) )
									
									$key_1_value = get_post_meta( get_the_ID(), 'awsm-team-designation', true );
									// Check if the custom field has a value.
									if ( ! empty( $key_1_value ) ) { ?>
									    
									    <div class="director-title uppercase"><?php echo $key_1_value; ?></div>
									    
									<? }
					            ?>
					            
					        </div><!-- board-of-director-item -->
					    <?php endforeach; ?>
					    </div><!-- board-of-directors-section -->
					    <?php 
					    // Reset the global post object so that the rest of the page works correctly.
					    wp_reset_postdata(); ?>
					<?php endif; ?>
			
				</div><!-- wp-block-group__inner-container -->
				
			</div><!-- wp-block-group -->
			
			
			<!-- SUBSCRIBE SECTION -->			
			<?php get_template_part( 'template-parts/content-subscribe' ); ?>
			

			<?php
			if ( is_search() || ! is_singular() && 'summary' === get_theme_mod( 'blog_content', 'full' ) ) {
				the_excerpt();
			} else {
				the_content( __( 'Continue reading', 'twentytwenty' ) );
			}
			?>

		</div><!-- .entry-content -->

	</div><!-- .post-inner -->

	<div class="section-inner">
		<?php
		wp_link_pages(
			array(
				'before'      => '<nav class="post-nav-links bg-light-background" aria-label="' . esc_attr__( 'Page', 'twentytwenty' ) . '"><span class="label">' . __( 'Pages:', 'twentytwenty' ) . '</span>',
				'after'       => '</nav>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			)
		);

		edit_post_link();

		// Single bottom post meta.
		twentytwenty_the_post_meta( get_the_ID(), 'single-bottom' );

		if ( post_type_supports( get_post_type( get_the_ID() ), 'author' ) && is_single() ) {

			get_template_part( 'template-parts/entry-author-bio' );

		}
		?>

	</div><!-- .section-inner -->

	<?php

	if ( is_single() ) {

		get_template_part( 'template-parts/navigation' );

	}

	/**
	 *  Output comments wrapper if it's a post, or if comments are open,
	 * or if there's a comment number – and check for password.
	 * */
	if ( ( is_single() || is_page() ) && ( comments_open() || get_comments_number() ) && ! post_password_required() ) {
		?>

		<div class="comments-wrapper section-inner">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

		<?php
	}
	?>

</article><!-- .post -->
