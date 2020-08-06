<?php

require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';

class TenthAdminMenu extends \WP_Admin_Bar {
	protected static ?TenthAdminMenu $singleton = null;

	/**
	 * Replaces default.  Prevents default bar from being rendered.
	 */
	public function render() {
		// Do nothing.
	}

	public function add_menus() {
		// User-related, aligned right.
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
//		add_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
//		add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_recovery_mode_menu', 8 );

		// Site-related.
		add_action( 'admin_bar_menu', 'wp_admin_bar_sidebar_toggle', 0 );
//		add_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40 );
//		add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 50 );

		// Content-related.
		if ( ! is_network_admin() && ! is_user_admin() ) {
//			add_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
			add_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
		}
		add_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );

		add_action( 'admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 );

		/**
		 * Fires after menus are added to the menu bar.
		 *
		 * @since 3.1.0
		 */
		do_action( 'add_admin_bar_menus' );

	}

	/**
	 * Renders the TenthAdminMenu.  Throws an exception if it hasn't been instantiated.
	 *
	 * @throws Exception
	 */
	public static function renderSingleton() {
		if (self::$singleton === null) {
			throw new Exception("TenthAdminMenu has not been instantiated yet.");
		}
		self::$singleton->renderForMenu();
	}

	/**
	 * TenthAdminMenu constructor.  Assigns the singleton; returns false if the singleton already exists.
	 */
	public function __construct() {
		if (self::$singleton === null) {
			self::$singleton = $this;
			return $this;
		} else {
			return false;
		}
		// note that there is no constructor on the parent class.
	}

	/**
	 * Called by the userMenu partial to generate the content of this menu.
	 */
	public function renderForMenu() {
		$root = $this->_bind();
		if ($root) {
			$this->_tenthRender($root);
		}
	}

	/**
	 * Renders the admin menu.
	 *
	 * @param object $root
	 */
	final protected function _tenthRender($root) {
			foreach ($root->children as $group) {
				$this->_tenthRender_group($group);
			}
	}


	/**
	 * @param object $node
	 */
	final protected function _tenthRender_group($node) {
		if ($node->type !== 'group' || empty($node->children)) {
			return;
		}

		echo "<ul>";
		foreach ( $node->children as $item ) {
			$this->_tenthRender_item( $item );
		}
		echo '</ul>';
	}

	/**
	 * @param object $node
	 */
	final protected function _tenthRender_item($node) {
		if ($node->type !== 'item') {
			return;
		}

		$is_parent   = !empty( $node->children );
		$has_link    = !empty( $node->href );

		echo "<li>";

		if ($has_link) {
			$attributes = ['onclick', 'target', 'title', 'rel', 'lang', 'dir'];
			echo "<a href='" . esc_url( $node->href ) . "'";
		} else {
			$attributes = ['onclick', 'target', 'title', 'rel', 'lang', 'dir'];
			echo '<span ';
		}

		foreach ($attributes as $attribute) {
			if (empty($node->meta[$attribute])) {
				continue;
			}

			if ('onclick' === $attribute) {
				echo " $attribute='" . esc_js($node->meta[$attribute]) . "'";
			} else {
				echo " $attribute='" . esc_attr($node->meta[$attribute]) . "'";
			}
		}

		echo ">{$node->title}";

		if ( $has_link ) {
			echo '</a>';
		} else {
			echo '</span>';
		}

		if ( $is_parent ) {
			foreach ( $node->children as $group ) {
				$this->_tenthRender_group($group);
			}
		}

		echo '</li>';
	}

	/**
	 *
	 */
	public function initialize() {
		$this->user = new stdClass;

		if ( is_user_logged_in() ) {
			/* Populate settings we need for the menu based on the current user. */
			$this->user->blogs = get_blogs_of_user( get_current_user_id() );
			if ( is_multisite() ) {
				$this->user->active_blog    = get_active_blog_for_user( get_current_user_id() );
				$this->user->domain         = empty( $this->user->active_blog ) ? user_admin_url() : trailingslashit( get_home_url( $this->user->active_blog->blog_id ) );
				$this->user->account_domain = $this->user->domain;
			} else {
				$this->user->active_blog    = $this->user->blogs[ get_current_blog_id() ];
				$this->user->domain         = trailingslashit( home_url() );
				$this->user->account_domain = $this->user->domain;
			}
		}

		/**
		 * Fires after WP_Admin_Bar is initialized.
		 *
		 * @since 3.1.0
		 */
		do_action( 'admin_bar_init' );
	}
}