<?php

//namespace tp\Exultant;  TODO this, probably?

require_once __DIR__ . '/vendor/autoload.php';

use Timber\Timber;

Timber::init();

Timber::$dirname = ['views', 'views/templates', 'views/partials'];


add_filter('tp_prevent_admin_bar', '__return_true');
add_filter('tp_use_default_templates', '__return_false');

function removeDashIcons() { // TODO move to a class.
    if ( ! is_admin() ) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}
add_action('wp_enqueue_scripts', 'removeDashIcons');
add_filter('show_admin_bar', '__return_false');

\tp\TenthTemplate\Exultant::instance();

//new Exultant(); TODO once namespace is fixed, uncomment.