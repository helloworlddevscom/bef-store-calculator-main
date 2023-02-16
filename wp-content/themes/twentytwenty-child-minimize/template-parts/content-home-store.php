<?php
/**
 * The default template for Home Store from Full Width Template
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
			
			
			<div class="alignfull home-store-your-section">
				
				<div class="alignwide">
					
					<?php if( have_rows('your_section_tiles') ): ?>
							
					    <div class="your-section-tiles">
						    
						    <?php while( have_rows('your_section_tiles') ): the_row(); 
						        $image = get_sub_field('icon');
						        $image_height = get_sub_field('icon_height');
						        $link = get_sub_field('first_button');
						        $link_two = get_sub_field('second_button');
						        ?>

							    	<div class="your-section-tile">
								        
								        <div class="your-section-tile-inner">
									        
								        	<div class="icon-wrapper">						
									            <img style="height: <?php echo esc_html( $image_height ); ?>px;" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
								        	</div>
								            
								            <div><h4><?php the_sub_field('title'); ?></h4></div>
								            
								            <div><p><?php the_sub_field('text'); ?></p></div>
								            
								            <div class="your-section-buttons-wrapper">
									            
									            <?php if( $link ): 
											        $link_url = $link['url'];
												    $link_title = $link['title'];
												    $link_target = $link['target'] ? $link['target'] : '_self';
										        ?>
										        	<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button class="button-your-section"><?php echo esc_html( $link_title ); ?></button></a>
											    <?php endif; ?>
											    
											    <?php if( $link_two ): 
											        $link_url = $link_two['url'];
												    $link_title = $link_two['title'];
												    $link_target = $link_two['target'] ? $link_two['target'] : '_self';
										        ?>
										        	<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button class="button-your-section"><?php echo esc_html( $link_title ); ?></button></a>
											    <?php endif; ?>
									            
								        	</div>
								            
								            
								        </div><!-- feature-tile-inner -->
							            
							        </div><!-- feature-tile -->
						        
						    <?php endwhile; ?>
					    
					    </div><!-- feature-tiles -->
					    
					<?php endif; ?>
				
				</div><!-- alignwide -->		

			</div>
			
			
			
			<div class="alignfull home-balance-section">
				
				<div class="alignwide">
					
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
