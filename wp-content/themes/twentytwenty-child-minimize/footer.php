<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

?>
			<footer id="site-footer" role="contentinfo" class="header-footer-group">

				<div class="section-inner">
					
					<a class="to-the-top to-the-top-transparent" href="#site-header">
						<span class="to-the-top-long">
							<?php
							/* translators: %s: HTML character for up arrow */
							printf( __( 'To the top %s', 'twentytwenty' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
							?>
						</span><!-- .to-the-top-long -->
						<span class="to-the-top-short">
							<?php
							/* translators: %s: HTML character for up arrow */
							printf( __( 'Up %s', 'twentytwenty' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
							?>
						</span><!-- .to-the-top-short -->
					</a><!-- .to-the-top -->

					<div class="footer-credits">
						
						<p class="footer-logo">
							<a href="https://befnew1.wpengine.com/">
								<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/bef-logo-3COLOR-RGB-small_copy.png">
							</a>
						</p><!-- .powered-by-wordpress -->

						<p class="footer-copyright gotham-book">&copy;
							<?php
							echo date_i18n(
								/* translators: Copyright date format, see https://secure.php.net/date */
								_x( 'Y', 'copyright date format', 'twentytwenty' )
							);
							?>
							<a href="https://befnew1.wpengine.com/">
								Bonneville Environmental Foundation
							</a>
						</p><!-- .footer-copyright -->
					</div><!-- .footer-credits -->

					<a class="to-the-top" href="#site-content">
						<span class="to-the-top-long">
							<?php
							/* translators: %s: HTML character for up arrow */
							printf( __( 'To the top %s', 'twentytwenty' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
							?>
						</span><!-- .to-the-top-long -->
						<span class="to-the-top-short">
							<?php
							/* translators: %s: HTML character for up arrow */
							printf( __( 'Up %s', 'twentytwenty' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
							?>
						</span><!-- .to-the-top-short -->
					</a><!-- .to-the-top -->

				</div><!-- .section-inner -->

			</footer><!-- #site-footer -->

		<?php wp_footer(); ?>

		<script type="text/javascript">
							
			jQuery(document).ready(function($) {
				
				$( ".modal-menu .sub-menu" ).addClass( "active" );
					
				// NEW ACF IN FUNCTIONS	
				//$( ".menu-preview" ).append( "<div class='menu-preview-box'>Test</div>" );
					
				//$( "#menu-item-148 a" ).prepend( "<span class='primary-menu-icon fas fa-shopping-cart'></span>" );
				$( ".menu-store-icon a" ).prepend( "<span class='primary-menu-icon fas fa-shopping-cart'></span>" );
				
				$( ".awsm-grid-wrapper" ).addClass( "alignwide" );
				
				// Kelly prefers floating F icon for Facebook:
				// From: http://svgicons.sparkk.fr
				$('.social-menu .menu-social-facebook .svg-icon').html('<path d="M11.344,5.71c0-0.73,0.074-1.122,1.199-1.122h1.502V1.871h-2.404c-2.886,0-3.903,1.36-3.903,3.646v1.765h-1.8V10h1.8v8.128h3.601V10h2.403l0.32-2.718h-2.724L11.344,5.71z"></path>');
				
				$(".footer-top").prependTo(".footer-widgets-wrapper");
				
				
				$( window ).scroll(function() {
			        if ($(document).scrollTop() > 100) {
			            $('#home-2-scroll-tab').fadeOut('slow');
			        }
			        else {
			            $('#home-2-scroll-tab').fadeIn('slow');
			        }
			    });
				
				
				$( window ).load(function() {
					
					//
					
				});
				
				$( window ).resize(function() {
					
					//
					
				});
					
			});
		</script>

	</body>
</html>
