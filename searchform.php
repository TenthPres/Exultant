<?php
/**
 * The searchform.php template.
 *
 * Used any time that get_search_form() is called.
 *
 * Optional arguments:
 *   - id -> used for the id attr of the text input elt
 *   - label -> adds a label elt with the given text/html
 *   - placeholder -> puts the given text in the placeholder of the text elt
 *   - submit -> puts the given text in the submit button value
 *
 * @package Tenth_Template
 */

$search_elt_id = $args['id'] ?? template_unique_id( 'search-form-' );
$placeholder = $args['placeholder'] ?? esc_attr_x( 'Search&hellip;', 'placeholder', 'tenthtemplate' );
$submit = $args['submit'] ?? esc_attr_x( 'Search', 'submit button', 'tenthtemplate' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <?php if (!empty($args['label'])) { ?>
        <label for="<?php echo esc_attr($search_elt_id); ?>"><?php echo esc_html($args['label']) ?></label>
    <?php } ?>
    <input type="search" id="<?php echo esc_attr( $search_elt_id ); ?>" placeholder="<?php echo $placeholder ?>"
           value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
	<input type="submit" value="<?php echo $submit; ?>" />
</form>
