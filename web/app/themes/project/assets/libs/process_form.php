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
        $html .= '<div class="error-block alert alert-danger bs-alert-old-docs">';
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


function registerUser()
{
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['registration']))) {
        try {
            $form = (array)$_POST['registration'];
            $requiredFields = [
                'email',
                'password',
                'repeat_password',
            ];


            //sanitize form data

            foreach ($form as $key => $val) {
                $form[$key] = sanitize_text_field($val);
            }

            foreach ($requiredFields as $requiredField) {
                if (!isset($form[$requiredField]) || empty($form[$requiredField])) {
                    throw new Exception('<< ' . $requiredField . ' >> is required field!');
                }
            }

            if ($form['password'] !== $form['repeat_password']) {
                throw new Exception ('Passwords must be equal');
            }

            if (strlen($form['password']) < 6) {
                throw new Exception ('Passwords must have minimum 6 characters');
            }


            if (!is_email($form['email'])) {
                throw new Exception ('Write valid email');
            }

            //          end validation

            $userId = wp_insert_user([
                'user_login' => $form['email'],
                'user_email' => $form['email'],
                'user_pass' => $form['password'],
                'role' => 'subscriber',
            ]);

            if (is_wp_error($userId)) {
                throw new Exception($userId->get_error_message());
            }
            // register users with physical type
            if ($form['subscribe']) {
                update_user_meta($userId, 'allow_to_subscribe', '1');
            }

            wp_signon([
                'user_login' => $form['email'],
                'user_password' => $form['password'],
                true
            ]);
            wp_redirect(home_url());
            exit();

        } catch (Exception $e) {
            project_errors()->add('registration_errors', __($e->getMessage(), 'project'));
        }
    }
}

add_action('init', 'registerUser', 1);


function loginByFormData()
{
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['login']['email']) && !empty($_POST['login']['email']))) {
        $email = $_POST['login']['email'];
        $password = $_POST['login']['password'];
        $remember = true;
        if (!empty($email) && is_email($email)) {
            $user = get_user_by('email', $email);
            if (isset($user, $user->user_login, $user->user_status)) {
                if (0 === intval($user->user_status)) {
                    $signOnResult = wp_signon(array(
                        'user_login' => $user->user_login,
                        'user_password' => $password,
                        'remember' => $remember,
                    ));

                    if (!is_wp_error($signOnResult)) {

                        wp_set_auth_cookie($user->ID, $password, true);

                        wp_set_current_user($user->ID, $user->user_login);
                        do_action('wp_login', $user->user_login);
                        project_errors()->add('login_success', __('Ati intrat cu success', 'project'));

                        wp_redirect(home_url());
                        exit;
                    }
                }
            }
        }
        project_errors()->add('login_errors', __('Eroare: Ati introdus email-ul sau parola gresit', 'project'));
    }
}

add_action('init', 'loginByFormData', 1);


function updateMainUserData()
{
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['form_type']) && $_POST['form_type'] == 'edit_user_main_data')) {
        try {
            $form = $_POST['form'];
            $currentUserId = get_current_user_id();
            foreach ($form as $key => $val) {
                $form[$key] = sanitize_text_field($val);
            }

            // start validation
            if (isset($form['email'])) {
                if (!is_email($form['email'])) {
                    throw new Exception('Write valid email');
                }
                $emailExist = email_exists($form['email']);
                if ($emailExist !== false && $emailExist !== $currentUserId) {
                    throw new Exception('This email is already used by another user');
                }
            }

            // end validation
            $userdata = [
                'ID' => $currentUserId,
                'user_login' => $form['email'],
                'user_email' => $form['email']
            ];


            if (isset($form['pass']) && !empty($form['pass'])) {
                if (!isset($form['repeat_pass']) || empty($form['repeat_pass'])) {
                    throw new Exception('Repeat your pass');
                }
                if ($form['pass'] !== $form['repeat_pass']) {
                    throw new Exception ('Passwords must be equal');
                }
                if (strlen($form['pass']) < 6) {
                    throw new Exception ('Passwords must have minimum 6 characters');
                }
                $userdata['user_pass'] = $form['pass'];
            }


            wp_update_user($userdata);
            update_user_meta($currentUserId, 'updated_date', date('d.m.Y'));
            project_errors()->add('login_success', __('Profile successfully updated', 'project'));


        } catch (Exception $e) {
            project_errors()->add('edit_main_user_data', __($e->getMessage(), 'project'));
        }
    }
}

add_action('init', 'updateMainUserData', 1);


function processTestimonialsContactForm()
{
    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['form_type']) && $_POST['form_type'] == 'testimonials')) {
        try {
            $sanitizedFormFields = [];
            $form = $_POST['form'];
            foreach (['how-you-find-site', 'nota-pentru-site', 'folosire-de-vreicasa', 'ajustari', 'alt-sfat'] as $field) {
                if (isset($form[$field])) {
                    $sanitizedFormFields[$field] = sanitize_text_field($form[$field]);
                }
            }

            $html = '';
            $subject = __('O noua parere despre site ', 'project') . get_bloginfo('name');
            foreach ($sanitizedFormFields as $key => $field) {
                $html .= $key . ' : ' . $field . PHP_EOL;
            }
            wp_mail(get_field('testimonials_email','options'), $subject, $html);
            project_errors()->add('successfully_send_testimonial_email', __('Success', 'project'));
        } catch (Exception $e) {
            project_errors()->add('notify_on_available_errors', __($e->getMessage(), 'project'));
        }
    }
}

add_action('init', 'processTestimonialsContactForm', 1);


