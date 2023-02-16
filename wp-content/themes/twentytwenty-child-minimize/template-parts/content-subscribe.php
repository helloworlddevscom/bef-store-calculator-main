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

<div class="alignfull home-signup-section subscribe-section">
				
	<div class="alignwide">
	
		<?php if( get_field('signup_intro_text', 484) ): ?>
			<p><?php the_field('signup_intro_text', 484); ?></p>
		<?php endif; ?>
		
		<div class="form-input-wrapper">
			
				<?php if( get_field('text_before_field', 484) ): ?>
<!-- 					<div class="text_before_field all-caps gotham-bold"><?php the_field('text_before_field', 484); ?></div> -->
					
					<div class="form-input-placeholder">Your Email Address</div>
				<?php endif; ?>
				
		</div><!-- .form-input-wrapper -->
		
		<button>Submit</button>
	
	</div><!-- alignwide -->
	
</div>