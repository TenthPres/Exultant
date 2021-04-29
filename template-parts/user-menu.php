<?php

/* Icon or Profile Picture */
if (get_current_user_id() > 0 && get_avatar_url(get_current_user_id()) !== "") {
    echo "<label><img src=\"" . get_avatar_url(get_current_user_id(), ['size' => 64]) . "\" /></label>";
} else {
    echo "<label class=\"las la-user\"></label>";
} ?>

<ul>
    <li>
        <?php
        if (get_current_user_id() === 0) {
            echo "<a href=\"" . wp_login_url(get_permalink()) . "\">" . __('Sign In', 'tenthtemplate') . "</a>";
        } else {
            $current_user = wp_get_current_user();
            if ($current_user->first_name . $current_user->last_name === "") {
                echo "<span>" . $current_user->user_nicename . "</span>";
            } else {
                echo "<span>" . $current_user->first_name . " " . $current_user->last_name . "</span>";
            }
            echo "<div id=\"userMenu\">";

            if (class_exists(TenthAdminMenu::class)) {
                TenthAdminMenu::renderSingleton(true);
            }
            ?>
            <ul>
                <li><?php
                    echo "<a href=\"" . get_edit_profile_url() . "\">";
                    echo __("My Profile", "tenthtemplate");
                    echo "</a>"; ?>
                    <ul>
                        <li><?php
                            echo "<a href=\"" . wp_logout_url(get_permalink()) . "\">";
                            echo __("Logout", "tenthtemplate");
                            echo "</a>"; ?>
                        </li>
                    </ul>
                </li>
            </ul>
        <?php
            echo "</div>";
        } ?>
    </li>
</ul>

