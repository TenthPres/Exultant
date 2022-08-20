<?php
/**
 * The template for displaying Author Archive pages
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 */

use Timber\PostQuery;
use Timber\Timber;
use tp\TenthTheme;
use tp\TouchPointWP\Person;

global $wp_query;

$context          = Timber::context();
require 'commonContext.php';
$context['posts'] = new PostQuery();
$context['type']  = "Person";
if ( isset( $wp_query->query_vars['author'] ) ) {
    $person = Person::fromId($wp_query->query_vars['author']);
    if ($person !== null) {
        $context['person'] = $person;
        $context['title']  = $person->display_name;

        TenthTheme::render( [ 'person.twig', 'archive.twig' ], $context );
    }

     // TODO else: render an error
}
// TODO else: render an error