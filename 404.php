<?php
/**
 * The template for displaying the 404 template in the Tenth Template theme.
 *
 * @package Tenth_Template
 */

use Timber\Timber;
use tp\Exultant;

$context = Timber::context();
Exultant::render('templates/404.twig', $context );
