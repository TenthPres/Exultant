<?php

/* Icon or Profile Picture */

use tp\TenthTemplate\Exultant;

if (get_current_user_id() > 0 && get_avatar_url(get_current_user_id()) !== "") {
    echo "<label><img src=\"" . get_avatar_url(get_current_user_id(), ['size' => 64]) . "\" /></label>";
} else {
    echo "<label class=\"las la-user\"></label>";
} ?>

<ul>
    <li>
        <?php
        if (get_current_user_id() === 0) {
            echo "<a href=\"" . wp_login_url(get_permalink()) . "\">" . __('Sign In', 'TenthTemplate') . "</a>";
        } else {
            $current_user = wp_get_current_user();
            if ($current_user->first_name . $current_user->last_name === "") {
                echo "<span>" . $current_user->user_nicename . "</span>";
            } else {
                echo "<span>" . trim($current_user->first_name . " " . $current_user->last_name) . "</span>";
            }
            echo "<div id=\"userMenu\">";

            if (class_exists(TenthAdminMenu::class)) {
                TenthAdminMenu::renderSingleton(true);
            }
            ?>
            <ul>
                <li><?php
                    echo "<a href=\"" . get_edit_profile_url() . "\">";
                    echo __("My Profile", "TenthTemplate");
                    echo "</a>"; ?>
                    <ul>
                        <li><?php
                            echo "<a href=\"" . wp_logout_url(get_permalink()) . "\">";
                            echo __("Logout", "TenthTemplate");
                            echo "</a>"; ?>
                        </li>
                    </ul>
                </li>
                <?php
                if (in_array('administrator',  wp_get_current_user()->roles)) {
                    global $template;
                    $phpTemplate  = str_replace(get_template_directory(), "", $template);
                    $postType     = get_post_type(get_queried_object());
                    ?>
                    <li>
                        <span><?php _e("PHP Template", "TenthTemplate") ?></span>
                        <ul>
                            <li><span><?php echo $phpTemplate; ?></span></li>
                        </ul>
                    </li>
                    <li>
                        <span><?php _e("Twig Template", "TenthTemplate") ?></span>
                        <ul>
                            <li><span><?php echo Exultant::$renderedFilename; ?></span></li>
                        </ul>
                    </li>
                    <li>
                        <span><?php _e("Post Type", "TenthTemplate") ?></span>
                        <ul>
                            <li><span><?php echo $postType; ?></span></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        <?php
            echo "</div>";
        } ?>
    </li>
</ul>
