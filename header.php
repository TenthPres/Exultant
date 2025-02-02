<?php
/**
 * Header file for the Tenth Template theme.  Hypothetically, this should only be used by
 * plugin-driven pages.
 *
 * @package Tenth_Template
 * @see footer.php
 */

require_once 'vendor/autoload.php';

$GLOBALS['timberContext'] = Timber\Timber::context();
require 'commonContext.php';
ob_start();

