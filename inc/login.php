<?php

    /**
     * Replace login logo
     */
    function my_login_logo()
    { ?>
        <style type="text/css">
            .login h1 a {
                background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo.png);
                padding-bottom: 30px;
                background-size: 200px;
                width: 200px;
            }
        </style>
    <?php }

    add_action('login_enqueue_scripts', 'my_login_logo');


    /**
     * Redirect user to login page on fail
     */
    add_action('login_redirect', 'redirect_login', 10, 3);
    function redirect_login($redirect_to, $url, $user)
    {
        if ($user->errors['empty_password']) {
            wp_redirect(get_bloginfo('url') . '/login/?login=failed');
        } else if ($user->errors['empty_username']) {
            wp_redirect(get_bloginfo('url') . '/login/?login=failed');
        } else if ($user->errors['invalid_username']) {
            wp_redirect(get_bloginfo('url') . '/login/?login=failed');
        } else if ($user->errors['incorrect_password']) {
            wp_redirect(get_bloginfo('url') . '/login/?login=failed');
        } else if ($user->id != 0) {
            // User was successfully logged in, redirect home
            wp_redirect(get_bloginfo('url'));

        } else {
            // Empty form submission, or on logout
            wp_redirect(get_bloginfo('url') . '/login/');
        }
        exit;
    }