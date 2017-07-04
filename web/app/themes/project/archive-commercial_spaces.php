<?php
/**
 * The template for displaying apartments archive pages.
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */
$templates = ['archive-commercial.twig'];
$context = Timber::get_context();

global $paged;
if (!isset($paged) || !$paged) {
    $paged = 1;
}
// process form
if (isset($_REQUEST['form']) && $_REQUEST['form']) {
    $form = array_merge($_REQUEST['form'], ['paged' => $paged]);
    $build = new BuildSearchQuery($form);
    $build->buildQuery();
}

$context['posts'] = Timber::get_posts(false, 'CustomTimberPost');
$context['pagination'] = Timber::get_pagination(['show_all' => false, 'mid_size' => 4, 'end_size' => 1,]);
$context['actualLink'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
Timber::render($templates, $context);
