<?php
/**
 * Template name: My account page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render( array( 'page-my-account.twig', 'page.twig' ), $context );