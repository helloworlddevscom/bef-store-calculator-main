<?php
/**
 * The template for displaying SOLUTIONS custom taxonomy archive page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content" role="main">

	<?php

	$archive_title    = '';
	$archive_subtitle = '';

	if ( is_search() ) {
		global $wp_query;

		$archive_title = sprintf(
			'%1$s %2$s',
			'<span class="color-accent">' . __( 'Search:', 'twentytwenty' ) . '</span>',
			'&ldquo;' . get_search_query() . '&rdquo;'
		);

		if ( $wp_query->found_posts ) {
			$archive_subtitle = sprintf(
				/* translators: %s: Number of search results. */
				_n(
					'We found %s result for your search.',
					'We found %s results for your search.',
					$wp_query->found_posts,
					'twentytwenty'
				),
				number_format_i18n( $wp_query->found_posts )
			);
		} else {
			$archive_subtitle = __( 'We could not find any results for your search. You can give it another try through the search form below.', 'twentytwenty' );
		}
	} elseif ( ! is_home() ) {
		$archive_title    = get_the_archive_title();
		$archive_subtitle = get_the_archive_description();
	}

	if ( $archive_title || $archive_subtitle ) {
		?>

		<header class="archive-header has-text-align-center header-footer-group">

			<div class="archive-header-inner section-inner medium">

				<?php if ( $archive_title ) { ?>
					<h1 class="archive-title"><?php echo wp_kses_post( $archive_title ); ?></h1>
				<?php } ?>

				<?php if ( $archive_subtitle ) { ?>
					<div class="archive-subtitle section-inner thin max-percentage intro-text"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></div>
				<?php } ?>

			</div><!-- .archive-header-inner -->

		</header><!-- .archive-header -->
		
		<div class="entry-content">
			
			<div class="wp-block-cover alignfull" style="background-image:url(https://befnew1.wpengine.com/wp-content/uploads/2020/02/road-1072823_1920.jpg)">
				<div class="wp-block-cover__inner-container">
					<p class="has-text-align-center has-large-font-size"></p>
				</div>
			</div>
			
			
			<div class="alignfull solutions-nav-banner">
				<div class="solutions-nav-item solutions-nav-item-title current-item">
					<p class="has-text-align-center gotham-bold"><?php echo wp_kses_post( $archive_title ); ?> Solutions Overview</p>
					<div class="solutions-nav-item-arrow-up"></div>
				</div>
				
				<?php 
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post(); ?>
						
							
								<div class="solutions-nav-item">
								
									<?php if( get_field('white_logo') ): ?>
									    <img src="<?php the_field('white_logo'); ?>" />
									<?php endif; ?>
									<div class="solutions-nav-item-arrow-up"></div>
									
									<a class="solutions-nav-link" href="<?php the_permalink(); ?>"></a>
								
								</div>	
						
					<?php } // end while
				} // end if
				?>
			</div>
			
			<?php
				// ACF FIELDS
				// get the current taxonomy term
				$term = get_queried_object();
				
				
				// vars
				$intro_header = get_field('intro_header', $term);
				$intro_subheader = get_field('intro_subheader', $term);
				
				$take_action_header = get_field('take_action_header', $term);
				
				$activate_header = get_field('activate_header', $term)
				
			?>
			
			<div class="alignfull solutions-intro-section">
			
				<?php if( get_field('intro_header', $term) ): ?>
					<h2><?php the_field('intro_header', $term); ?></h2>
				<?php endif; ?>
				
				<?php 
				$image = get_field('intro_icon');
				if( !empty( $image ) ): ?>
				    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
				<?php endif; ?>
				
				<?php if( get_field('intro_subheader', $term) ): ?>
					<h5><?php the_field('intro_subheader', $term); ?></h5>
				<?php endif; ?>
				
				<?php if( get_field('intro_body') ): ?>
					<p><?php the_field('intro_body'); ?></p>
				<?php endif; ?>
			
			</div>
			
			<div class="alignwide featured-programs-section take-action-section">
				
				<?php if( get_field('take_action_header', $term) ): ?>
					<h4><?php the_field('take_action_header', $term); ?></h4>
				<?php endif; ?>
				
				<?php
				$featured_posts = get_field('take_action_programs', $term);
				if( $featured_posts ): ?>
				
					<?php $count_take_action = count(get_field('take_action_programs', $term)); ?>
					
					<div class="great-place-images-wrapper">
					    <style>
						    .take-action-section .great-place-images-wrapper div {
							    flex-basis: calc(100% / <?php echo $count_take_action ?>);
							    flex-basis: 33.3%;
						    }
						</style>
					
				    
					    <?php foreach( $featured_posts as $post ): 
					
					        // Setup this post for WP functions (variable must be named $post).
					        setup_postdata($post); ?>
					        <div>
						        <a href="<?php the_permalink(); ?>">
						            <div class="featured-program-logo"><?php the_post_thumbnail(); ?></div>
									<div><?php the_field( 'intro_description' ); ?></div>
						        </a>
					        </div>
					    <?php endforeach; ?>
					    
					</div>
				    
				    <?php 
				    // Reset the global post object so that the rest of the page works correctly.
				    wp_reset_postdata(); ?>
				<?php endif; ?>
			
			</div>
			
			<div class="alignwide featured-programs-section activate-section">
				
				<?php if( get_field('activate_header', $term) ): ?>
					<h4><?php the_field('activate_header', $term); ?></h4>
				<?php endif; ?>
				
				<?php
				$featured_posts = get_field('activate_programs', $term);
				if( $featured_posts ): ?>
				
					<?php $count_activate = count(get_field('activate_programs', $term)); ?>
					
					<div class="great-place-images-wrapper">
					    <style>
						    .activate-section .great-place-images-wrapper div {
							    flex-basis: calc(100% / <?php echo $count_activate ?>);
							    flex-basis: 33.3%;
						    }
						</style>
					
				    
					    <?php foreach( $featured_posts as $post ): 
					
					        // Setup this post for WP functions (variable must be named $post).
					        setup_postdata($post); ?>
					        
					        <div>
						        <a href="<?php the_permalink(); ?>">
						            <div class="featured-program-logo"><?php the_post_thumbnail(); ?></div>
									<div><?php the_field( 'intro_description' ); ?></div>
					        	</a>							</div>
					        
					    <?php endforeach; ?>
					    
					</div>
				    
				    <?php 
				    // Reset the global post object so that the rest of the page works correctly.
				    wp_reset_postdata(); ?>
				<?php endif; ?>
			
			</div>
			
		
		</div><!-- entry-content -->

		
		<?php
	} 
	
	?>

	<?php get_template_part( 'template-parts/pagination' ); ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
