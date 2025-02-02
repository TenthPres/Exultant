<?php
/**
 * The main template file
 *
 * @package Tenth_Template
 */


require_once 'vendor/autoload.php';

use Timber\Timber;
use tp\TenthTemplate\Exultant;

$context          = Timber::context();
require 'commonContext.php';
$context['posts'] = Timber::get_posts();
$templates        = ['templates/index.twig'];
if ( is_home() ) {
    array_unshift( $templates, 'templates/front-page.twig', 'templates/home.twig', 'templates/archive.twig');
}
Exultant::render( $templates, $context );
