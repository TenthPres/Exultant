<?php

/* Icon or Profile Picture */

use tp\Exultant\AdminMenu;
use tp\Exultant;

$userId = get_current_user_id();
if ($userId > 0 && !!get_avatar_url($userId)) {
    $usersName = wp_get_current_user()->first_name;
    $avatar    = get_avatar_url($userId, ['size' => 64]);
    echo "<label><img src=\"$avatar\" alt=\"$usersName\" /></label>";
} else {
    echo "<label class=\"las la-user\"></label>";
} ?>

<ul>
    <li>
        <?php
        if ($userId === 0) {
            echo "<a href=\"" . wp_login_url(get_permalink()) . "\">" . __('Sign In', 'Exultant') . "</a>";
        } else {
            $current_user = wp_get_current_user();
            if ($current_user->first_name . $current_user->last_name === "") {
                $userPrettyName = $current_user->user_nicename;
            } else {
                $userPrettyName = trim($current_user->first_name . " " . $current_user->last_name);
            }
            echo "<span>$userPrettyName</span>";
            echo "<div id=\"userMenu\">";

            if (!is_customize_preview()) {
                AdminMenu::renderSingleton(true);
            }
            ?>
            <ul>
                <li><?php
                    echo "<a href=\"" . get_edit_profile_url() . "\">";
                    echo __("My Profile", "Exultant");
                    echo "</a>"; ?>
                    <ul>
                        <li><?php
                            echo "<a href=\"" . wp_logout_url(get_permalink()) . "\">";
                            echo __("Logout", "Exultant");
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
                        <span><?php _e("PHP Template", "Exultant") ?></span>
                        <ul>
                            <li><span><?php echo $phpTemplate; ?></span></li>
                        </ul>
                    </li>
                    <li>
                        <span><?php _e("Twig Template", "Exultant") ?></span>
                        <ul>
                            <li><span><?php echo Exultant::$renderedFilename; ?></span></li>
                        </ul>
                    </li>
                    <li>
                        <span><?php _e("Post Type", "Exultant") ?></span>
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