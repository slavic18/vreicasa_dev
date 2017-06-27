<?php
function exampleProcessFormFunc()
{
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['form_type']) && $_POST['form_type'] == 'notify_on_available')) {
        try {

            project_errors()->add('login_success', __('We will notify you when this product will be in stock', 'project'));

        } catch (Exception $e) {
            project_errors()->add('notify_on_available_errors', __($e->getMessage(), 'project'));
        }
    }
}

//add_action('init', 'notifyOnAvailable', 1);

// used for tracking error messages
function project_errors()
{
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function project_show_error_messages($type = false)
{
    $html = '';
    if ($codes = project_errors()->get_error_codes()) {
        $html .= '<div class="error-block">';
        // Loop error codes and display errors
        foreach ($codes as $code) {
            if ($type && $type == $code) {
                $message = project_errors()->get_error_message($code);
                $html .= '<span class="error">' . $message . '</span><br/>';
            }
        }
        $html .= '</div>';
    }
    return $html;
}


function processApartmentContactForm()
{

    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['form_type']) && $_POST['form_type'] == 'contact_form')) {
        try {
            $sanitizedFormFields = [];
            $form = $_POST['form'];
            foreach (['name', 'email', 'phone', 'details'] as $field) {
                $sanitizedFormFields[$field] = sanitize_text_field($form[$field]);
            }

            $html = '';
            $subject = __('New request on ') . get_bloginfo('name');
            foreach ($sanitizedFormFields as $key => $field) {
                $html .= $key . ' : ' . $field . PHP_EOL;
            }
            wp_mail(get_bloginfo('admin_email'), $subject, $html);
            project_errors()->add('successfully_send_contact_email', __('Success', 'project'));

        } catch (Exception $e) {
            project_errors()->add('notify_on_available_errors', __($e->getMessage(), 'project'));
        }
    }
}

add_action('init', 'processApartmentContactForm', 1);




