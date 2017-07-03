<?php
/**
 * Template name: Login page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['form'] = isset($_REQUEST['login']) ? $_POST['login'] : [];
Timber::render( array( 'page-login.twig', 'page.twig' ), $context );