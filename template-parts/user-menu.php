
<?php
/* Icon or Profile Picture */
if (get_current_user_id() > 0 && get_avatar_url(get_current_user_id()) !== "") {
	echo "<label><img src=\"" . get_avatar_url(get_current_user_id(), ['size' => 64]) . "\" /></label>";
} else {
	echo "<label class=\"las la-user\"></label>";
}

echo "<div>";
if (get_current_user_id() > 0) {
	$current_user = wp_get_current_user();
	echo $current_user->first_name . " " . $current_user->last_name;
} else {
	_e('Sign In', 'tenthtemplate');
}
echo "</div>";

