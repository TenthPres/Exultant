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
use tp\Exultant\Post;
use tp\Exultant;
use tp\TouchPointWP\Involvement;

$context         = Timber::context();
/** @var Post $timber_post */
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

// Password required.
if (post_password_required($timber_post->ID)) {
    Exultant::render('single-password.twig', $context);

} else {
    $context['object'] = $timber_post->toObject();

    $templates = [
        "templates/single-$timber_post->ID.twig",
        "templates/single-$timber_post->slug.twig",
        "templates/single-$timber_post->post_type.twig",
        "templates/single.twig"
    ];

    $type = get_post_type();
    $addTemplates[] = "templates/single-$type.twig";
    if (Involvement::postTypeMatches($type)) {
        $settings = Involvement::getSettingsForPostType($type);
        $context['use_geo'] = $settings->useGeo;
        $addTemplates[] = "templates/single-tp_inv.twig";
    }

    array_unshift( $templates, ...$addTemplates);

    Exultant::render($templates, $context );
}