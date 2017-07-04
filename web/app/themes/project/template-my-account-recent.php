<?php
/**
 * Template name: My account page - recent
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['accountMenu'] = getMyAccountMenu();

Timber::render( array( 'page-my-account-recent.twig', 'page.twig' ), $context );