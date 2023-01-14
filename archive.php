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

use tp\TenthTemplate\PostQuery;
use tp\TenthTheme;
use tp\TouchPointWP\Involvement;

$templates = [ 'archive.twig', 'index.twig' ];

$context = Timber\Timber::context();
require 'commonContext.php';

$context['title'] = 'Archive';
$context['type'] = null;
if ( is_day() ) {
    $ordinalDate = TenthTheme::addOrdinalIndicator(get_the_date( 'j' ));
    $context['title'] = str_replace("%", $ordinalDate, get_the_date( 'F %, Y' ));
    $context['type'] = "Date";
} elseif ( is_month() ) {
    $context['title'] = get_the_date( 'F Y' );
    $context['type'] = "Date";
} elseif ( is_year() ) {
    $context['title'] = get_the_date( 'Y' );
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
    $context['type'] = get_post_type();
    $addTemplates = [];
    if (substr(get_post_type(), 0, 7) == "tp_inv_") {
        $settings = Involvement::getSettingsForPostType(get_post_type());
        $context['use_geo'] = $settings->useGeo;
        $addTemplates[] = "archive-" . get_post_type() . ".twig";
        $addTemplates[] = "archive-tp_inv.twig";
    }
    $addTemplates[] = 'archive-' . get_post_type() . '.twig';
    array_unshift( $templates, ...$addTemplates);
}

$context['posts'] = new PostQuery();

TenthTheme::render( $templates, $context );