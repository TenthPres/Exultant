<?php
/**
 * Custom template tags for this theme.
 *
 * @package Tenth_Template
 * @subpackage Twenty_Twenty
 * @since Tenth Template 1.0
 */


/**
 * Check if the specified comment is written by the author of the post commented on.
 *
 * @param ?object $comment Comment data.
 *
 * @return bool
 */
function tenthTemplate_is_comment_by_post_author($comment = null)
{
    if (is_object($comment) && $comment->user_id > 0) {
        $user = get_userdata($comment->user_id);
        $post = get_post($comment->comment_post_ID);

        if ( ! empty($user) && ! empty($post)) {
            return $comment->user_id === $post->post_author;
        }
    }

    return false;
}

/**
 * Get and Output Post Meta.
 * If it's a single post, output the post meta values specified in the Customizer settings.
 *
 * @param ?int   $post_id The ID of the post for which the post meta should be output.
 * @param string $location Which post meta location to output – single or preview.
 */
function tenthTemplate_the_post_meta($post_id = null, $location = 'single-top')
{
    echo tenthTemplate_get_post_meta($post_id, $location);
}


/**
 * Get the post meta.
 *
 * @param ?int   $post_id The ID of the post.
 * @param string $location The location where the meta is shown.
 *
 * @return string|void
 */
function tenthTemplate_get_post_meta($post_id = null, $location = 'single-top')
{
    // Require post ID.
    if ( ! $post_id) {
        return;
    }

    /**
     * Filters post types array
     *
     * This filter can be used to hide post meta information of post, page or custom post type registered by child themes or plugins
     *
     * @param array Array of post types
     *
     * @since Tenth Template 1.0
     *
     */
    $disallowed_post_types = apply_filters('tenthTemplate_disallowed_post_types_for_meta_output', array('page'));
    // Check whether the post type is allowed to output post meta.
    if (in_array(get_post_type($post_id), $disallowed_post_types, true)) {
        return;
    }

    $post_meta_wrapper_classes = '';
    $post_meta_classes         = '';

    // Get the post meta settings for the location specified.
    if ('single-top' === $location) {
        /**
         * Filters post meta info visibility
         *
         * Use this filter to hide post meta information like Author, Post date, Comments, Is sticky status
         *
         * @param array $args {
         *
         * @type string 'author'
         * @type string 'post-date'
         * @type string 'comments'
         * @type string 'sticky'
         * }
         * @since Tenth Template 1.0
         *
         */
        $post_meta = apply_filters(
            'tenthTemplate_post_meta_location_single_top',
            [
                'author',
                'post-date',
                'comments',
                'sticky',
            ]
        );

        $post_meta_wrapper_classes = ' meta-s-top';
    } elseif ('single-bottom' === $location) {
        /**
         * Filters post tags visibility
         *
         * Use this filter to hide post tags
         *
         * @param array $args {
         *
         * @type string 'tags'
         * }
         * @since Tenth Template 1.0
         *
         */
        $post_meta = apply_filters(
            'tenthTemplate_post_meta_location_single_bottom',
            [
                'tags',
            ]
        );

        $post_meta_wrapper_classes = ' meta-s-bottom';
    }

    // If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output.
    if ($post_meta && ! in_array('empty', $post_meta, true)) {
        // Make sure we don't output an empty container.
        $has_meta = false;

        global $post;
        $the_post = get_post($post_id);
        setup_postdata($the_post);

        ob_start();

        ?>

        <div class="post-meta<?php
        echo esc_attr($post_meta_wrapper_classes); ?>">

            <ul class="post-meta<?php
            echo esc_attr($post_meta_classes); ?>">

                <?php

                /**
                 * Fires before post meta html display.
                 *
                 * Allow output of additional post meta info to be added by child themes and plugins.
                 *
                 * @param int    $post_id Post ID.
                 * @param array  $post_meta An array of post meta information.
                 * @param string $location The location where the meta is shown.
                 *                          Accepts 'single-top' or 'single-bottom'.
                 *
                 * @since Tenth Template 1.0
                 *
                 */
                do_action('tenthTemplate_start_of_post_meta_list', $post_id, $post_meta, $location);

                // Author.
                if (in_array('author', $post_meta, true)) {
                    $has_meta = true;
                    ?>
                    <li class="byline">
                        <?php
                        printf(
                        /* translators: %s: Author name. */ // TODO integrate multi-author
                            __('By %s', 'tenthTemplate'),
                            '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(
                                get_the_author_meta('display_name')
                            ) . '</a>'
                        );
                        ?>
                    </li>
                    <?php
                }

                // Post date.
                if (in_array('post-date', $post_meta, true)) {
                    $has_meta = true;
                    ?>
                    <li class="date">
                        <a href="<?php
                        the_permalink(); ?>"><?php
                            the_time(get_option('date_format')); ?></a>
                    </li>
                    <?php
                }

                // Categories.
                if (in_array('categories', $post_meta, true) && has_category()) {
                    $has_meta = true;
                    ?>
                    <li class="category">
                        <?php
                        the_category(', '); ?>
                    </li>
                    <?php
                }

                // Tags.
                if (in_array('tags', $post_meta, true) && has_tag()) {
                    $has_meta = true;  // TODO separate and add links
                    ?>
                    <li class="tags">
                        <?php
                        the_tags('', ', ', ''); ?>
                    </li>
                    <?php
                }

                /**
                 * Fires after post meta html display.
                 *
                 * Allow output of additional post meta info to be added by child themes and plugins.
                 *
                 * @param int    $post_id Post ID.
                 * @param array  $post_meta An array of post meta information.
                 * @param string $location The location where the meta is shown.
                 *                          Accepts 'single-top' or 'single-bottom'.
                 *
                 * @since Tenth Template 1.0
                 *
                 */
                do_action('tenthTemplate_end_of_post_meta_list', $post_id, $post_meta, $location);

                ?>

            </ul><!-- .post-meta -->

        </div><!-- .post-meta-wrapper -->

        <?php

        wp_reset_postdata();

        $meta_output = ob_get_clean();

        // If there is meta to output, return it.
        if ($has_meta && $meta_output) {
            return $meta_output;
        }
    }
}

/**
 * Menus
 */
/**
 * Filter Classes of wp_list_pages items to match menu items.
 * Filter the class applied to wp_list_pages() items with children to match the menu class, to simplify.
 * styling of sub levels in the fallback. Only applied if the match_menu_classes argument is set.
 *
 * @param array  $css_class CSS Class names.
 * @param string $item Comment.
 * @param int    $depth Depth of the current comment.
 * @param array  $args An array of arguments.
 * @param string $current_page Whether or not the item is the current item.
 *
 * @return array $css_class CSS Class names.
 */
function twentytwenty_filter_wp_list_pages_item_classes( $css_class, $item, $depth, $args, $current_page ) {

	// Only apply to wp_list_pages() calls with match_menu_classes set to true.
	$match_menu_classes = isset( $args['match_menu_classes'] );

	if ( ! $match_menu_classes ) {
		return $css_class;
	}

	// Add current menu item class.
	if ( in_array( 'current_page_item', $css_class, true ) ) {
		$css_class[] = 'current-menu-item';
	}

	// Add menu item has children class.
	if ( in_array( 'page_item_has_children', $css_class, true ) ) {
		$css_class[] = 'menu-item-has-children';
	}

	return $css_class;
}

add_filter( 'page_css_class', 'twentytwenty_filter_wp_list_pages_item_classes', 10, 5 );

///**
// * Add a Sub Nav Toggle to the Expanded Menu and Mobile Menu.
// *
// * @param stdClass $args An array of arguments.
// * @param string   $item Menu item.
// * @param int      $depth Depth of the current menu item.
// *
// * @return stdClass $args An object of wp_nav_menu() arguments.
// */
//function tenthTemplate_add_sub_toggles_to_main_menu( $args, $item, $depth ) { // TODO rework for mobile nav or remove
//
//	// Add sub menu toggles to the Expanded Menu with toggles.
//	if ( isset( $args->show_toggles ) && $args->show_toggles ) {
//
//		// Wrap the menu item link contents in a div, used for positioning.
//		$args->before = '<div class="ancestor-wrapper">';
//		$args->after  = '';
//
//		// Add a toggle to items with children.
//		if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
//
//			$toggle_target_string = '.menu-modal .menu-item-' . $item->ID . ' > .sub-menu';
//
//			// Add the sub menu toggle.
//			$args->after .= '<button class="toggle sub-menu-toggle fill-children-current-color" data-toggle-target="' . $toggle_target_string . '" data-toggle-type="slidetoggle" aria-expanded="false"><span class="screen-reader-text">' . __( 'Show sub menu', 'tenthtemplate' ) . '</span>' . twentytwenty_get_theme_svg( 'chevron-down' ) . '</button>';
//
//		}
//
//		// Close the wrapper.
//		$args->after .= '</div><!-- .ancestor-wrapper -->';
//
//		// Add sub menu icons to the primary menu without toggles.
//	} elseif ( 'primary' === $args->theme_location ) {
//		if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
//			$args->after = '<span class="icon"></span>';
//		} else {
//			$args->after = '';
//		}
//	}
//
//	return $args;
//
//}
//
//add_filter( 'nav_menu_item_args', 'tenthTemplate_add_sub_toggles_to_main_menu', 10, 3 );


/**
 * Add No-JS Class.
 * If we're missing JavaScript support, the HTML element will have a no-js class.
 */
function tenthTemplate_no_js_class() {

	?>
	<script>document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>
	<?php

}

add_action( 'wp_head', 'tenthTemplate_no_js_class' );

/**
 * Add conditional body classes.
 *
 * @param string[] $classes Classes added to the body tag.
 *
 * @return string[] Classes added to the body tag.
 */
function tenthTemplate_body_classes(array $classes) {
    global $post;
    $post_type = isset($post) ? $post->post_type : false;

    // Check whether we're singular.
    if (is_singular()) {
        $classes[] = 'singular';
    }

//    // Check whether the current page should have an overlay header.  TODO Connect for other templates.
//    if (is_page_template(array('templates/template-cover.php'))) {
//        $classes[] = 'overlay-header';
//    }
//
//    // Check whether the current page has full-width content.
//    if (is_page_template(array('templates/template-full-width.php'))) {
//        $classes[] = 'has-full-width-content';
//    }

	// Check for post thumbnail.
	if ( is_singular() && has_post_thumbnail() ) {
		$classes[] = 'has-thumb';
	} elseif ( is_singular() ) {
		$classes[] = 'no-thumb';
	}

	// Check whether we're in the customizer preview.
	if ( is_customize_preview() ) {
		$classes[] = 'customizer-preview';
	}

	// Check if posts have single pagination.
	if ( is_single() && ( get_next_post() || get_previous_post() ) ) {
		$classes[] = 'pgntn-single';
	} else {
		$classes[] = 'pgntn-none';
	}

    // Check if we're showing comments.
    if ($post && (('post' === $post_type || comments_open() || get_comments_number()) && ! post_password_required())) {
        $classes[] = 'show-comments';
    } else {
        $classes[] = 'hide-comments';
    }

	// Check for the elements output in the top part of the footer.
	$has_footer_menu = has_nav_menu( 'footer' );
	$has_social_menu = has_nav_menu( 'social' );

	// Add a class indicating whether those elements are output.
	if ( $has_footer_menu || $has_social_menu ) {
		$classes[] = 'footer-visible';
	} else {
		$classes[] = 'footer-hidden';
	}

	return $classes;

}

add_filter( 'body_class', 'tenthTemplate_body_classes' );

/**
 * Archives
 */
/**
 * Filters the archive title and styles the word before the first colon.
 *
 * @param string $title Current archive title.
 *
 * @return string $title Current archive title.
 */
function tenthTemplate_get_the_archive_title(string $title)
{
    $regex = apply_filters(
        'tenthTemplate_get_the_archive_title_regex',
        [
            'pattern'     => '/(\A[^\:]+\:)/',
            'replacement' => '<span>$1</span>',
        ]
    );

    if (empty($regex)) {
        return $title;
    }

    return preg_replace($regex['pattern'], $regex['replacement'], $title);
}

add_filter('get_the_archive_title', 'tenthTemplate_get_the_archive_title');



