<?php
/**
 * The main template file
 *
 * @package Tenth_Template
 */

use Timber\Timber;
use tp\TenthTemplate\PostQuery;
use tp\TenthTheme;

$context          = Timber::context();
require 'commonContext.php';
$context['posts'] = new PostQuery();
$templates        = ['index.twig'];
if ( is_home() ) {
    array_unshift( $templates, 'front-page.twig', 'home.twig' );
}
TenthTheme::render( $templates, $context );
