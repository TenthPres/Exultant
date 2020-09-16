<?php

namespace tp\TenthTemplate;


class TenthHeaderMenuWalker extends \Walker_Nav_Menu {

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl(&$output, $depth = 0, $args = null) {
		if ($depth === 0) {
			self::newlineAndIndent($output, $depth+1, $args);
			$output .= "<div>";
		}

		self::newlineAndIndent($output, ($depth+1)*2, $args);

		// Default class.
		$classes = ['sub-menu'];

		// Filters CSS classes applied to a menu list.
		$class_names = implode(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= "<ul$class_names>";
	}


	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		self::newlineAndIndent($output, ($depth+1)*2, $args);
		$output .= "</ul>";
		if ($depth === 0) {
			self::newlineAndIndent($output, $depth+1, $args);
			$output .= "</div>";
		}
	}


	/**
	 * Starts the element output.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el(&$output, $item, $depth=0, $args=null, $id = 0) {

		$title = $item->title;
		$description = $item->description;
		$permalink = $item->url;
		$classNames = $item->classes;

		// column breaks
		if ($depth === 1 && in_array('col', $classNames)) {
			self::newlineAndIndent($nai, $depth*2, $args);
			$output .= $nai . "</ul>" . $nai . "<ul>";
		}


		// Menu item

		// Filters CSS classes applied to a menu item.
		$classNames = implode(' ', apply_filters('nav_menu_css_class', $classNames, $args, $depth));
		$classNames = $classNames ? ' class="' . esc_attr( $classNames ) . '"' : '';

		self::newlineAndIndent($output, $depth*2 + ($depth < 1 ? 0 : 1), $args);
		$output .= "<li" . $classNames . ">";

		// Add span if not a link
		if( $permalink && $permalink != '#' ) {
			$output .= '<a href="' . $permalink . '">';
		} else {
			$output .= '<span>';
		}

		$output .= $title;

		if( $description != '' ) {
			$output .= '&nbsp;<small class="description">' . $description . '</small>';
		}

		if( $permalink && $permalink != '#' ) {
			$output .= '</a>';
		} else {
			$output .= '</span>';
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_el()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Page data object. Not used.
	 * @param int      $depth  Depth of page. Not Used.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		self::newlineAndIndent($output, $depth*2 + ($depth < 1 ? 0 : 1), $args);
		$output .= "</li>";
	}


	/**
	 *  Adds newlines and tabs in a nice, standardized, way.
	 *
	 * @param $output
	 * @param int $depth
	 * @param null $args
	 */
	public static function newlineAndIndent(&$output, $depth = 0, $args = null) {
		if (isset($args->item_spacing) && $args->item_spacing === 'discard') {
			return;
		} else {
			$output .= "\n" . str_repeat("\t", $depth);
		}
	}

}