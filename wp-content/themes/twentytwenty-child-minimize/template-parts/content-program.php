<?php
/**
 * The default template for displaying content
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

<!-- GET SOME POST VARIABLES / VALUES -->
<?php 
    global $post;
    $post_slug = $post->post_name;
    //echo $post_slug;
?>

<?php
	$terms = get_the_terms( get_the_ID(), 'solutions' );
	$term = array_pop($terms);
	$termslug = $term->slug;
	$termname = $term->name;
	//echo $termslug;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<!-- LOAD FLICKITY UNCONDITIONALLY SINCE IT'S USED IN MULTIPLE LOCATIONS ON THIS TEMPLATE -->	
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/flickity/flickity.min.css" media="screen">
	<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/flickity/flickity.pkgd.min.js"></script>

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
			
			<?php if( get_field('programs_content') == 'yes_commodity' ) {
				$sol_nav_banner = "solutions-nav-banner-commodity";
			} ?>
			
			
			<div class="alignfull solutions-nav-banner <?php echo $sol_nav_banner; ?>">
				
				
				    
					<div class="solutions-nav-item solutions-nav-item-title">
					
						<p class="has-text-align-center gotham-bold"><?php echo $termname; ?> Solutions Overview</p>
						<div class="solutions-nav-item-arrow-up"></div>
						
						<a class="solutions-nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>solutions/<?php echo $termslug; ?>"></a>
					</div>
				
				
				
				
				
				<?php
					$terms = get_the_terms( get_the_ID(), 'solutions' );
					$term = array_pop($terms);
					$termslug = $term->slug;
					//echo $termslug;

					$args = array(
					    'post_type' => 'programs',
					    'tax_query' => array(
					        array(
					            'taxonomy' => 'solutions',
					            'field'    => 'slug',
					            'terms'    => $termslug,
					        ),
					    ),
					);
					
					$the_query = new WP_Query( $args ); ?>
					 
					<?php if ( $the_query->have_posts() ) : ?>
					 
					    <!-- the loop -->
					    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
					    
					    	<?php 
						    	$loopslug = get_post_field( 'post_name', get_the_ID() ); 
						    	//echo $loopslug;
					    	?>
					    	
					    	<?php if ( $post_slug == $loopslug ) {
						    	//echo "yes";
						    	$currentitem = "current-item";
					    	} else {
						    	$currentitem = "";
						    }
					    	?>
					    
					        <div class="solutions-nav-item <?php echo $currentitem; ?>">

								
								<?php if( get_field('white_logo') ): ?>
								    <img src="<?php the_field('white_logo'); ?>" />
								<?php endif; ?>
								<div class="solutions-nav-item-arrow-up"></div>
								
								<a class="solutions-nav-link" href="<?php the_permalink(); ?>"></a>
							
							</div>
					        
					    <?php endwhile; ?>
					    <!-- end of the loop -->
					 
					    <?php wp_reset_postdata(); ?>

					<?php endif; ?>
				
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
			
			
			<?php if( get_field('feature_title') ): ?>
			
				<div class="alignfull solutions-features-section">
				
					<div class="alignwide">
						
						<h4><?php the_field('feature_title'); ?></h4>
						
						
						<?php if( have_rows('feature_tiles') ): ?>
						
						    <div class="feature-tiles">
							    
							    <?php $tile_count = count(get_field('feature_tiles')); ?>
							    
							    <?php while( have_rows('feature_tiles') ): the_row(); 
							        $image = get_sub_field('icon');
							        ?>
							        
							        <div class="feature-tile feature-tile-<?php echo $tile_count; ?>">
								        
							            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
							            
							            <h5><?php the_sub_field('title'); ?></h5>
							            
							            <p><?php the_sub_field('description'); ?></p>
							            
							        </div><!-- feature-tile -->
							        
							    <?php endwhile; ?>
							    
							    <?php if( $tile_count == 3 ): ?>
						        	<style>
							        	@media ( min-width: 700px ) {
								        	
								        	.solutions-features-section .feature-tiles {
									        	grid-template-columns: 1fr 1fr 1fr;
								        	}
										
										}
									
									</style>
								<?php endif; ?>
						    
						    </div><!-- feature-tiles -->
						    
						<?php endif; ?>
						
						<?php if( have_rows('feature_buttons') ): ?>
						
						    <div class="feature-buttons">
							    
							    <?php while( have_rows('feature_buttons') ): the_row(); ?>
							        
							        <div class="feature-button">
	
								        <?php 
										$link = get_sub_field('button');
										if( $link ): 
										    $link_url = $link['url'];
										    $link_title = $link['title'];
										    $link_target = $link['target'] ? $link['target'] : '_self';
										    ?>
										    <a class="solution-feature-button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><span><?php echo esc_html( $link_title ); ?></span><span><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/BEF-Button-Right-Arrow@2x.png"></span></a>
										<?php endif; ?>
										
										
							            
							        </div><!-- feature-tile -->
							        
							    <?php endwhile; ?>
						    
						    </div><!-- feature-tiles -->
						    
						<?php endif; ?>
					
					</div><!-- alignwide -->
				
				</div><!-- alignfull solutions-features-section -->
				
			<?php endif; ?>
			
		
			<div class="wp-block-columns alignwide main-content-columns solutions-main-section">
				<div class="wp-block-column">
					
					<?php if( get_field('programs_content') != 'yes_commodity' ) : ?>
					
						<?php the_post_thumbnail(); ?>
						
					<?php endif; ?>
					
					<?php
					$testimonials = get_field('testimonials');
					if( $testimonials ): ?>
							
						<div class="minimize-testimonials-carousel <?php if( get_field('programs_content') == 'yes_commodity' ) : ?>commodity-testimonials-carousel<?php endif; ?>" data-flickity='{ 
								"freeScroll": true,
								"contain": true,
								"pageDots": true,
								"prevNextButtons": false,
								"draggable": false,
								"wrapAround": true,
								"autoPlay": 8000
							}'>
				
				    
						    <?php foreach( $testimonials as $testimonial ): 
						        $title = get_the_title( $testimonial->ID );
						        $testimonial_quote = get_field( 'quote', $testimonial->ID );
						        $testimonial_name = get_field( 'name', $testimonial->ID );
						        $testimonial_organization = get_field( 'organization', $testimonial->ID );
						        ?>
						        
						        <div class="carousel-cell">

							        <p class="carousel-cell-testimonial-quote gotham-bold">"<?php echo $testimonial_quote; ?>"</p>
							        <p class="carousel-cell-testimonial-cite gotham-bold"><?php echo $testimonial_name; ?>, <?php echo $testimonial_organization; ?></p>

						        </div>
						        
						    <?php endforeach; ?>
				    
						</div><!-- .minimize-partner-carousel -->
				    
					    <style>
						    .entry-content > .minimize-testimonials-carousel {
								margin-top: 0;
								margin-bottom: 0;
							}
							.minimize-testimonials-carousel, .minimize-testimonials-carousel * {
								box-sizing: border-box;
							}
							.minimize-testimonials-carousel {
								margin-bottom: 6rem;
							}
							.minimize-testimonials-carousel {
								/* MOVED TO STYLES.CSS TO ADJUST RESPONSIVE */
							}
							.minimize-testimonials-carousel .carousel-cell {
								background-color: #FFF;
								width: 100%;
								border-left: 1px solid white;
								background-position: center center;
								background-size: cover;
							}
							.minimize-testimonials-carousel .partner-carousel-image {
								background-position: center center;
								background-size: contain;
								background-repeat: no-repeat;
								height: 100px;
							}
							.minimize-testimonials-carousel .flickity-page-dots {
								text-align: left;
							}
							.minimize-testimonials-carousel .flickity-page-dots li:first-child {
								margin-left: 0;
							}
						</style>
				
					    
					<?php endif; ?> <!-- testimonials -->
					
				</div>
				<div class="wp-block-column">
					<?php if( get_field('headline') ): ?>
						<h2><?php the_field('headline'); ?></h2>
					<?php endif; ?>
					<div class="bef-difference-body-text <?php if( get_field('programs_content') == 'yes_commodity' ) : ?>bef-difference-commodity-body-text<?php endif; ?>">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
			
			
			<?php
				$map_section = get_field( 'map_text_toggle' );
				if( $map_section == "yes" ) : ?>
				
					<div class="program-map-text-section alignfull">
						<div class="wp-block-columns alignwide main-content-columns">
							<div class="wp-block-column">
								<?php the_field('text_next_to_map'); ?>
							</div>
							<div class="wp-block-column">
								<?php 
								$image = get_field('map_image');
								if( !empty( $image ) ): ?>
								    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
								<?php endif; ?>
							</div>
						</div>
					</div>
					
			<?php elseif( $map_section == "singular_image" ): ?>
			
					<div class="program-singular-image-section alignfull">
						<?php 
						$image = get_field('singular_image');
						if( !empty( $image ) ): ?>
						    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
						<?php endif; ?>
					</div>
			
			<?php endif; ?>
			
			
			<div class="alignfull square-tiles-section">
				
				<div class="alignwide">
					
					<?php if( get_field('square_tiles_title') ): ?>
						<h4><?php the_field('square_tiles_title'); ?></h4>
					<?php endif; ?>
					
					<?php if( have_rows('square_tiles') ): ?>
							
					    <div class="feature-tiles">
						    
						    <?php while( have_rows('square_tiles') ): the_row(); 
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
			
			
			<?php if( get_field('in_action_title') ): ?>
				<div class="alignfull in-action-section">
					
					<div class="alignwide">
						
						
						<h4><?php the_field('in_action_title'); ?></h4>
						
						
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
			<?php endif; ?>
			
			
			<?php if( get_field('lower_text') ): ?>
				<div class="alignfull program-lower-text-section">
					
					
						
					<?php the_field('lower_text'); ?>
					
							
	
				</div>
			<?php endif; ?>
			
			
			<?php if( get_field('contact_body') ): ?>
			
				<div class="alignfull solutions-contact-section">
			
					<div class="alignwide">
						
						<div class="contact-tiles">
							
							<div class="contact-tile">
								
								<?php the_field('contact_body'); ?>
								
							</div><!-- contact-tile -->
							
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
						
						</div><!-- contact-tiles -->
						
					</div><!-- alignwide -->
			
				</div><!-- alignfull solutions-features-section -->
			
			<?php endif; ?><!-- if contact_body -->
			
			
			
			<?php if( get_field('bef_proudly_supports_title') ): ?>
			
				<div class="alignfull bef-proudly-supports-section">
			
					<div class="alignwide">
						
						<h4><?php the_field('bef_proudly_supports_title'); ?></h4>
						
						<?php if( have_rows('bef_proudly_supports_-_tiles') ): ?>
						
						    <div class="bef-proudly-supports-tiles">
							    
						    <?php while( have_rows('bef_proudly_supports_-_tiles') ): the_row();
							    $image = get_sub_field('logo');
							    ?>
						        
						        <div class="bef-proudly-supports-tile">
							        
							        <div class="bef-proudly-supports-tile-image"><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" /></div>
							        
						            <h5><?php the_sub_field('title'); ?></h5>
						            
						        </div><!-- bef-proudly-supports-tile -->
						        
						    <?php endwhile; ?>
						    
						    </div><!-- bef-proudly-supports-tiles -->
						    
						<?php endif; ?>
						
					</div><!-- alignwide -->
			
				</div><!-- alignfull solutions-features-section -->
			
			<?php endif; ?><!-- if bef_proudly_supports_title -->
			
			
			
			<?php
				$programType = get_field( 'programs_content' );
				if( $programType == "yes_external" ) : ?>
				
					<?php if( get_field('programs_content') == 'yes_commodity' ) {
						$carousel_cell_secondary_text = 0;
					} ?>
				
					<?php if( have_rows('carousel') ): ?>
			
						<div class="alignfull minimize-carousel external-program-carousel">
							
							<div class="minimize-carousel" data-flickity='{ 
									"freeScroll": true,
									"contain": true,
									"pageDots": false,
									"draggable": false,
									"groupCells": 3,
									"wrapAround": true,
									"autoPlay": 8000,
									"setGallerySize": false
								}'>
									
								<?php while( have_rows('carousel') ): the_row(); 
		
									// vars
									$title = get_sub_field('title');
									$client_name = get_sub_field('client_name');
									$image = get_sub_field('image');
									$text = get_sub_field('text');
							
									?>
									
									<?php if( $programType == "yes_internal" ) : ?>
										<?php if( $link ): ?>
											<a href="<?php echo $link; ?>">
										<?php endif; ?>
									<?php endif; ?>
							
										<div class="carousel-cell" 
											<?php if( $programType == "yes_external" ) : ?>
												style="background-image: url(<?php echo $image['url']; ?>)"
											<?php endif; ?>
										>
								
											<?php if( $programType == "yes_internal" ) : ?>
												<div class="internal-program-carousel-image" style="background-image: url(<?php echo $image['url']; ?>)"></div>
											<?php endif; ?>
								
											<div class="carousel-cell-text-wrapper">
												<div class="carousel-cell-text">
													<h4><?php echo $title; ?></h4>
													<div class="carousel-cell-text-client-name"><?php echo $client_name; ?></div>
													<?php if( $programType == "yes_internal" ) : ?>
														<p class="carousel-cell-text-text_area"><?php echo $text; ?></p>
													<?php endif; ?>
												</div>
												<div class="carousel-cell-gradient"></div>
											</div>
								
										</div>
										
									<?php if( $programType == "yes_internal" ) : ?>
										<?php if( $link ): ?>
											</a>
										<?php endif; ?>
									<?php endif; ?>
									
								<?php endwhile; ?>	
								
							</div>
		
						</div><!-- .alignfull .minimize-carousel -->
			
					<?php endif; ?> <!-- carousel -->
					 
			<?php elseif( $programType == "yes_internal" ) : ?> <!-- external above, internal below -->
				
				<?php
				$case_study_posts = get_field('carousel_internal');
				if( $case_study_posts ): ?>
			
					<div class="alignfull minimize-carousel internal-program-carousel">
						
						<div class="minimize-carousel" data-flickity='{ 
								"freeScroll": true,
								"contain": true,
								"pageDots": false,
								"draggable": false,
								"groupCells": 3,
								"wrapAround": true,
								"autoPlay": 8000,
								"setGallerySize": false
							}'>
								
							<?php foreach( $case_study_posts as $case_study_post ): 
						        $permalink = get_permalink( $case_study_post->ID );
						        $title = get_the_title( $case_study_post->ID );
						        $client_name = get_field( 'client_name', $case_study_post->ID );
						        $text = get_field( 'text', $case_study_post->ID );
						        $featured_img_url = get_the_post_thumbnail_url($case_study_post->ID,'full'); 
						        ?>
						        
						        <div class="carousel-cell">
						
									<?php if( $programType == "yes_internal" ) : ?>
										<div class="internal-program-carousel-image" style="background-image: url(<?php echo $featured_img_url; ?>)"></div>
									<?php endif; ?>
						
									<div class="carousel-cell-text-wrapper">
										<div class="carousel-cell-text">
											<h4><?php echo $title; ?></h4>
											<div class="carousel-cell-text-client-name"><?php echo $client_name; ?></div>
											<?php if( $programType == "yes_internal" ) : ?>
												<p class="carousel-cell-text-text_area"><?php echo $text; ?></p>
											<?php endif; ?>
										</div>
										<div class="carousel-cell-gradient"></div>
									</div>
						
								</div>
						        
						    <?php endforeach; ?>
							
						</div>
	
					</div><!-- .alignfull .minimize-carousel -->
		
				<?php endif; ?> <!-- $case_study_posts -->
			
			<?php else: ?> <!-- commodity -->
				
				<?php
				$case_study_posts = get_field('carousel_internal');
				if( $case_study_posts ): ?>
			
					<div class="alignfull minimize-carousel external-program-carousel">
						
						<div class="minimize-carousel" data-flickity='{ 
								"freeScroll": true,
								"contain": true,
								"pageDots": false,
								"draggable": false,
								"groupCells": 3,
								"wrapAround": true,
								"autoPlay": 8000,
								"setGallerySize": false
							}'>
								
							<?php foreach( $case_study_posts as $case_study_post ): 
						        $permalink = get_permalink( $case_study_post->ID );
						        $title = get_the_title( $case_study_post->ID );
						        $client_name = get_field( 'client_name', $case_study_post->ID );
						        $text = get_field( 'text', $case_study_post->ID );
						        $featured_img_url = get_the_post_thumbnail_url($case_study_post->ID,'full'); 
						        ?>
						        
						        <div class="carousel-cell" style="background-image: url(<?php echo $featured_img_url; ?>)">
						
									<div class="carousel-cell-text-wrapper">
										<div class="carousel-cell-text">
											
											<h4><?php echo $title; ?></h4>
											
										</div>
										<div class="carousel-cell-gradient"></div>
									</div>
						
								</div>
						        
						        
						    <?php endforeach; ?>
							
						</div>
	
					</div><!-- .alignfull .minimize-carousel -->
		
				<?php endif; ?> <!-- $case_study_posts -->
				
			<?php endif; ?> <!-- $programType -->
			
			<?php if( $programType ) : ?>
				
				<style>
					.minimize-carousel, .minimize-carousel * {
						box-sizing: border-box;
					}
					.entry-content > .minimize-carousel {
						margin-top: 0;
						margin-bottom: 0;
					}
					
					.minimize-carousel .carousel-cell {
						width: 33.333%;
						border-left: 2px solid white;
						background-position: center center;
						background-size: cover;
					}
					.external-program-carousel,
					.internal-program-carousel {
						margin-bottom: 0 !important;
					}
					
					
					/* carousel height, as percentage of width */
					/* .carousel, */
					.external-program-carousel .minimize-carousel {
						padding-bottom: 33.3%;
						padding-bottom: 45.75%; /* Kelly's AI */
					}
					.internal-program-carousel .minimize-carousel {
						padding-bottom: 57.68%; /* Kelly's AI */
					}
					/* viewport inherit size from carousel */
					/* .carousel .flickity-viewport, */
					.minimize-carousel .flickity-viewport {
						position: absolute;
						width: 100%;
					}
					/* cell inherit height from carousel */
					/* .carousel-cell, */
					.external-program-carousel .carousel-cell,
					.internal-program-carousel .carousel-cell {
						height: 100%;
					}
					
					
					.internal-program-carousel-image {
						background-position: center center;
						background-size: cover;
						height: 255px;
						height: 38.45%; /* Kelly's AI */
					}

					.internal-program-carousel .carousel-cell-text-wrapper {
						padding: 5%;
					}
					.external-program-carousel .carousel-cell-text {
						position: absolute;
						bottom: 0;
						padding: 5%;
					}
					
					.internal-program-carousel .carousel-cell-text-client-name {
						padding-bottom: 2rem;
					}
					
					.internal-program-carousel .flickity-prev-next-button {
						top: 254px !important;
						top: 38.45% !important; /* Kelly's AI */
					}
					
					.minimize-carousel .flickity-button, .minimize-carousel .flickity-button:hover {
						background-color: transparent !important;
						background-repeat: no-repeat;
					}
					.minimize-carousel .flickity-prev-next-button {
						width: auto;
						height: 85px;
						border-radius: 0;
					}
					.minimize-carousel .flickity-prev-next-button.previous {
						background-image: url("<?php echo get_stylesheet_directory_uri(); ?>/images/Carousel-Left@2x.png");
						background-size: contain;
						left: 0;
						background-position: left center;
					}
					.minimize-carousel .flickity-prev-next-button.previous .flickity-button-icon {
						display: none;
					}
					.minimize-carousel .flickity-prev-next-button.next {
						background-image: url("<?php echo get_stylesheet_directory_uri(); ?>/images/Carousel-Right@2x.png");
						background-size: contain;
						right: 0;
						background-position: right center;
					}
					.minimize-carousel .flickity-prev-next-button.next .flickity-button-icon {
						display: none;
					}
					
					.carousel-cell-text {
						z-index: 1;
					}
					.carousel-cell-gradient {
						position: absolute;
						z-index: 0;
						top: 0;
						right: 0;
						bottom: 0;
						left: 0;
					}
					.external-program-carousel .carousel-cell-gradient {
						background: linear-gradient(0deg, rgba(0,0,0,0.500437675070028) 0%, rgba(0,0,0,0.500437675070028) 50%, rgba(0,0,0,0) 100%);

						background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.65) 100%);
					}

					.internal-program-carousel .carousel-cell-gradient {
						background: linear-gradient(0deg, rgba(0,172,210,1) 0%, rgba(0,172,210,1) 4%, rgba(0,172,210,0) 8%);
					}
					.solutions-energy .internal-program-carousel .carousel-cell-gradient {
						background: linear-gradient(0deg, rgba(255,153,83,1) 0%, rgba(255,153,83,1) 4%, rgba(255,153,83,0) 8%);
					}
					
					@media ( min-width: 1367px ) {
					
						.external-program-carousel .minimize-carousel {
							padding-bottom: 625px; /* Kelly's AI - Specifying px instead of percentage at this breakpoint to limit height */
						}
						.internal-program-carousel .minimize-carousel {
							padding-bottom: 788px; /* Kelly's AI - Specifying px instead of percentage at this breakpoint to limit height */
						}
					
					}
					
				</style>
			
			<?php endif; ?> <!-- $programType -->
			
			
			
			<?php if( $programType == "yes_external" ) : ?>
				
					<?php 
					$link = get_field('external_link');
					if( $link ): 
					    $link_url = $link['url'];
					    $link_title = $link['title'];
					    $link_target = $link['target'] ? $link['target'] : '_self';
					    ?>
					    
					    <div class="program-learn-more program-learn-more-external alignfull">
							<div class="alignwide">
							    
							    <a class="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
								    
								    <div class="program-learn-more-external-button-internal-wrapper">
									    <p>
									    <?php if( get_field('external_link_text') ): ?>
									    	<?php the_field('external_link_text'); ?><br />
									    <?php endif; ?>
									    
									    	<span class=""><?php echo esc_html( $link_title ); ?></span>
									    </p>
									    <p>
										    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/BEF-Button-Right-Arrow@2x.png">
									    </p>
								    </div>
								    
								</a>
							
							</div>
						</div>
					    
					<?php endif; ?>
				
			<?php endif; ?>
			
			<?php if( $programType == "yes_internal" ) : ?>
			
				<?php if( get_field('internal_text') ): ?>
			
					<div class="program-learn-more program-learn-more-internal alignfull">
						<div class="alignwide">
							<?php the_field('internal_text'); ?>
							
							<?php 
							$link = get_field('blue_button');
							if( $link ): 
							    $link_url = $link['url'];
							    $link_title = $link['title'];
							    $link_target = $link['target'] ? $link['target'] : '_self';
							    ?>
							    <br />
							    <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button><?php echo esc_html( $link_title ); ?></button></a>
							    
							<?php endif; ?>
						</div>
					</div>
					
				<?php endif; ?>
			<?php endif; ?>
			

			
			<?php
			$featured_posts = get_field('program_partners');
			if( $featured_posts ): ?>
			
				<div class="alignwide">
					
					<div class="alignwide program-partners-title">
						
						<?php if( get_field('program_partners_title') ): ?>
							<h4><?php the_field('program_partners_title'); ?></h4>
						<?php endif; ?>

					</div>
					
					<?php
						$page_id = get_the_ID();
						if ($page_id == 368) {
							$partner_count = 2;
						} else {
							$partner_count = 4;
						}
					?>

					<div class="minimize-partner-carousel" data-flickity='{ 
							"freeScroll": true,
							"contain": true,
							"pageDots": true,
							"prevNextButtons": false,
							"draggable": false,
							"groupCells": <?php echo $partner_count ?>,
							"wrapAround": true,
							"autoPlay": 5000
						}'>
			
			    
					    <?php foreach( $featured_posts as $featured_post ): 
					        $permalink = get_permalink( $featured_post->ID );
					        $title = get_the_title( $featured_post->ID );
					        $custom_field = get_field( 'field_name', $featured_post->ID );
					        $featured_img_url = get_the_post_thumbnail_url($featured_post->ID,'full'); 
					        ?>
					        <?php 
							$custom_field_link = get_field('partner_link', $featured_post->ID );
							if( $custom_field_link ): 
							    $link_url = $custom_field_link['url'];
							    $link_title = $custom_field_link['title'];
							    $link_target = $custom_field_link['target'] ? $custom_field_link['target'] : '_self';
							    ?>
							<?php endif; ?>
					        
					        <div class="carousel-cell">
						        <?php if( $custom_field_link ): ?>
						        	<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
						        <?php endif; ?>
						            
						            <div class="partner-carousel-image" style="background-image: url(<?php echo esc_url($featured_img_url); ?>)"></div>
						        
						        <?php if( $custom_field_link ): ?>
						        	</a>
						        <?php endif; ?>
					        </div>
					        
					    <?php endforeach; ?>
			    
					</div><!-- .minimize-partner-carousel -->
			    
			    </div><!-- .alignwide -->
			    
			    <style>
				    .entry-content > .minimize-partner-carousel {
						margin-top: 0;
						margin-bottom: 0;
					}
					.minimize-partner-carousel, .minimize-partner-carousel * {
						box-sizing: border-box;
					}
					.minimize-partner-carousel {
						margin-bottom: 6rem;
					}
					.program-partners-title {
						text-align: center;
					}
					.program-partners-title,
					.minimize-partner-carousel {
						max-width: 620px;
						max-width: 685px;
						margin-left: auto;
						margin-right: auto;
					}
					.postid-368 .minimize-partner-carousel {
						max-width: 342px;
					}
					.minimize-partner-carousel .carousel-cell {
						background-color: #FFF;
						width: 25%;
						border-left: 1px solid white;
						background-position: center center;
						background-size: cover;
						padding: 0 2.5rem;
					}
					.postid-368 .minimize-partner-carousel .carousel-cell {
						width: 50%;
					}
					.postid-368 .flickity-page-dots {
						display: none;
					}
					.minimize-partner-carousel .partner-carousel-image {
						background-position: center center;
						background-size: contain;
						background-repeat: no-repeat;
						height: 100px;
					}
				</style>
			    
			<?php endif; ?> <!-- program_partners -->

		</div><!-- .entry-content -->

	</div><!-- .post-inner -->

	

</article><!-- .post -->
