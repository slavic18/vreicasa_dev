<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$templates = array('archive.twig', 'index.twig');

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination(['show_all' => false, 'mid_size' => 4, 'end_size' => 1,]);

$context['popularPosts'] = Timber::get_posts([
    'post_type' => 'post',
    'posts_per_page' => 6,
    'orderby' => 'rand'
]);
Timber::render($templates, $context);
