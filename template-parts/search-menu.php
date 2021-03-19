<?php

$searchId        = wp_unique_id('search-form-');
$searchResultsId = wp_unique_id('search-list-');

?>
<label class="las la-search" for="<?php echo $searchId; ?>"></label>
<div><!-- equiv to ul -->
    <div><!-- equiv to li -->
        <?php
        get_search_form(
            [
                'placeholder' => __('Search', 'TenthTemplate'),
                'id'          => $searchId,
                'resultsId'   => $searchResultsId
            ]
        );
        ?>
        <div>
            <ul id="<?php echo $searchResultsId; ?>" class="search-results">
                <li><span class="start-typing">Start Typing...</span></li>
            </ul>
        </div>
    </div>
</div>