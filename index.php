<?php
/**
 * The main template file
 *
 * @package Tenth_Template
 */

$context          = Timber::context();
$context['posts'] = new Timber\PostQuery();
$templates        = ['index.twig'];
if ( is_home() ) {
    array_unshift( $templates, 'front-page.twig', 'home.twig' );
}
Timber::render( $templates, $context );
