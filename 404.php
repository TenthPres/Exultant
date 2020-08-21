<?php
/**
 * The template for displaying the 404 template in the Tenth Template theme.
 *
 * @package Tenth_Template
 */

get_header();
?>

<main id="site-content" role="main" class="error">

	<div>

		<h1><?php _e( 'Not Found', 'tenthtemplate' ); ?></h1>

		<div><p><?php _e( "We couldn't find what you're looking for.", 'tenthtemplate' ); ?></p></div>

	</div><!-- .section-inner -->

</main><!-- #site-content -->

<?php
get_footer();
