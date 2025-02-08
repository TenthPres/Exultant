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

use tp\Exultant;
use tp\TouchPointWP\Involvement;
use Timber\Timber;

$templates = [ 'templates/archive.twig', 'templates/index.twig' ];

$context = Timber::context();

$context['title'] = 'Archive';
$context['type'] = null;
if ( is_day() ) {
    $ordinalDate = Exultant::addOrdinalIndicator(get_the_date( 'j' ));
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
    $type = get_post_type();
    $context['type'] = $type;
    $addTemplates = [];
    $addTemplates[] = 'templates/archive-' . $type . '.twig';
    if (Involvement::postTypeMatches($type)) {
        $settings = Involvement::getSettingsForPostType($type);
        $context['use_geo'] = $settings->useGeo;
        $addTemplates[] = "templates/archive-tp_inv.twig";
    }
    array_unshift( $templates, ...$addTemplates);
}

$context['posts'] = Timber::get_posts();

Exultant::render( $templates, $context );