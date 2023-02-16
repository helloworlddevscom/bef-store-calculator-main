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

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php

	get_template_part( 'template-parts/entry-header' );

	?>

	<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">

		<div class="entry-content">
			
			
			<div class="wp-block-cover alignfull" style="background-image:url(https://befnew1.wpengine.com/wp-content/uploads/2020/02/road-1072823_1920.jpg)">
				<div class="wp-block-cover__inner-container">
					<p class="has-text-align-center has-large-font-size"></p>
				</div>
			</div>
			
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
			
			<div class="alignfull solutions-nav-banner">
				<div class="solutions-nav-item solutions-nav-item-title">
					
					<p class="has-text-align-center"><?php echo $termname; ?> Overview</p>
					<div class="solutions-nav-item-arrow-up"></div>
					
					<a class="solutions-nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>solutions/<?php echo $termslug; ?>"></a>
				</div>
				
				<?php
					$terms = get_the_terms( get_the_ID(), 'solutions' );
					$term = array_pop($terms);
					$termslug = $term->slug;
					//echo $termslug;

					$args = array(
					    'post_type' => 'projects',
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
			
			<div class="wp-block-columns alignwide main-content-columns">
				<div class="wp-block-column">
					<?php the_post_thumbnail(); ?>
				</div>
				<div class="wp-block-column">
					<?php if( get_field('headline') ): ?>
						<h4><?php the_field('headline'); ?></h4>
					<?php endif; ?>
					<?php the_content(); ?>
				</div>
			</div>
			
			<?php
				$programType = get_field( 'programs_content' );
				if( $programType == "yes_external" ) :
					$carouselClass = "external-program-carousel";
				else:
					$carouselClass = "internal-program-carousel";
				endif;
			?>

			<?php if( have_rows('carousel') ): ?>
			
				<div class="alignfull minimize-carousel <?php echo $carouselClass; ?>">
					
					<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/flickity/flickity.min.css" media="screen">
					<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/flickity/flickity.pkgd.min.js"></script>
					
					<div class="minimize-carousel" data-flickity='{ 
							"freeScroll": true,
							"contain": true,
							"pageDots": false,
							"draggable": false,
							"groupCells": 3,
							"wrapAround": true
						}'>
							
						<?php while( have_rows('carousel') ): the_row(); 

							// vars
							$title = get_sub_field('title');
							$client_name = get_sub_field('client_name');
							$image = get_sub_field('image');
							$text = get_sub_field('text');
					
							?>
							
							<?php if( $link ): ?>
								<a href="<?php echo $link; ?>">
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
											<h5><?php echo $title; ?></h5>
											<div class="carousel-cell-text-client-name"><?php echo $client_name; ?></div>
											<?php if( $programType == "yes_internal" ) : ?>
												<p class="carousel-cell-text-text_area"><?php echo $text; ?></p>
											<?php endif; ?>
										</div>
									</div>
						
								</div>
							
							<?php if( $link ): ?>
								</a>
							<?php endif; ?>
					
						<?php endwhile; ?>	
						
					</div>
	
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
							border-left: 1px solid white;
							background-position: center center;
							background-size: cover;
						}
						.external-program-carousel .carousel-cell {
							height: 500px;
						}
						.internal-program-carousel .carousel-cell {
							height: 700px;
						}
						
						.internal-program-carousel-image {
							background-position: center center;
							background-size: cover;
							height: 255px;
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
					</style>
					
				</div><!-- .alignfull .minimize-carousel -->
			
			<?php endif; ?>
			
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
								    
								    <p>
								    <?php if( get_field('external_link_text') ): ?>
								    	<?php the_field('external_link_text'); ?><br />
								    <?php endif; ?>
								    
								    	<span class=""><?php echo esc_html( $link_title ); ?></span>
								    </p>
								    
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
						</div>
					</div>
					
				<?php endif; ?>
			<?php endif; ?>
			

			
			<?php
			$featured_posts = get_field('program_partners');
			if( $featured_posts ): ?>
			
				<div class="alignwide">
					
					<div class="alignwide program-partners-title"><p>Program Partners</p></div>
					
					<?php if( ! have_rows('carousel') ): ?> <!-- DON'T LOAD IF CAROUSEL ABOVE HAS ALREADY DONE SO -->
						<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/flickity/flickity.min.css" media="screen">
						<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/flickity/flickity.pkgd.min.js"></script>
					<?php endif; ?>
					
					
					<div class="minimize-partner-carousel" data-flickity='{ 
							"freeScroll": true,
							"contain": true,
							"pageDots": true,
							"prevNextButtons": false,
							"draggable": false,
							"groupCells": 4,
							"wrapAround": true
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
						max-width: 600px;
						margin-left: auto;
						margin-right: auto;
					}
					.minimize-partner-carousel .carousel-cell {
						background-color: #FFF;
						width: 25%;
						border-left: 1px solid white;
						background-position: center center;
						background-size: cover;
					}
					.minimize-partner-carousel .partner-carousel-image {
						background-position: center center;
						background-size: contain;
						background-repeat: no-repeat;
						height: 100px;
					}
				</style>
			    
			<?php endif; ?>

		</div><!-- .entry-content -->

	</div><!-- .post-inner -->

	

</article><!-- .post -->
