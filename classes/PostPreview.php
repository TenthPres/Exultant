<?php

namespace tp\TenthTemplate;

class PostPreview extends \Timber\PostPreview
{
    protected $shortcodeContent = null;

    protected function run()
    {
        // Remove shortcodes from previews.  Priority needs to be ahead of do_shortcodes (11) and behind do_blocks (9).
        add_filter('the_content', 'strip_shortcodes', 10);
        $oldContent = $this->post->get___content();
        $this->post->set___content($this->shortcodeContent);

        $r = parent::run();

        $this->shortcodeContent = $this->post->get___content();
        $this->post->set___content($oldContent);
        remove_filter('the_content', 'strip_shortcodes');

        return $r;
    }

    public function __toString() {
        return $this->run();
    }
}