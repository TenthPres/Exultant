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
use tp\TenthTemplate\Post;
use tp\TenthTemplate\Exultant;
use tp\TouchPointWP\Involvement;

$context         = Timber::context();
require 'commonContext.php';
/** @var Post $timber_post */
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

// Password required.
if (post_password_required($timber_post->ID)) {
    Exultant::render('single-password.twig', $context);

} else {
    $context['object'] = $timber_post->toObject();

    $templates = [
        'templates/single-' . $timber_post->ID . '.twig',
        'templates/single-' . $timber_post->post_type . '.twig',
        'templates/single-' . $timber_post->slug . '.twig',
        'templates/single.twig'
    ];

    if (str_starts_with(get_post_type(), "tp_inv_")) {
        $settings = Involvement::getSettingsForPostType(get_post_type());
        $context['use_geo'] = $settings->useGeo;
        $addTemplates[] = "templates/single-" . get_post_type() . ".twig";
        $addTemplates[] = "templates/single-tp_inv.twig";
    }
    $addTemplates[] = 'templates/single-' . get_post_type() . '.twig';

    array_unshift( $templates, ...$addTemplates);

    Exultant::render($templates, $context );
}