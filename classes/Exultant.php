<?php

namespace tp;

use Timber\Loader;
use tp\Exultant\AdminMenu;
use tp\Exultant\ExultantScriptLoader;
use tp\Exultant\Post;
use Timber\Site;
use Timber\Timber;
use Twig\TwigFunction;
use tp\Exultant\HeaderMenuWalker;
use tp\Exultant\Menu;
use tp\TouchPointWP\Person;
use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

use WP_MatchesMapRegex;
use WP_Post;
use WP_Query;

class Exultant extends Site
{
    protected static ?self $singleton = null;


    public static string $joiner = "  &sdot;  ";

    /**
     * Get the singleton.
     *
     * @return Exultant
     */
    public static function instance(): Exultant
    {
        if (self::$singleton === null) {
            self::$singleton = new Exultant();
        }
        return self::$singleton;
    }

    /**
     * Adjust the Customizer to reflect actual options.
     *
     * @param $wp_customize
     * @return void
     */
    public function customizeCustomizer($wp_customize): void
    {

        // remove front page options
        $wp_customize->remove_control('show_on_front');
        $wp_customize->remove_control('page_on_front');
        $wp_customize->remove_control('page_for_posts');
        $wp_customize->remove_section('static_front_page');

        // Remove the Additional CSS section
        $wp_customize->remove_section('custom_css');


        // Add a new section for the header navigation
        $wp_customize->add_section('navigation_section', [
            'title'       => __('Navigation', 'Exultant'),
            'priority'    => 30,
            'description' => __('Settings for the navigation.', 'Exultant'),
        ]);

        // Add a new setting for enabling/disabling the header navigation
        $wp_customize->add_setting('enable_header_nav', [
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ]);

        // Add a new control for the header navigation setting
        $wp_customize->add_control('enable_header_nav_control', [
            'label'    => __('Enable Header Navigation', 'Exultant'),
            'section'  => 'navigation_section',
            'settings' => 'enable_header_nav',
            'type'     => 'checkbox',
        ]);
    }

    /**
     * Disable unwanted default WordPress things -- emojis, admin bar, etc.
     *
     * @return void
     */
    public function disableUnwantedDefaultWordPressThings_init(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    /**
     * Disable unwanted default WordPress things -- specifically scripts
     *
     * @return void
     */
    public function disableUnwantedDefaultWordPressThings_scripts(): void
    {
        wp_dequeue_script('admin-bar');
        wp_dequeue_script('hoverintent-js');
        wp_dequeue_script('jquery');
        wp_dequeue_script('jquery-core');
        wp_dequeue_script('jquery-migrate');

        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
        wp_dequeue_style('wpml-blocks');
        wp_dequeue_style('core-block-supports' );
        wp_dequeue_style('core-block-supports-duotone');
        wp_dequeue_style('core-block-supports');
    }

    /**
     * Filter out unwanted prefetch domains.
     *
     * @param array $urls
     * @param string $relation_type
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection PhpMissingParamTypeInspection
     */
    public function filterPrefetchDomains($urls, $relation_type)
    {
        if ('dns-prefetch' === $relation_type) {
            $emoji_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
            $urls = array_diff($urls, [$emoji_url]);
        }

        return $urls;
    }

    /**
     * Remove emojis from TinyMCE
     *
     * @param $plugins
     * @return array
     */
    public function handleTinyMceInclusions($plugins)
    {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        } else {
            return [];
        }
    }

    /**
     * Exultant constructor.  Establishes most timber-related filters.
     */
    protected function __construct()
    {
        add_action('after_setup_theme', [$this, 'themeSupports']);
        add_action('customize_register', [$this, 'customizeCustomizer']);

        add_filter('timber/context', [$this, 'commonContext']);
        add_filter('timber/twig', [$this, 'addTwigRuntimeAndExtensions']);
        add_filter('timber/post/classmap', [Post::class, 'classMap']);

        add_action('init', [$this, 'registerPostTypes']);
        add_action('init', [$this, 'registerTaxonomies']);
        add_action('init', [$this, 'registerScriptsAndStyles']);

        // Remove unwanted default WordPress things
        add_action('init', [$this, 'disableUnwantedDefaultWordPressThings_init']);
        add_filter('tiny_mce_plugins', [$this, 'handleTinyMceInclusions']);
        add_filter('wp_resource_hints', [$this, 'filterPrefetchDomains'], 10, 2);
        add_action('wp_enqueue_scripts', [$this, 'disableUnwantedDefaultWordPressThings_scripts'], 99);

        // use our own admin menu, integrated into the nav.
        add_action('admin_bar_init', [AdminMenu::class, 'adminBarMenuHandler']);

        parent::__construct();
    }

    /** This is where you can register custom post types. */
    public function registerPostTypes()
    {
    }

    /** This is where you can register custom taxonomies. */
    public function registerTaxonomies()
    {
    }

    public function registerScriptsAndStyles()
    {
        add_filter('script_loader_tag', [ExultantScriptLoader::class, 'filterByTag'], 10, 2);

        wp_register_script('exultant-defer', get_template_directory_uri() . "/assets/js/exultant-defer.js", ['wp-i18n']);
        wp_set_script_translations('exultant-defer', 'Exultant');
        wp_enqueue_script('exultant-defer');
    }

    /** This is where you add some context
     *
     * @param array $context context['this'] Being the Twig's {{ this }}.
     */
    public function commonContext(array $context): array
    {
        $context['context'] = [
            'dir'               => get_template_directory_uri(),
        ];

        $context['typeInfo'] = [
            'includeByline'     => true
        ];

        $context['menus'] = [];
        if (has_nav_menu('primary')) {
            $context['menus']['primary'] = new Menu(
                'primary', [
                             'menu_id'     => 'menu-primary',
                             'container'   => false,
                             'fallback_cb' => false,
                             'depth'       => 5,
//                         'item_spacing' => 'discard', // remove to add newlines and spaces to nav html
                             'walker'      => new HeaderMenuWalker()
                         ]
            );
        }
        if (has_nav_menu('quick')) {
            $context['menus']['quick'] = new Menu(
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
            $context['menus']['footer'] = new Menu(
                'footer', [
                            'container'      => '',
                            'depth'          => 2,
                            'items_wrap'     => '%3$s',
                            'theme_location' => 'footer',
                        ]
            );
        }
        if (has_nav_menu('social')) {
            $context['menus']['social'] = new Menu(
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

        $context['poweredBy'] = __("Built with ❤️ in Philadelphia", "Exultant");

        return $context;
    }

    public function themeSupports(): void
    {
        load_theme_textdomain( 'Exultant', get_template_directory() . '/i18n' );


        $menus = [
           'primary' => __('Primary Menu', 'Exultant'),
           'quick'   => __('Quick Menu', 'Exultant'),
           'footer'  => __('Footer Menu', 'Exultant'),
           'social'  => __('Social Menu', 'Exultant'),
        ];

        foreach ($menus as $key => $description) {
            if (!has_nav_menu($key)) {
                $menu_id = wp_create_nav_menu($description);
                if (!is_wp_error($menu_id)) {
                    set_theme_mod('nav_menu_locations', array_merge(get_theme_mod('nav_menu_locations', []), [$key => $menu_id]));
                }
            }
        }

        register_nav_menus($menus);

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
                'status',
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

        $twig->addFunction(new TwigFunction('byline', [self::class, 'byline']));
        $twig->addFunction(new TwigFunction('breadcrumbs', [self::class, 'breadcrumbs']));
        $twig->addFunction(new TwigFunction('trim', 'trim'));
//        $twig->addFunction(new TwigFunction('option', 'get_option'));

        return $twig;
    }

    /**
     * Get the byline for a given post, or the current post if none is provided.
     *
     * TODO this should be completely reworked to work better with TouchPoint and not include authors on post types where they don't belong.
     *
     * @param $p
     * @return string
     */
    public static function byline($p = null): string
    {
        /** @var Post $p */

        $items = [];
        $author = null;

        if ($p === null) {
            $p = Timber::get_post();
            $author = $p->author();
        }

        if (!is_author()) {
            if ($author && $author->ID !== 0) {
                $items[] = __('By ') . "<a href=\"{$author->path()}\">{$author->name()}</a>";
            }
        }

        if (!is_day()) {
            $date = get_the_date('', $p);
            if ($date) {
                $items[] = $date;
            }
        }

        $readTime = self::timeToRead_str($p->post_content);
        if ($readTime) {
            $items[] = $readTime;
        }

        $editLink = get_edit_post_link($p);
        if ($editLink) {
            $edit = __('Edit');
            $items[] = "<a href=\"$editLink\">$edit</a>";
        }
        return implode(Exultant::$joiner, $items);
    }

    /**
     * Render function.  Replaces (and wraps) Timber::render()
     *
     * @param array|string $filenames  Name of the Twig file to render. If this is an array of files, Timber will
     *                                 render the first file that exists.
     * @param array        $data       Optional. An array of data to use in Twig template.
     * @param bool|int $expires    Optional. In seconds. Use false to disable cache altogether. When passed an
     *                                 array, the first value is used for non-logged in visitors, the second for users.
     *                                 Default false.
     * @param string       $cache_mode Optional. Any of the cache mode constants defined in TimberLoader.
     *
     * @return void
     *
     * @see Timber::render
     */
    public static function render(array|string $filenames, array $data = [], bool|int $expires = false, string $cache_mode = Loader::CACHE_USE_DEFAULT ): void
    {
        self::instance();

        $loader = new Loader();
        $file = $loader->choose_template($filenames);
        self::$renderedFilename = $file;  // makes filename available to the user menu partial

        Timber::render($filenames, $data, $expires, $cache_mode);
    }

    public static ?string $renderedFilename = null;

    /**
     * Provides a time to read as a human-readable string.
     *
     * @param string $content
     *
     * @return ?string
     */
    public static function timeToRead_str(string $content): ?string
    {
        /** @noinspection SpellCheckingInspection */
        $mins  = self::timeToRead_min($content);
        /** @noinspection SpellCheckingInspection */
        $rmins = round($mins * 2) / 2;

        if ($rmins === 0.0) { // exclude things that round to zero
            return null;
        }
        if ($mins < 1) {
            $secs = round($mins * 12) * 5;

            // translators: %s: number of seconds
            $s = sprintf(_n('%s second', '%s seconds', $secs, 'Exultant'), number_format_i18n($secs));
        } else {
            // translators: %s: number of minutes
            $s = sprintf(_n('%s minute', '%s minutes', $rmins, 'Exultant'), number_format_i18n($rmins));
        }
        // translators: %s: a string like "8 minutes" or "30 seconds"
        return sprintf(__('Read Time: %s', 'Exultant'), $s);
    }

    /**
     * Provides an estimate of time to read as a number of minutes.
     *
     * @param string $content
     *
     * @return float
     */
    public static function timeToRead_min(string $content): float
    {
        $content = strip_shortcodes($content);
        $content = strip_tags($content);

        $speed_wpm = 250.0; // 250-300, from WolframAlpha
        $words = 1.0 * str_word_count($content);
        return $words / $speed_wpm;
    }

    /** @var ?object[]  */
    private static ?array $_breadcrumbs = null;

    /**
     * Generate an array of info to be used for breadcrumbs.
     *
     * @return array
     */
    public static function breadcrumbs(): array
    {
        if (self::$_breadcrumbs !== null) {
            return self::$_breadcrumbs;
        }

        global $wp;
        $path = explode("/", $wp->request);
        $r = [];
        $concat = "/";

        foreach ($path as $p) {
            if ($p === "")
                continue;

            $concat .= $p;
            $info = self::urlToInfo($concat);

            if ($info !== null) {
                if ($info['q']->is_author) {
                    $person = Person::fromId($info['q']->query_vars['author']);
                    if ($person)
                        $info['title'] = $person->display_name;
                }

                $r[] = $info;
            }

            $concat .= "/";
        }

        if (is_search()) {
            $r[] = (object)[
                'url' => $wp->request,
                'title' => __('Search'),
                'type' => 'search',
                'label' => null
            ];
        }

        self::$_breadcrumbs = $r;

        return $r;
    }

    /**
     * Add st, nd, rd, th for numbers.
     *
     * @param numeric $num
     *
     * @return string
     *
     * TODO i18n
     */
    public static function addOrdinalIndicator(float|int|string $num): string
    {
        $num = intval($num);
        $ind = $num % 100;
        if ($ind >= 11 && $ind <= 13) // 11, 12, 13
            return $num . 'th';
        return $num . ['th','st','nd','rd','th','th','th','th','th','th'][$ind % 10];
    }

    /**
     * Examines a URL and gets the item title  TODO figure out how much of this is actually needed.
     *
     * @param string $url Permalink to check.
     *
     * @return ?array Post ID, or 0 on failure.
     */
    public static function urlToInfo(string $url): ?array
    {
        global $wp_rewrite;

        $info = [
            'url' => $url,
            'title' => null,
            'type' => null,
            'label' => null
        ];

        // Get rid of the #anchor.
        $url = explode('#', $url)[0];

        // Set the correct URL scheme.
        $scheme = parse_url(home_url(), PHP_URL_SCHEME);
        $url    = set_url_scheme($url, $scheme);

        if (trim($url, '/') === home_url() && get_option('show_on_front') === 'page') {
            $page_on_front = get_option('page_on_front');

            if ($page_on_front && get_post($page_on_front) instanceof WP_Post) {
                return null;
            }
        }

        // Check to see if we are using rewrite rules.
        $rewrite = $wp_rewrite->wp_rewrite_rules();

        // Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options.
        if (empty($rewrite)) {
            return null;
        }

        // Strip 'index.php/' if we're not using path info permalinks.
        if ( ! $wp_rewrite->using_index_permalinks()) {
            $url = str_replace($wp_rewrite->index . '/', '', $url);
        }

        if (str_contains(trailingslashit($url), home_url('/'))) {
            // Chop off http://domain.com/[path].
            $url = str_replace(home_url(), '', $url);
        } else {
            // Chop off /path/to/blog.
            $home_path = parse_url(home_url('/'));
            $home_path = $home_path['path'] ?? '';
            $url       = preg_replace(sprintf('#^%s#', preg_quote($home_path)), '', trailingslashit($url));
        }

        // Trim leading and lagging slashes.
        $url = trim($url, '/');

        $request              = $url;
        $post_type_query_vars = [];

        foreach (get_post_types([], 'objects') as $post_type => $t) {
            if ( ! empty($t->query_var)) {
                $post_type_query_vars[$t->query_var] = $post_type;
            }
        }

        // Look for matches.
        $request_match = $request;
        foreach ((array)$rewrite as $match => $query) {
            if (preg_match("#^$match#", $request_match, $matches)) {
                if ($wp_rewrite->use_verbose_page_rules && preg_match(
                        '/pagename=\$matches\[([0-9]+)]/',
                        $query,
                        $varMatch
                    )) {
                    // This is a verbose page match, let's check to be sure about it.
                    $page = get_page_by_path($matches[$varMatch[1]]);
                    if ( ! $page) {
                        continue;
                    }

                    $post_status_obj = get_post_status_object($page->post_status);
                    if ( ! $post_status_obj->public && ! $post_status_obj->protected
                         && ! $post_status_obj->private && $post_status_obj->exclude_from_search) {
                        continue;
                    }
                }

                // Got a match.
                // Trim the query of everything up to the '?'.
                $query = preg_replace('!^.+\?!', '', $query);

                // Substitute the substring matches into the query.
                $query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

                // Filter out non-public query vars.
                global $wp;
                parse_str($query, $query_vars);
                $query = [
                    'posts_per_page' => 2
                ];
                foreach ($query_vars as $key => $value) {
                    if (in_array((string)$key, $wp->public_query_vars, true)) {
                        $query[$key] = $value;
                        if (isset($post_type_query_vars[$key])) {
                            $query['post_type'] = $post_type_query_vars[$key];
                            $query['name']      = $value;
                        }
                    }
                }

                // Resolve conflicts between posts with numeric slugs and date archive queries.
                $query = wp_resolve_numeric_slug_conflicts($query);

                // Do the query.
                $wpq = new WP_Query($query);

                $info['q'] = $wpq;
                $posts = $wpq->get_posts();
                if (count($posts) === 0) {
                    return null;
                }
                if ($wpq->is_archive()) {
                    if ($wpq->is_day()) {
                        $info['title'] = self::addOrdinalIndicator(get_the_date( 'j' ));
                        $info['type'] = "day";
                        $info['label'] = __('Day');
                    } elseif ($wpq->is_month()) {
                        $info['title'] = get_the_date('F');
                        $info['type'] = "month";
                        $info['label'] = __('Month');
                    } elseif ($wpq->is_year()) {
                        $info['title'] = get_the_date('Y');
                        $info['type'] = "year";
                        $info['label'] = __('Year');
                    } elseif ($wpq->is_post_type_archive()) {
                        global $wp_post_types;
                        $info['title'] = $wp_post_types[$wpq->get_posts()[0]->post_type]->label ?? null;
                        $info['type']  = 'post_type_archive';
                    }
                    return $info;
                }
                if ($wpq->is_singular() && count($posts) === 1) {
                    $info['title'] = get_the_title($posts[0]);
                    $info['type']  = 'single';
                    return $info;
                }
                if ($wpq->is_search()) {
                    $info['title'] = Timber::Context();
                    $info['type']  = 'search';
                    return $info;
                }
            }
        }

        return null;
    }
}