<?php
/**
 * Javascript Loader Class
 *
 * Allow `async` and `defer` while enqueuing Javascript.
 *
 * @package Exultant
 * @since Exultant 1.0
 */

namespace tp\Exultant;

class ExultantScriptLoader
{
    /**
     * Adds async/defer attributes to enqueued / registered scripts.  If -defer or -async is present in the script's
     * handle, the respective attribute is added.
     *
     * DOES apply to ALL scripts, not just those in the template.
     *
     * @param string $tag The script tag.
     * @param string $handle The script handle.
     *
     * @return string The HTML string.
     */
    public static function filterByTag($tag, $handle): string
    {
        if (!str_contains($tag, ' async') &&
            strpos($handle, '-async') > 0
        ) {
            $tag = str_replace(' src=', ' async="async" src=', $tag);
        }
        if (!str_contains($tag, ' defer') &&
            strpos($handle, '-defer') > 0
        ) {
            $tag = str_replace('<script ', '<script defer ', $tag);
        }

        return $tag;
    }
}