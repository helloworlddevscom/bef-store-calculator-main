<?php
/**
 * The default template for Resources Page from Full Width Template
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
			
			<div class="alignwide">

				<!-- PUBLICATIONS -->			
				<?php
				$args = array(
				    'post_type' => 'resources',
				    'tax_query' => array(
				        array(
				            'taxonomy' => 'resource_types',
				            'field'    => 'slug',
				            'terms'    => 'publications',
				        ),
				    ),
				);
				$the_query = new WP_Query( $args );
				
				if ( $the_query->have_posts() ) : ?>
				
					<div class="resource-type-wrapper">

					    <h4>Publications</h4>
					    
					    <div class="resource-grid-wrapper resource-grid-pubs">
					 
					    <!-- the loop -->
					    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
					    
					    	<?php
							$file = get_field('internal_file');
							if( $file ): ?>
								<a href="<?php echo $file['url']; ?>" target="_blank">
							<?php endif; ?>
					    	
						    	<div>
							    	
							    	<?php // check if the post or page has a Featured Image assigned to it.
									if ( has_post_thumbnail() ) {
									    the_post_thumbnail();
									} ?>
								
									<?php if( get_field('video_embed') ): ?>
										<div><?php the_field('video_embed'); ?></div>
									<?php endif; ?>
						    
							        <div><?php the_title(); ?></div>
						        
						    	</div>
						    	
						    <?php if( $file ): ?>
								</a>
						    <?php endif; ?>
					    	
					    <?php endwhile; ?>
					    <!-- end of the loop -->
					 
					    <?php wp_reset_postdata(); ?>
					    
					    </div><!-- resource-grid-wrapper -->
					    
					</div><!-- resource-type-wrapper -->
				 
				<?php endif; ?>
				
				
				<!-- VIDEOS -->			
				<?php
				$args = array(
				    'post_type' => 'resources',
				    'tax_query' => array(
				        array(
				            'taxonomy' => 'resource_types',
				            'field'    => 'slug',
				            'terms'    => 'videos',
				        ),
				    ),
				);
				$the_query = new WP_Query( $args );
				
				if ( $the_query->have_posts() ) : ?>

					<div class="resource-type-wrapper">
						
					    <h4>Videos</h4>
					    
					    <div class="resource-grid-wrapper resource-grid-vids">
					 
					    <!-- the loop -->
					    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
					    	
					    	<div class="resource-video">
							
								<?php if( get_field('video_embed') ): ?>
									<div class="resource-video-frame-wrapper"><?php the_field('video_embed'); ?></div>
								<?php endif; ?>
					    
						        <div><?php the_title(); ?></div>
					        
					    	</div>
					    	
					    <?php endwhile; ?>
					    <!-- end of the loop -->
					 
					    <?php wp_reset_postdata(); ?>
					    
					    </div><!-- resource-grid-wrapper -->
					
					</div><!-- resource-type-wrapper -->
				 
				<?php endif; ?>
				
				
				<!-- NEWSLETTERS -->			
				<?php
				$args = array(
				    'post_type' => 'resources',
				    'tax_query' => array(
				        array(
				            'taxonomy' => 'resource_types',
				            'field'    => 'slug',
				            'terms'    => 'newsletters',
				        ),
				    ),
				);
				$the_query = new WP_Query( $args );
				
				if ( $the_query->have_posts() ) : ?>
				
					<div class="resource-type-wrapper">

					    <h4>Newsletters</h4>
					    
					    <div class="resource-grid-wrapper resource-grid-news">
					 
					    <!-- the loop -->
					    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
					    
					    	<!-- Output The Post Content -->
					    	
					    	<div class="resource-news-row">
					    	
						    	<div class="resource-news-date">
							    
								    <?php if( get_field('date') ): ?>
										<span class="resource-date newsletter-date"><strong><?php the_field('date'); ?></strong></span>
									<?php endif; ?>
								
						    	</div>
						    	
						    
								<div class="resource-news-title">
							    
								    <?php if( get_field('internal_file') ): ?>
									    
										<?php $file = get_field('internal_file'); ?>		
										<a href="<?php echo $file['url']; ?>" target="_blank">
										
									<?php elseif( get_field('external_link') ): ?>
										
										<a href="<?php the_field('external_link'); ?>" target="_blank">
									
									<?php else: ?>
											
										<a href="#">
										
									<?php endif; ?>
								    
									    	<span class="resource-title newsletter-title"><?php the_title(); ?></span>
								    	</a>
							    </div>
							    
							</div>
					    	
					    <?php endwhile; ?>
					    <!-- end of the loop -->
					 
					    <?php wp_reset_postdata(); ?>
					    
					    </div><!-- resource-grid-wrapper -->
					    
					</div><!-- resource-type-wrapper -->
				 
				<?php endif; ?>
				
				<!-- MEDIA HIGHLIGHTS -->			
				<?php
				$args = array(
				    'post_type' => 'resources',
				    'tax_query' => array(
				        array(
				            'taxonomy' => 'resource_types',
				            'field'    => 'slug',
				            'terms'    => 'media-highlights',
				        ),
				    ),
				);
				$the_query = new WP_Query( $args );
				
				if ( $the_query->have_posts() ) : ?>
				
					<div class="resource-type-wrapper">

					    <h4>Media Highlights</h4>
					    
					    <div class="resource-grid-wrapper resource-grid-news">
					 
					    <!-- the loop -->
					    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
					    
					    	<!-- Output The Post Content -->
					    	
					    	<div class="resource-news-row">
					    	
						    	<div class="resource-news-date">
							    
								    <?php if( get_field('date') ): ?>
										<span class="resource-date newsletter-date"><strong><?php the_field('date'); ?></strong></span>
									<?php endif; ?>
								
						    	</div>
						    	
						    
								<div class="resource-news-title">
							    
								    <?php if( get_field('internal_file') ): ?>
									    
										<?php $file = get_field('internal_file'); ?>		
										<a href="<?php echo $file['url']; ?>" target="_blank">
										
									<?php elseif( get_field('external_link') ): ?>
										
										<a href="<?php the_field('external_link'); ?>" target="_blank">
									
									<?php else: ?>
											
										<a href="#">
										
									<?php endif; ?>
								    
									    	<span class="resource-title newsletter-title"><?php the_title(); ?></span>
								    	</a>
							    </div>
							    
							</div>
					    	
					    <?php endwhile; ?>
					    <!-- end of the loop -->
					 
					    <?php wp_reset_postdata(); ?>
					    
					    </div><!-- resource-grid-wrapper -->
					    
					</div><!-- resource-type-wrapper -->
				 
				<?php endif; ?>
				
				
			</div>
			

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
