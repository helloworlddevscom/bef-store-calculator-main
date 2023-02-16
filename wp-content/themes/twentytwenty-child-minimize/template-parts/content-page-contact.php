<?php
/**
 * The default template for Who We Are Page from Full Width Template
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
			
			
			<div class="alignfull contact-intro-section">
			
				<?php if( get_field('intro_header') ): ?>
					<h2><?php the_field('intro_header'); ?></h2>
				<?php endif; ?>
				
				<?php if( get_field('intro_body') ): ?>
					<p><?php the_field('intro_body'); ?></p>
				<?php endif; ?>
				
				<div class="contact-primary-wrapper">
					<?php if( get_field('phone') ): ?>
						
							<div class="contact-primary-title">Phone</div>
							<div class="contact-primary-info"><h5><?php the_field('phone'); ?></h5></div>
						
					<?php endif; ?>
					<?php if( get_field('fax') ): ?>
						
							<div class="contact-primary-title">Fax</div>
							<div class="contact-primary-info"><h5><?php the_field('fax'); ?></h5></div>
						
					<?php endif; ?>
					<?php if( get_field('email') ): ?>
						
							<div class="contact-primary-title">Email</div>
							<div class="contact-primary-info"><h5><a href="mailto:<?php the_field('email'); ?>"><?php the_field('email'); ?></a></h5></div>
						
					<?php endif; ?>
				</div>
			
			</div>
			
			
			<div class="alignfull contact-support-section">
				
				<?php if( get_field('customer_support_title') ): ?>
					<div><?php the_field('customer_support_title'); ?></div>
					<h5><?php the_field('support_phone'); ?></h5>
				<?php endif; ?>
				
					<br />
				
				<?php if( get_field('media_inquiries_title') ): ?>
					<div><?php the_field('media_inquiries_title'); ?></div>
					<h5><a href="mailto:<?php the_field('media_email'); ?>"><?php the_field('media_email'); ?></a></h5>
				<?php endif; ?>
			
			</div>
			
			
			<div class="alignfull contact-form-section">
				
				<?php if( get_field('form_intro') ): ?>
					<div><?php the_field('form_intro'); ?></div>
					
					<?php echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]'); ?>
					
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
