<?php
/**
 * Template name: My account page - configure
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['accountMenu'] = getMyAccountMenu();


$context['formData'] = isset($_POST['form']) ? $_POST['form'] : [];
//get current user data need for edit user form
$user = wp_get_current_user();
$arrayFormatUserMeta = get_user_meta($user->data->ID, '', true);
$allUserMeta = [];
foreach($arrayFormatUserMeta as $key => $val) {
    $allUserMeta[$key] = (is_array($val) && count($val)) ? $val[0] : '';
}
$context['userMeta'] = $allUserMeta;
// get main profile form data
$context['userEmail'] = isset($context['formData']['email']) ? $context['formData']['email'] : $user->data->user_email;

Timber::render( array( 'page-my-account-configure.twig', 'page.twig' ), $context );