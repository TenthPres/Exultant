<?php

namespace tp;

use Timber\Site;
use tp\TenthTemplate\TenthHeaderMenuWalker;
use tp\TenthTemplate\TenthMenu;
use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class TenthTheme extends Site
{
    /** Add timber support. */
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'theme_supports']);
        add_filter('timber/context', [$this, 'add_to_context']);
        add_filter('timber/twig', [$this, 'addTwigRuntimeAndExtensions']);
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
        parent::__construct();
    }

    /** This is where you can register custom post types. */
    public function register_post_types()
    {
    }

    /** This is where you can register custom taxonomies. */
    public function register_taxonomies()
    {
    }

    /** This is where you add some context
     *
     * @param array $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context(array $context)
    {  // TODO remove?
        $context['menus'] = [];
        if (has_nav_menu('primary')) {
            $context['menus']['primary'] = new TenthMenu(
                'primary', [
                             'menu_id'     => 'menu-primary',
                             'container'   => false,
                             'fallback_cb' => false,
                             'depth'       => 5,
//                         'item_spacing' => 'discard', // remove to add newlines and spaces to nav html
                             'walker'      => new TenthHeaderMenuWalker()
                         ]
            );
        }
        if (has_nav_menu('quick')) {
            $context['menus']['quick'] = new TenthMenu(
                'quick', [
                           'menu_id'     => 'menu-quick',
                           'container'   => false,
                           'fallback_cb' => false,
                           'depth'       => 1,
//                         'item_spacing' => 'discard' // remove to add newlines and spaces to nav html
                       ]
            );
        }
        if (has_nav_menu('footer')) {
            $context['menus']['footer'] = new TenthMenu(
                'footer', [
                            'container'      => '',
                            'depth'          => 2,
                            'items_wrap'     => '%3$s',
                            'theme_location' => 'footer',
                        ]
            );
        }
        if (has_nav_menu('social')) {
            $context['menus']['social'] = new TenthMenu(
                'social', [
                            'theme_location' => 'social',
                            'container'      => '',
                            'items_wrap'     => '%3$s',
                            'depth'          => 1,
                            'link_before'    => '<span class="screen-reader-text">',
                            'link_after'     => '</span>',
                            'fallback_cb'    => '',
                        ]
            );
        }
        $context['site'] = $this;

        return $context;
    }

    public function theme_supports()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            [
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        );

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support(
            'post-formats',
            [
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            ]
        );

        add_theme_support('menus');
    }

    /** Load extensions and runtime loaders for Twig
     *
     * @param Environment $twig get extension.
     */
    public function addTwigRuntimeAndExtensions(Environment $twig): Environment
    {
        // Allows for template extending
        $twig->addExtension(new StringLoaderExtension());

        // Allows for Markdown to be processed in the midst of twig
        $twig->addExtension(new MarkdownExtension());
        $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
            public function load($class): ?MarkdownRuntime
            {
                if (MarkdownRuntime::class === $class) {
                    return new MarkdownRuntime(new DefaultMarkdown());
                }
                return null;
            }
        });

        return $twig;
    }
}