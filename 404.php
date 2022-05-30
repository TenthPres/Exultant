<?php
/**
 * The template for displaying the 404 template in the Tenth Template theme.
 *
 * @package Tenth_Template
 */

use Timber\Timber;

$context = Timber::context();
require 'commonContext.php';
Timber::render( '404.twig', $context );

