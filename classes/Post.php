<?php

namespace tp\Exultant;

use tp\TouchPointWP\PostTypeCapable;
use tp\TouchPointWP\TouchPointWP_Exception;

class Post extends \Timber\Post
{
    public function excerpt(array $options = []): PostExcerpt
    {
        return new PostExcerpt($this, $options);
    }

    /**
     * For post types that are intricately intertwined with a particular class, this returns the object.
     *
     * @throws TouchPointWP_Exception
     */
    public function toObject(): ?PostTypeCapable
    {
        $post = get_post($this->id);

        return PostTypeCapable::fromPost($post);
    }
}