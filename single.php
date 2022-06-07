<?php
/**
 * The template for displaying single posts and pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tenth_Template
 * @since Tenth Template 1.0
 */

use Timber\Timber;

$context         = Timber::context();
require 'commonContext.php';
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

if ( post_password_required( $timber_post->ID ) ) {
    Timber::render( 'single-password.twig', $context );
} else {
    Timber::render( array( 'single-' . $timber_post->ID . '.twig', 'single-' . $timber_post->post_type . '.twig', 'single-' . $timber_post->slug . '.twig', 'single.twig' ), $context );
}