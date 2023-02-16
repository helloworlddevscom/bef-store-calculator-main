<?php
/**
 * The default template for Portfolio Page from Full Width Template
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

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>" >

	<?php

	get_template_part( 'template-parts/entry-header' );

	?>

	<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">
		
		<?php 
		$solution_type = get_field('solution_type');
		$solution_type_slug = esc_html( $solution_type->slug ); ?>

		<div class="entry-content portfolio-<?php echo $solution_type_slug; ?>">
			
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
			
			<div class="alignfull solutions-nav-banner <?php echo $sol_nav_banner; ?>">
				
				<div class="solutions-nav-item current-item">
								
					<?php if( get_field('white_logo') ): ?>
					    <img src="<?php the_field('white_logo'); ?>" />
					<?php endif; ?>
					<div class="solutions-nav-item-arrow-up"></div>
					
					<a class="solutions-nav-link" href="<?php the_permalink(); ?>"></a>
				
				</div>
				
			</div>
			
			<div class="alignfull solutions-intro-section">
			
				<h2><?php the_title(); ?></h2>
			
			</div>
			
			<div class="alignfull portfolio-rows-section">
				
				<?php
				$term_args = array (
					'taxonomy' =>  'project_types',
					'order' =>  'ASC',
					'orderby' =>  'meta_value_num',
					'meta_query' => [[
						'key' => 'order',
						'type' => 'NUMERIC',
					]],
				);
					
				$_terms = get_terms( 'project_types', $term_args );
				
				foreach ($_terms as $term) :
				
				    $term_slug = $term->slug;
				    $_posts = new WP_Query( array(
		                'post_type'         => 'projects',
		                'tax_query' => array(
			                'relation' => 'AND',
		                    array(
		                        'taxonomy' => 'project_types',
		                        'field'    => 'slug',
		                        'terms'    => $term_slug,
		                    ),
		                    array(
					            'taxonomy' => 'solution_types',
					            'field'    => 'slug',
		                        'terms'    => $solution_type_slug,
					        ),
		                ),
		                'meta_key' => 'portfolio_order',
		                'orderby' => 'meta_value',
		                'order' => 'ASC'
		            ));
				
				    if( $_posts->have_posts() ) : ?>
				    
				    	<div class="portfolio-row-header">
					    	<div class="portfolio-row-header-icon">
						    	<?php
							    	$image = get_field('icon', $term);
							    ?>
							    <img src="<?php echo $image['url']; ?>">
					    	</div>
					    	<div class="portfolio-row-header-text">
						    	<h5><?php echo $term->name; ?></h5>
								<p><?php echo $term->description; ?></p>
					    	</div>
				    	</div>
				        
				        <div class="portfolio-row portfolio-row-titles">
					        
					        <div>
					            Project Name
				            </div>
				            
				            <div>
				                Region
				            </div>
				            
				            <div>
				                Description
				            </div>
				            
				            <div class="portfolio-row-col-last">
					            Standard /<br />
					            Verification*
				            </div>
					        
				        </div><!-- portfolio-row -->
				        
				        <?php while ( $_posts->have_posts() ) : $_posts->the_post(); ?>
				        
				        	<div class="portfolio-row">
					        	
					            <div>
						            <?php the_title(); ?>
					            </div>
					            
					            <div>
					                <?php
						                $term_obj_list = get_the_terms( get_the_ID(), 'regions' );
										$terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));
										echo $terms_string;
					                ?>
					            </div>
					            
					            <div>
					                <?php the_content(); ?>
					            </div>
					            
					            <div class="portfolio-row-col-last">
						            <?php
						                $term_obj_list = get_the_terms( get_the_ID(), 'standard_verification_types' );
										$terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));
										echo $terms_string;
					                ?>
					            </div>
					            
					        </div><!-- portfolio-row -->
				        <?php
				        endwhile;
				
				    endif;
				    wp_reset_postdata();
				
				endforeach;
				?>
				
				<div class="portfolio-row portfolio-row-key-and-contact">
					        
			        <div class="portfolio-row-key">
			            *Standard/Verification Key:
			            
			            <?php
							
						$_terms = get_terms( 'standard_verification_types' );
						
						foreach ($_terms as $term) : ?>

							<p class="svt-abbr svt-abbr-<?php echo $term->slug; ?>"><?php echo $term->name; ?> - <?php echo $term->description; ?></p>
						
						<?php endforeach; ?>
			            
		            </div>
		            
		            <div class="portfolio-row-contact">
		                <div class="contact-tile">
								
							<?php
							$contact_person = get_field('contact_person');
							if( $contact_person ): ?>
							
								<div class="contact-person-tiles">
									
									<?php $contact_person_picture_url = esc_url( $contact_person['contact_person_picture']['url'] ); ?>
									
									<div class="contact-person-tile" style="background-image: url( <?php echo $contact_person_picture_url; ?>  )">
									</div><!-- contact-person-tile -->
									
									<div class="contact-person-tile">
										
										
											
											<p><?php echo $contact_person['contact_person_intro']; ?></p>
										
											
												
												<a href="<?php echo esc_url( $contact_person['link']['url'] ); ?>"><div class="contact-person-tile-link-wrapper"><div class="carousel-cell-testimonial-cite gotham-bold"><?php echo $contact_person['contact_person_name_title_etc']; ?></div><div><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/BEF-Button-Right-Arrow@2x.png"></div></div><!-- contact-person-tile --></a>
											
										
										
									</div><!-- contact-person-tile -->
									
								</div><!-- contact-person-tiles -->
							
							<?php endif; ?><!-- if contact_person -->
							
						</div><!-- contact-tile -->
		            </div>
			        
		        </div><!-- portfolio-row -->
				
			</div><!-- .alignwide -->
				
	
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
