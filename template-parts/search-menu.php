<?php

$searchId        = 'search-input';
$searchResultsId = 'search-results-list';

?>
<label class="las la-search" for="<?php echo $searchId; ?>"></label>
<div id="search-menu"><!-- equiv to ul -->
    <div><!-- equiv to li -->
        <?php
        get_search_form(
            [
                'placeholder' => __('Search', 'Exultant'),
                'id'          => $searchId,
                'resultsId'   => $searchResultsId
            ]
        );
        ?>
        <div>
            <ul id="<?php echo $searchResultsId; ?>" class="search-results">
                <li><span class="start-typing" id="search-results-status"><?php _e("Start Typing...", 'Exultant'); ?></span></li>
            </ul>
        </div>
    </div>
</div>