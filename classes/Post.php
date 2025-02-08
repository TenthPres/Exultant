<?php

namespace tp\Exultant;

use tp\TouchPointWP\Involvement;
use tp\TouchPointWP\Partner;
use tp\TouchPointWP\PostTypeCapable;
use tp\TouchPointWP\TouchPointWP_Exception;

class Post extends \Timber\Post
{
    public function preview()
    {
        return new PostPreview($this);
    }

    public function set___content($value): void
    {
        $this->___content = $value;
    }

    public function get___content(): ?string
    {
        return $this->___content;
    }

    public function disableShortcodes()
    {
        $this->___content = null;
        add_filter('the_content', 'strip_shortcodes', 10);
    }

    public function enableShortcodes()
    {
        $this->___content = null;
        remove_filter('the_content', 'strip_shortcodes', 10);
    }

    /**
     * For post types that are intricately intertwined with a particular class, this returns the object.
     *
     * @throws TouchPointWP_Exception
     */
    public function toObject(): ?PostTypeCapable
    {
        $post = get_post($this->id);

        if (Partner::postIsType($post)) {
            return Partner::fromPost($post);
        }

        if (Involvement::postIsType($post)) {
            return Involvement::fromPost($post);
        }

        return null;
    }
}