<?php
/**
 * The default template for Careers Page from Full Width Template
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
			
			
			<?php if( have_rows('in_action_tiles') ): ?>
			
				<div class="alignfull in-action-section">
					
					<div class="alignwide">
						
						
						<?php if( get_field('in_action_title') ): ?>
							<h4><?php the_field('in_action_title'); ?></h4>
						<?php endif; ?>
						
						<?php if( have_rows('in_action_tiles') ): ?>
								
						    <div class="feature-tiles">
							    
							    <?php while( have_rows('in_action_tiles') ): the_row(); 
							        $image = get_sub_field('icon');
							        $link = get_sub_field('link');
							        ?>
							        
							        	<?php if( $link ): 
									        $link_url = $link['url'];
										    $link_title = $link['title'];
										    $link_target = $link['target'] ? $link['target'] : '_self';
								        ?>
								        	<a class="feature-tile" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
									    <?php else: ?>    	
									    	<div class="feature-tile">
								        <?php endif; ?>
							        
									        <div>
										        
										        <div class="feature-tile-inner">
											        
										        	<div class="icon-wrapper">						
											            <img style="width: auto !important; height: <?php the_sub_field('icon_height'); ?>px !important" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
										        	</div>
										            
										            <div class="feature-tile-stat"><?php the_sub_field('stat'); ?></div>
										            
										            <p><?php the_sub_field('title'); ?></p>
										            
										            <p><?php the_sub_field('description'); ?></p>
										            
										        </div><!-- feature-tile-inner -->
									            
									        </div>
							        
							        <?php if( $link ): ?>
							        	</a>
							        <?php else: ?>
							        	</div>
							        <?php endif; ?>
							        
							    <?php endwhile; ?>
						    
						    </div><!-- feature-tiles -->
						    
						<?php endif; ?>
					
					</div><!-- alignwide -->		
	
				</div>
			<?php endif; ?>
			
			
			
			<div class="alignfull great-place-section">
				
				<?php if( get_field('great_place_to_work_header') ): ?>
					<h5><?php the_field('great_place_to_work_header'); ?></h5>
				<?php endif; ?>
				
					<p>
						Content here pending.
					</p>
				
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
