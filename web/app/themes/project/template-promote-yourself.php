<?php
/**
 * Template name: Promote yourself page
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['form'] = isset($_REQUEST['registration']) ? $_POST['registration'] : [];
Timber::render( array( 'template-promote-yourself.twig', 'page.twig' ), $context );