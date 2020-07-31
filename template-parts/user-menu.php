
<?php

if (get_current_user_id() > 0) {
	$current_user = wp_get_current_user();
	echo "<label><img src=\"" . get_avatar_url($current_user->ID) . "\" /></label>";
	echo "<div>";
	echo $current_user->first_name . " " . $current_user->last_name;
	echo "</div>";
} else {
	echo "<label class=\"las la-user\"><!-- TODO --></label>"; // TODO: icon for users without images
	echo "<div>";
	_e('Sign In', 'tenthtemplate');
	echo "</div>";
}
?>
</div>
