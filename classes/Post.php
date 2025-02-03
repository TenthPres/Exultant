<?php

namespace tp\Exultant;

use tp\TouchPointWP\Involvement;
use tp\TouchPointWP\Partner;
use tp\TouchPointWP\TouchPointWP;
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
    public function toObject()
    {
        $post = get_post($this->id);

        if ($this->post_type == "tp_partner") {
            return Partner::fromPost($post);
        }

        if (substr($this->post_type, 0, 7) == "tp_inv_") {
            return Involvement::fromPost($post);
        }

        return null;
    }
}