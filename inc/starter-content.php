<?php
/**
 * Tenth Template Starter Content
 *
 * @link https://make.wordpress.org/core/2016/11/30/starter-content-for-themes-in-4-7/
 *
 * @package TenthTemplate
 * @since Tenth Template 1.0
 *
 */

/**
 * Function to return the array of starter content for the theme.
 *
 * Passes it through the `tenthTemplate_starter_content` filter before returning.
 *
 * @since Tenth Template 1.0
 * @return array a filtered array of args for the starter_content.
 */
function tenthTemplate_get_starter_content() {

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = [

		// Create the custom image attachments used as post thumbnails for pages.
		'attachments' => [
			'image-opening' => [
				'post_title' => _x( 'Welcome to Tenth Template', 'Theme starter content', 'tenthTemplate' ),
				'file'       => 'assets/images/pulpit-view.jpg', // URL relative to the template directory.
			],
		],

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts'=> [
			'front' => [
				'post_type'    => 'page',
				'post_title'   => __( 'Welcome to Tenth Template', 'tenthTemplate' ),
				// Use the above featured image with the predefined about page.
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					[
                        '<!-- wp:heading -->',
						'<h2>' . __( 'Exalting His Name, Proclaiming His Word.', 'tenthTemplate' ) . '</h2>',
						'<!-- /wp:heading --></div></div>',
						'<!-- wp:paragraph -->',
						'<p>' . __( 'Center City Philadelphia since 1832', 'tenthTemplate' ) . '</p>',
						'<!-- /wp:paragraph -->',
					]
                ),
			],
			'about',
			'contact',
			'resources',
        ],

		// Default to a static front page and assign the front and posts pages.
		'options'     => [
			'show_on_front'  => 'page',
			'page_on_front'  => '{{front}}',
			'page_for_posts' => '{{resources}}',
		],

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus'   => [
			// Assign a menu to the "primary" location.
			'primary'  => [
				'name'  => __( 'Main Menu', 'tenthTemplate' ),
				'items' => [
					'page_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_resources',
				],
			],
			// This replicates primary just to demonstrate the expanded menu.
			'quick' => [
				'name'  => __( 'Quick Access Menu', 'tenthTemplate' ),
				'items' => [
					'page_home',
					'page_about',
					'page_resources',
				],
			],
			// Assign a menu to the "social" location.
			'social'   => [
				'name'  => __( 'Social Links Menu', 'tenthTemplate' ),
				'items' => [
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_youtube',
					'link_vimeo',
				],
			],
		],
	];

	/**
	 * Filters Tenth Template array of starter content.
	 *
	 * @since Tenth Template 1.0
	 *
	 * @param array $starter_content Array of starter content.
	 */
	return apply_filters( 'tenthTemplate_starter_content', $starter_content );

}
