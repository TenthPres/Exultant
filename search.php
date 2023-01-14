<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */


use Timber\Timber;
use tp\TenthTemplate\PostQuery;
use tp\TenthTheme;

add_action('wp_ajax_load_search_results', 'ajaxSearchResults' );
add_action( 'wp_ajax_nopriv_load_search_results', 'ajaxSearchResults' );

$templates = array( 'search.twig', 'archive.twig', 'index.twig' );

$context          = Timber::context();
require 'commonContext.php';
$context['title'] = __('Search: ') . get_search_query();
$context['posts'] = new PostQuery();

TenthTheme::render( $templates, $context );
