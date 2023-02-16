<?php
/**
 * The default template for Home v2 from Full Width Template
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
			
			<div class="alignfull home-intro-section">
				
				<div class="home-intro-section-inner">
			
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
				
				</div>
			
			</div>
			
			<div class="alignfull home-rows-section">
				
				<div id="home-2-scroll-tab-wrapper">
					<div id="home-2-scroll-tab">Scroll</div>
				</div>
				
				<?php if( have_rows('alternating_rows') ): ?>
				    
				    <div class="home-alt-rows-wrapper">
				    
				    <?php while( have_rows('alternating_rows') ): the_row(); 
				        $image = get_sub_field('image');
				        $term = get_sub_field('solution_category');
				        $counter +=1;
				        if ($term->slug =="water" ) {
					        $row_bg="bef-blue-bg";
				        } elseif ($term->slug =="carbon" ) {
					        $row_bg="bef-green-bg";
				        } elseif ($term->slug =="energy" ) {
					        $row_bg="bef-orange-bg";
				        }
				        ?>
				        <div class="home-alt-row home-alt-row-<?php echo $counter; ?>">
					        
					        <div class="home-alt-row-inner">
						        
						        <a href="<?php the_sub_field('link'); ?>">
						        
							        <div class="home-alt-row-content">
						        
								        <div class="home-alt-row-cell home-alt-row-cell-image" style="background-image: url(<?php echo esc_url($image['url']); ?>)">
								        
								        </div><!-- .home-alt-row-cell -->
							            
							            <div class="home-alt-row-cell home-alt-row-cell-text <?php echo $row_bg; ?>">
								            
								            <div class="home-alt-row-cell-arrow">
			
									            <div class="home-alt-row-cell-arrow-image" style="background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/BEF-Button-Right-Arrow@2x.png);">
												
									            </div>
								            </div>
			
								            <div>
									            <div class="home-alt-rows-cat"><?php echo $term->name; ?></div>
									            <h4><?php the_sub_field('title'); ?></h4>
								            </div><!-- .home-alt-row-cell -->
										
										</div><!-- .home-alt-row-cell -->
										
							        </div><!-- .home-alt-row-content -->
							        
						        </a>
								
					        </div><!-- .home-alt-row-inner -->
				            
				        </div><!-- .home-alt-row -->
				    <?php endwhile; ?>
				    
				    </div><!-- .home-alt-rows-wrapper -->
				    
				<?php endif; ?>
				
			</div>
			
			<div class="alignfull home-balance-section">
				
				<div class="alignwide">
				
					<h4>Balance Your Footprint</h4>
					
					<?php if( have_rows('balance_your_footprint_tiles') ): ?>
							
					    <div class="feature-tiles">
						    
						    <?php while( have_rows('balance_your_footprint_tiles') ): the_row(); 
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
										            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
									        	</div>
									            
									            <h5><?php the_sub_field('title'); ?></h5>
									            
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
