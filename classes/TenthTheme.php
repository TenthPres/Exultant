<?php

namespace tp;

use tp\TenthTemplate\Post;
use Timber\Site;
use Timber\Timber;
use Timber\Twig_Function;
use tp\TenthTemplate\TenthHeaderMenuWalker;
use tp\TenthTemplate\TenthMenu;
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

if (!is_admin()) {
    require_once "Post.php";
    require_once "PostQuery.php";
    require_once "PostPreview.php";
}

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

        $twig->addFunction(new Twig_Function('byline', [self::class, 'byline']));
        $twig->addFunction(new Twig_Function('breadcrumbs', [self::class, 'breadcrumbs']));
        $twig->addFunction(new Twig_Function('trim', 'trim'));

        return $twig;
    }

    public static function byline($p): string
    {
        /** @var Post $p */

        $items = [];

        if (!is_author()) {
            $author = get_the_author();

            if ($author) {
                $items[] = $author;
            } elseif ($p->author->ID !== 0) {
                $items[] = __('By ') . "<a href=\"{$p->author->path}\">{$p->author->name}</a>";
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
        return implode(TouchPointWP\TouchPointWP::$joiner, $items);
    }

    /**
     * Render function.  Replaces (and wraps) Timber::render()
     *
     * @param array|string $filenames  Name of the Twig file to render. If this is an array of files, Timber will
     *                                 render the first file that exists.
     * @param array        $data       Optional. An array of data to use in Twig template.
     * @param bool|int     $expires    Optional. In seconds. Use false to disable cache altogether. When passed an
     *                                 array, the first value is used for non-logged in visitors, the second for users.
     *                                 Default false.
     * @param string       $cache_mode Optional. Any of the cache mode constants defined in TimberLoader.
     *
     * @return bool|string The echoed output.
     *
     * @see \Timber\Timber::render
     */
    public static function render( $filenames, array $data = [], $expires = false, string $cache_mode = \Timber\Loader::CACHE_USE_DEFAULT )
    {
        if (in_array('administrator',  wp_get_current_user()->roles)) {
            $caller = \Timber\LocationManager::get_calling_script_dir(1);
            $loader = new \Timber\Loader($caller);
            $file = $loader->choose_template($filenames);
            self::$renderedFilename = $file;
        }
        return Timber::render($filenames, $data, $expires, $cache_mode);
    }

    public static $renderedFilename = null;

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
        $pre   = __('Read Time: ', 'tenthtemplate');

        if ($rmins === 0 || $mins * 60 < 27.5) {
            return null;
        }
        if ($rmins < 1) {
            $secs = round($mins * 12) * 5;
            return $pre . sprintf(
            /* translators: %s: number of seconds. */
                __( '%s seconds', 'tenthtemplate'),
                $secs
            );
        }
        if ($rmins === 1.0) {
            return $pre . sprintf(
            /* translators: %s: 1 */
                __( '%s minute', 'tenthtemplate' ),
                $rmins
            );
        }
        if ($rmins < 4) {
            return $pre . sprintf(
            /* translators: %s: number of minutes. */
                __( '%s minutes', 'tenthtemplate' ),
                $rmins
            );
        }
        return $pre . sprintf(
        /* translators: %s: number of minutes. */
            __( '%s minutes', 'tenthtemplate' ),
            round($mins)
        );
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

    private static $_breadcrumbs = null;

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
    public static function addOrdinalIndicator($num): string
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

        if (false !== strpos(trailingslashit($url), home_url('/'))) {
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
                        '/pagename=\$matches\[([0-9]+)\]/',
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
                foreach ((array)$query_vars as $key => $value) {
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