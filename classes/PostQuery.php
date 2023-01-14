<?php

namespace tp\TenthTemplate;

class PostQuery extends \Timber\PostQuery
{
    public function __construct($query = false, $post_class = '\tp\TenthTemplate\Post')
    {
        parent::__construct($query, $post_class);
    }
}