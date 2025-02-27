<?php

namespace tp\Exultant;

use tp\TouchPointWP\PostTypeCapable;
use tp\TouchPointWP\TouchPointWP_Exception;

class Post extends \Timber\Post
{
    /**
     * Get the excerpt for the post.
     *
     * @param array $options
     * @return PostExcerpt
     */
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

    /**
     * Allows for this class to be instantiated as the default post class from Timber.
     */
    public static function classMap($classMap): array
    {
        $posts = get_post_types();
        foreach ($posts as $postType) {
            $classMap[$postType] = Post::class;
        }
        return $classMap;
    }
}