<?php

namespace tp\Exultant;

require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';

use WP_Admin_Bar;

class AdminMenu extends WP_Admin_Bar
{
    protected static ?AdminMenu $singleton = null;

    /**
     * AdminMenu constructor.  Assigns the singleton; returns false if the singleton already exists.
     */
    protected function __construct()
    {
        if (self::$singleton !== null) {
            return false;
        }

        self::$singleton = $this;
        return $this;

        // note that there is no constructor on the parent class.
    }

    /**
     * Gets the singleton, creating it if it doesn't exist.
     *
     * @return AdminMenu
     */
    public static function getSingleton(): AdminMenu
    {
        if (self::$singleton === null) {
            self::$singleton = new AdminMenu();
        }
        return self::$singleton;
    }

    /**
     * Renders the AdminMenu.  Throws an exception if it hasn't been instantiated.
     *
     * @param bool $includeUL
     * @return void
     */
    public static function renderSingleton(bool $includeUL = true): void
    {
        $s = self::getSingleton();
        $s->renderForMenu($includeUL);
    }

    /**
     * Called by the userMenu partial to generate the content of this menu.
     *
     * @param bool $includeUL
     * @return void
     */
    public function renderForMenu($includeUL = true): void
    {
        do_action_ref_array('admin_bar_menu', [&$this]);
        do_action('wp_before_admin_bar_render');

        $root = $this->_bind();
        if ($root) {
            foreach ($root->children as $group) {
                $this->renderGroup($group, $includeUL);
            }
        }

        do_action('wp_after_admin_bar_render');
    }

    /**
     * @param object $node
     * @param bool $includeUL
     */
    protected function renderGroup(object $node, $includeUL = true): void
    {
        if ($node->type !== 'group' || empty($node->children)) {
            return;
        }

        if ($includeUL)
            echo "<ul>";
        foreach ($node->children as $item) {
            $this->renderItem($item);
        }
        if ($includeUL)
            echo '</ul>';
    }

    /**
     * Intercept the admin bar menu creation to replace it with our own.
     *
     * @return void
     */
    public static function adminBarMenuHandler(): void
    {
        if (!is_admin()) {
            global $wp_admin_bar;
            $wp_admin_bar = self::getSingleton();

            wp_dequeue_style('admin-bar');
            wp_dequeue_style('admin-bar-css');
        }
    }

    /**
     * @param object $node
     * @return void
     */
    protected function renderItem(object $node): void
    {
        if ($node->type !== 'item') {
            return;
        }

        $is_parent = ! empty($node->children);
        $has_link  = ! empty($node->href);

        echo "<li>";

        $attributes = ['onclick', 'target', 'title', 'rel', 'lang', 'dir'];
        if ($has_link) {
            echo "<a href='" . esc_url($node->href) . "'";
        } else {
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

        // if title has a node with class screen-reader-text (e.g. updates or comments), extract the text from that node.
        $title = $node->title;
        if (preg_match('/screen-reader-text.+>(.+)<\/span>/', $title, $matches)) {
            $title = $matches[1];
        } else {
            $title = strip_tags($title);
        }

        echo ">$title";

        if ($has_link) {
            echo '</a>';
        } else {
            echo '</span>';
        }

        if ($is_parent) {
            foreach ($node->children as $group) {
                $this->renderGroup($group);
            }
        }

        echo '</li>';
    }

    /**
     * Replaces default.  Prevents default bar from being rendered.
     */
    public function render()
    {
        // Do nothing.
    }

    public function add_menus(): void
    {
        // User-related, aligned right.
//        add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
//        add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 9991 );
        add_action( 'admin_bar_menu', 'wp_admin_bar_recovery_mode_menu', 9992 );
//        add_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 9999 );

        // Site-related.
        add_action( 'admin_bar_menu', 'wp_admin_bar_sidebar_toggle', 0 );
//        add_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
        add_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
        add_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
        add_action( 'admin_bar_menu', 'wp_admin_bar_edit_site_menu', 40 );
        add_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40 );
        add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 50 );

        // Content-related.
        if ( ! is_network_admin() && ! is_user_admin() ) {
            add_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
//            add_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
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
}