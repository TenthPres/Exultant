<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/templates/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib subdirectory
 *
 */

use tp\TenthTemplate\Exultant;

$context = Timber\Timber::context();
require 'commonContext.php';
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;
$context['typeInfo']['includeByline'] = false;
Exultant::render( [ 'page-' . $timber_post->post_name . '.twig', 'page.twig' ], $context );