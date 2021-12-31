<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 */

$templates = [ 'archive.twig', 'index.twig' ];

$context = Timber\Timber::context();

$context['title'] = 'Archive';
$context['type'] = null;
if ( is_day() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'D M Y' );
    $context['type'] = "Date";
} elseif ( is_month() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'M Y' );
    $context['type'] = "Date";
} elseif ( is_year() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'Y' );
    $context['type'] = "Date";
} elseif ( is_tag() ) {
    $context['title'] = single_tag_title( '', false );
    $context['type'] = "Tag";
} elseif ( is_category() ) {
    $context['title'] = single_cat_title( '', false );
    $context['type'] = "Category";
    array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
} elseif ( is_post_type_archive() ) {
    $context['title'] = post_type_archive_title( '', false );
    $context['type'] = post_type_archive_title( '', false ); // TODO figure out a better term here.
    array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}

$context['posts'] = new Timber\PostQuery();

Timber\Timber::render( $templates, $context );