<?php
/**
 * The main template file
 *
 * @package Tenth_Template
 */

use Timber\PostQuery;
use Timber\Timber;

$context          = Timber::context();
require 'commonContext.php';
$context['posts'] = new PostQuery();
$templates        = ['index.twig'];
if ( is_home() ) {
    array_unshift( $templates, 'front-page.twig', 'home.twig' );
}
Timber::render( $templates, $context );
