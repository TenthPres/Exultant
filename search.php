<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib subdirectory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */


use Timber\Timber;
use tp\Exultant;

add_action('wp_ajax_load_search_results', 'ajaxSearchResults' );
add_action('wp_ajax_nopriv_load_search_results', 'ajaxSearchResults' );

$templates = ['templates/search.twig', 'templates/archive.twig', 'templates/index.twig'];

$context          = Timber::context();
$context['title'] = __('Search: ', 'TenthTemplate') . get_search_query();  // TODO i18n correctly.
$context['posts'] = Timber::get_posts();

Exultant::render($templates, $context);
