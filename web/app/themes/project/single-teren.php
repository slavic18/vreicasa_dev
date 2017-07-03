<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post(false, 'CustomTimberPost');
$context['post'] = $post;
$context['relatedPosts'] = Timber::get_posts([
    'post_type' => 'teren',
    'posts_per_page' => 3,
    'posts__not_in' => [$context['post']->id],
    'orderby' => 'rand'
], 'CustomTimberPost');


Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
