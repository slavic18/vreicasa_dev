<?php
/**
 * The template for displaying home page.
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

// form fields
$form = [];
$form['address'] = Timber::get_terms('cities');

$context['form'] = $form;


Timber::render(array('page-home.twig', 'page.twig'), $context);