<?php

namespace tp;

require_once __DIR__ . '/vendor/autoload.php';

use Timber\Timber;

Timber::init();

Timber::$dirname = ['views', 'views/templates', 'views/partials'];

// tell TouchPoint-WP not to use its default templates.
add_filter('tp_use_default_templates', '__return_false');


// Entry point for the theme.
Exultant::instance();
