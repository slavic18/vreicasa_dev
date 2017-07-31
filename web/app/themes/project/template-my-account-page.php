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
$context['accountMenu'] = getMyAccountMenu();

if (isset($_COOKIE['favoritePosts'])) {
    $context['posts'] = Timber::get_posts([
        'post__in' => explode(',', $_COOKIE['favoritePosts']),
        'post_type' => ['projects', 'apartments', 'teren', 'commercial_spaces', ''],
        'posts_per_page' => -1
    ], 'CustomTimberPost');
}else {
    $context['posts'] = [];
}
Timber::render(array('page-my-account.twig', 'page.twig'), $context);