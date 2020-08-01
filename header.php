<?php
/**
 * Header file for the Tenth Template theme.
 *
 * @package Tenth_Template
 */

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<?php
		wp_body_open();
		?>


        <header role="banner">
            <a href="/" class="logo">
                <h1>Tenth Presbyterian Church</h1>
            </a>
			<?php if ( get_theme_mod( 'enable_header_nav', true ) && has_nav_menu( 'primary' ) ) { ?>
                <nav aria-label="<?php esc_attr_e( 'Main Menu', 'tenthtemplate' ); ?>" role="navigation" oncontextmenu="return true;">
                    <div>
                        <label class="las la-bars"></label>
                        <!-- TODO make hamburger do something -->
	                <?php wp_nav_menu( [
			                'menu'         => 'primary',
			                'menu_id'      => 'menu-primary',
			                'container'    => false,
			                'fallback_cb'  => false,
			                'depth'        => 5,
			                'item_spacing' => 'discard', // remove to add newlines and spaces to nav html
                            'walker' => new TenthHeaderMenuWalker()
		                ] );

	                $searchId = template_unique_id( 'search-form-' );
	                $searchResultsId = template_unique_id( 'search-list-' );
	                ?>
                    </div>
                    <div>
                        <label class="las la-search" for="<?php echo $searchId ?>"><!-- TODO --></label>
                        <div>
	                    <?php
	                    get_search_form(
		                    [
			                    'placeholder' => __( 'Search', 'tenthtemplate' ),
                                'id' => $searchId,
                                'resultsId' => $searchResultsId
		                    ]
	                    );
	                    ?>
                            <div id="<?php echo $searchResultsId ?>">
                                <span><?php _e("Start Typing...", "tenthtemplate") ?></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php get_template_part( 'template-parts/user-menu' ); ?>
                    </div>
                </nav>
            <?php } ?>
            <?php if ( get_theme_mod( 'enable_header_nav', true ) && has_nav_menu( 'quick' ) ) { ?>
                <div> <!-- quick menu -->
                    <?php wp_nav_menu( [
                            'menu'         => 'quick',
                            'menu_id'      => 'menu-quick',
                            'container'    => false,
                            'fallback_cb'  => false,
                            'depth'        => 1,
                            'item_spacing' => 'discard' // remove to add newlines and spaces to nav html
                        ] ); ?>
                </div>
            <?php } ?>
        </header>
