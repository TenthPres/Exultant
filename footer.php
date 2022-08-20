<?php
/**
 * Footer file for the Tenth Template theme.
 *
 * @package Tenth_Template
 */

use tp\TenthTheme;

$timberContext = $GLOBALS['timberContext'];
if ( ! isset( $timberContext ) ) {
    throw new \Exception( 'Timber context not set in footer.' );
}
$timberContext['content'] = ob_get_contents();
ob_end_clean();
$templates = [ 'page-plugin.twig' ];
TenthTheme::render( $templates, $timberContext );

