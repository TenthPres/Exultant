<?php
/**
 * Footer file for the Tenth Template theme.
 *
 * @package Tenth_Template
 */

?>
<div id="site-footer">
        <footer>

<?php
$has_footer_menu = has_nav_menu( 'footer' );
$has_social_menu = has_nav_menu( 'social' );

if ($has_footer_menu) {

    ?>
    <nav id="footer-nav">
        <ul>
            <?php
            wp_nav_menu(
                [
                    'container'      => '',
                    'depth'          => 2,
                    'items_wrap'     => '%3$s',
                    'theme_location' => 'footer',
                ]
            );
            ?>
        </ul>

    </nav>
<?php

}

?>
            <div>
            <?php
if ( $has_social_menu ) {
    ?>
                <nav aria-label="<?php esc_attr_e( 'Social links', 'tenthtemplate' ); ?>" class="footer-social">
                    <ul class="footer-social">
                        <?php
                        wp_nav_menu(
                            [
                                'theme_location'  => 'social',
                                'container'       => '',
                                'items_wrap'      => '%3$s',
                                'depth'           => 1,
                                'link_before'     => '<span class="screen-reader-text">',
                                'link_after'      => '</span>',
                                'fallback_cb'     => '',
                            ]
                        );
                        ?>

                    </ul><!-- .footer-social -->

                </nav><!-- .footer-social-wrapper -->

<?php } ?>


					<div class="footer-credits">

						<p class="footer-copyright">&copy;
							<?php
							echo date_i18n(
								/* translators: Copyright date format, see https://www.php.net/date */
								_x( 'Y', 'copyright date format', 'tenthtemplate' )
							);
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
						</p><!-- .footer-copyright -->

					</div><!-- .footer-credits -->
            </div>

			</footer>

</div><!-- #site-footer -->

		<?php wp_footer(); ?>

	</body>
</html>
