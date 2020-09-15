<?php
/**
 * Javascript Loader Class
 *
 * Allow `async` and `defer` while enqueuing Javascript.
 *
 * @package Tenth_Template
 * @since Tenth Template 1.0
 */

class TenthScriptLoader
{
    /**
     * Adds async/defer attributes to enqueued / registered scripts.
     *
     * @param string $tag The script tag.
     * @param string $handle The script handle.
     *
     * @return string The HTML string.
     */
    public function filter_script_loader_tag($tag, $handle)
    {
        foreach (['async', 'defer'] as $attr) {
            if ( ! wp_scripts()->get_data($handle, $attr)) {
                continue;
            }
            // Prevent adding attribute when already added in #12009.
            if ( ! preg_match(":\s$attr(=|>|\s):", $tag)) {
                $tag = preg_replace(':(?=></script>):', " $attr", $tag, 1);
            }
            // Only allow async or defer, not both.
            break;
        }

        return $tag;
    }
}