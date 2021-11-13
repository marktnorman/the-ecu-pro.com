<?php

function disable_feed()
{
    wp_die(__('No feed available, please visit the <a href="' . esc_url(home_url('/')) . '">home page</a>.'));
}

add_action('do_feed', 'disable_feed', 1);
add_action('do_feed_rdf', 'disable_feed', 1);
add_action('do_feed_rss', 'disable_feed', 1);
add_action('do_feed_rss2', 'disable_feed', 1);
add_action('do_feed_atom', 'disable_feed', 1);
add_action('do_feed_rss2_comments', 'disable_feed', 1);
add_action('do_feed_atom_comments', 'disable_feed', 1);
remove_action('wp_head', 'feed_links', 2);
add_filter(
    'post_comments_feed_link',
    function () {
        return null;
    }
);

global $tecup_form_error, $tecup_form_success, $tecup_form_show_number;
$tecup_form_error = false;
$tecup_form_success = false;
$tecup_form_show_number = false;

if (isset($_POST['tecup-action'])) {
    $function = $_POST['tecup-action'];
    $function();
}

function insertNewComment($name, $wp_comment_content)
{
    global $current_user, $tecup_form_success;
    $comment_data = array(
        'comment_post_ID' => 73,  //contact page
        'comment_author' => $name,
        'comment_author_email' => $_POST['email'] ?? '',
        'comment_author_url' => '',
        'comment_content' => wpautop($wp_comment_content),
        'comment_type' => '',
        'comment_parent' => 0,
        'user_id' => $current_user->ID,
    ); ?>

    <script>
        // Lets save in localstorage that the form has been submitted
        localStorage.setItem('form-submitted', 'true');
    </script>

    <?php

    // Check before submission
    global $wpdb;

    $comments_table = 'wp_comments';

    $comments_id = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT comment_ID AS ID FROM $wpdb->comments WHERE comment_author_email LIKE '%s' LIMIT 1;",
            '%' . $_POST['email'] . '%'
        )
    );

    // Doesnt exist so submit
    if (empty($comments_id)) {
        wp_new_comment($comment_data);
    }

    if (!empty($_POST['redirect_url'])) {
        wp_redirect($_POST['redirect_url']);
        exit;
    } ?>

    <?php $tecup_form_success = true;
    return true;
}

function save_click_to_call()
{
    global $tecup_form_error, $tecup_form_show_number;
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['cname'])) {
        $wp_comment_content = empty($_POST['phone']) ? 'Click to call submission. Phone number not provided.' : '<p>Click to call submission. Phone number: ' . $_POST['phone'] . '</p>';
        if (insertNewComment($_POST['cname'], $wp_comment_content)) {
            $tecup_form_show_number = true;
        }
    } else {
        $tecup_form_error = true;
    }
}

function save_contact_message()
{
    global $tecup_form_error;
    if (filter_var(
            $_POST['email'],
            FILTER_VALIDATE_EMAIL
        ) && !empty($_POST['cname']) && !empty($_POST['vehicle']) && !empty($_POST['message'])) {
        $name = $_POST['cname'];
        $wp_comment_content = '<p>Vehicle: ' . $_POST['vehicle'] . '</p>';
        if (!empty($_POST['phone'])) {
            $wp_comment_content .= '<p>Phone: ' . $_POST['phone'] . '</p>';
        }
        $wp_comment_content .= '<p>' . $_POST['message'] . '</p>';
        insertNewComment($name, $wp_comment_content);
    } else {
        $tecup_form_error = true;
    }
}

function field_error_class($are_errors, $is_form, $name, $validation)
{
    if ($are_errors && $is_form == $_POST['which-form']) {
        if ($validation == 'required' && empty($_POST[$name])) {
            echo 'border border-danger';
        }
        if ($validation == 'email' && !filter_var($_POST[$name], FILTER_VALIDATE_EMAIL)) {
            echo 'border border-danger';
        }
    }
}

function formMsg($which_form, $error_only)
{
    global $tecup_form_error, $tecup_form_success;
    $isThisForm = isset($_POST['which-form']) && $_POST['which-form'] === $which_form;
    $showError = $tecup_form_error && $isThisForm;
    $showSuccess = $tecup_form_success && $isThisForm;
    if ($showError) {
        echo '<div class="border border-danger text-danger p-3 mt-4 mb-2" data-js-ref="lap-form-msg">Some of the fields were not entered correctly. Please see the highlighted fields below.</div>';
    } elseif ($showSuccess && !$error_only) {
        echo '<div class="border border-success text-success p-3 mb-3" data-js-ref="lap-form-msg">Message successfully submitted. We will respond within {X AMOUNT OF TIME}.</div>';
    }
}
