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
use tp\TenthTheme;
use tp\TouchPointWP\Involvement;

$context         = Timber::context();
require 'commonContext.php';
/** @var Post $timber_post */
$timber_post     = Timber::get_post(false, Post::class);
$context['post'] = $timber_post;

// Password required.
if (post_password_required($timber_post->ID)) {
    TenthTheme::render('single-password.twig', $context);

} else {
    $context['object'] = $timber_post->toObject();

    $templates = [
        'single-' . $timber_post->ID . '.twig',
        'single-' . $timber_post->post_type . '.twig',
        'single-' . $timber_post->slug . '.twig',
        'single.twig'
    ];

    if (substr(get_post_type(), 0, 7) == "tp_inv_") {
        $settings = Involvement::getSettingsForPostType(get_post_type());
        $context['use_geo'] = $settings->useGeo;
        $addTemplates[] = "single-" . get_post_type() . ".twig";
        $addTemplates[] = "single-tp_inv.twig";
    }
    $addTemplates[] = 'single-' . get_post_type() . '.twig';

    array_unshift( $templates, ...$addTemplates);

    TenthTheme::render($templates, $context );
}