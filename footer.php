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
$hasFooterMenu = has_nav_menu('footer');
$hasSocialMenu = has_nav_menu('social');

if ($hasFooterMenu) {
    // get footer menu items based on 'footer' menu location.
    $footerMenuItems = wp_get_nav_menu_items(get_term(get_nav_menu_locations()['footer'], 'nav_menu')->name);

    $footerColumnCount = 0;
    foreach($footerMenuItems as $key => $itm) {
        if ($itm->menu_item_parent === '0' || $itm->menu_item_parent === 0) {
            $footerColumnCount++;
        };
    }


    ?>
    <nav id="footer-nav" style="flex: <?php echo $footerColumnCount; ?>;">
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
if ( $hasSocialMenu ) {
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
