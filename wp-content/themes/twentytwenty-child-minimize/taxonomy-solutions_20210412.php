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
			
			
			<div class="has-text-align-center tax-solution-term-description"><?php echo term_description(); ?></div>
		
		</div><!-- entry-content -->

		<?php
	}

	if ( have_posts() ) {

		$i = 0; ?>
		
		<div class="wp-block-columns alignwide solutions-grid-group">

		<?php while ( have_posts() ) {
			$i++;

			the_post(); ?>
			
				<div class="wp-block-column">
					
					<div class="wp-block-group">
					
						<div class="tax-solution-row tax-solution-row-1">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
						</div>
						
						<div class="tax-solution-row tax-solution-row-2">
							<?php if( get_field('intro_description') ): ?>
								<p><?php the_field('intro_description'); ?></p>
							<?php endif; ?>
						</div>
						
						<div class="tax-solution-row tax-solution-row-3">
							<?php if( get_field('intro_partners_with') ): ?>
								<div class="carousel-cell-testimonial-cite gotham-bold">PARTNERS WITH:</div>
								<h6><?php the_field('intro_partners_with'); ?></h6>
							<?php endif; ?>
						</div>
						
						<div class="tax-solution-row tax-solution-row-4">
							<a class="solution-feature-button" href="<?php the_permalink(); ?>">
								<span>Learn More</span>
							</a>
						</div>
					
					</div>
					
				</div>
			
			<?php
			
		} ?>
		
		</div>
		
		<?php
	} 
	
	?>

	<?php get_template_part( 'template-parts/pagination' ); ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
