<?php
/**
 * The template for displaying the 404 template in the Tenth Template theme.
 *
 * @package Tenth_Template
 */

use Timber\Timber;
use tp\TenthTheme;

$context = Timber::context();
require 'commonContext.php';
TenthTheme::render('404.twig', $context );

