<?php
if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    });
    return;
} else {
    Timber::$cache = false;
}
include_once 'assets/libs/custom_timber_post.php';
include_once 'assets/libs/custom_order_fields.php';
include_once 'assets/libs/additional_tables.php';
include_once 'assets/libs/process_form.php';
include_once 'assets/libs/build_search_query.php';
include_once 'assets/libs/ajax_actions.php';
function my_acf_init()
{
    acf_update_setting('google_api_key', 'AIzaSyBROsqLrIBnocNauBUwS0jF_0Nl3e9-3XA');
}

add_action('acf/init', 'my_acf_init');

if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Theme General Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));
}

function fruitframe_get_permalink($system, $isCat = FALSE, $siteId = NULL)
{
    if (!is_null($siteId)) {
        switch_to_blog($siteId);
    }
    if ($isCat) {
        if (!is_numeric($system)) {
            $system = get_cat_ID($system);
        }
        $link = get_category_link($system);
    } else {
        $page = fruitframe_get_page($system);
        $link = null === $page ? '' : get_permalink($page);
    }
    if (!is_null($siteId)) {
        restore_current_blog();
    }
    return $link;
}


/**
 * Get page by name, id or slug.
 * @global object $wpdb
 * @param mixed $name
 * @return object
 */
function fruitframe_get_page($slug)
{
    static $pages;
    if (empty($pages[$slug])) {
        $pages[$slug] = (is_numeric($slug) ?
            get_page($slug)
            :
            $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare("SELECT DISTINCT * FROM {$GLOBALS['wpdb']->posts} WHERE post_name=%s AND post_status=%s", $slug, 'publish')));
    }
    return $pages[$slug];
}

function deploy($var, $die = true)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    if ($die) {
        die;
    }
}

function transient_slug($slug, $locale = true)
{
    return $locale ? $slug . '_' . get_locale() : $slug;
}

Timber::$dirname = array('templates', 'views');

// custom twig functions
class Twig_Extension_CustomFunctions extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('customTimberPost', [$this, 'customTimberPost'], array('needs_environment' => false)),
            new Twig_SimpleFunction('getPermalink', [$this, 'getPermalink'], array('needs_environment' => false)),
            new Twig_SimpleFunction('getFormNumberValue', [$this, 'getFormNumberValue'], array('needs_environment' => false)),
            new Twig_SimpleFunction('getFormRadioValue', [$this, 'getFormRadioValue'], array('needs_environment' => false)),
            new Twig_SimpleFunction('getFormCheckboxValue', [$this, 'getFormCheckboxValue'], array('needs_environment' => false)),
        );
    }

    public function getPermalink($slug)
    {
        return fruitframe_get_permalink($slug);
    }

    public function customTimberPost($id)
    {
        return new CustomTimberPost($id);
    }

    public function getFormNumberValue($name)
    {
        $name = str_replace(']', '', $name);
        $name = explode('[', $name);
        if (isset($_REQUEST['form'][$name[1]]) && !empty($_REQUEST['form'][$name[1]])) {
            return $_REQUEST['form'][$name[1]];
        }
    }

    public function getFormRadioValue($name, $value)
    {
        $name = str_replace(']', '', $name);
        $name = explode('[', $name);
        if (isset($_REQUEST['form'][$name[1]]) && !empty($_REQUEST['form'][$name[1]]) && $_REQUEST['form'][$name[1]] == $value) {
            return 'checked';
        }
    }

    public function getFormCheckboxValue($name)
    {
        $name = str_replace(']', '', $name);
        $name = explode('[', $name);
        if (count($name) == 2) {
            if (isset($_REQUEST['form'][$name[1]]) && !empty($_REQUEST['form'][$name[1]])) {
                return 'checked';
            }
        }
        if (count($name) == 3) {
            if (isset($_REQUEST['form'][$name[1]][$name[2]]) && !empty($_REQUEST['form'][$name[1]][$name[2]])) {
                return 'checked';
            }
        }
    }
}


class StarterSite extends TimberSite
{

    function __construct()
    {
        add_theme_support('post-formats');
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_filter('timber_context', array($this, 'add_to_context'));
        add_filter('get_twig', array($this, 'add_to_twig'));
        add_action('init', array($this, 'register_menus'));
        add_action('init', array($this, 'register_post_types'), 1);
        add_action('init', array($this, 'register_taxonomies'), 1);
        add_action('init', array($this, 'createCustomPages'));
        add_action('widgets_init', array($this, 'register_sidebars'), 99);
        add_action('wp_enqueue_scripts', array($this, 'applyScriptsFrontend'), 99);
        load_theme_textdomain('project', get_template_directory() . '/assets/languages');
        Ajax_Actions::init()->initAllActions();

        parent::__construct();
    }

    public function applyScriptsFrontend()
    {

        $version = 1. . rand(0, 1000);
        $templateUri = get_template_directory_uri();
        $assetsUri = $templateUri . '/assets';

        //fonts
        wp_enqueue_style('fa-font', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), $version);
        wp_enqueue_style('montserat', '//fonts.googleapis.com/css?family=Montserrat:300,400,500,700&amp;subset=latin-ext', array(), $version);

        //styles
        wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css', array(), $version);
        wp_enqueue_style('bootstrap', $assetsUri . '/css/libs/bootstrap.min.css', array(), $version);
        wp_enqueue_style('bootstrap-theme', $assetsUri . '/css/libs/bootstrap-theme.min.css', array(), $version);
        wp_enqueue_style('owl-carousel', $assetsUri . '/css/libs/owl.carousel.min.css', array(), $version);
        wp_enqueue_style('owl-carousel-theme', $assetsUri . '/css/libs/owl.theme.default.css', array(), $version);
        wp_enqueue_style('chosen', $assetsUri . '/css/libs/chosen.css', array(), $version);
        wp_enqueue_style('fonts', $assetsUri . '/css/fonts.css', array(), $version);
        wp_enqueue_style('app', $assetsUri . '/css/app.css', array(), $version);
        wp_enqueue_style('developer', $assetsUri . '/css/developer.css', array(), $version);

        //scripts
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array(), $version, true);
        wp_enqueue_script('validate', '//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js', array('jquery'), $version, true);
        wp_enqueue_script('maps', '//maps.googleapis.com/maps/api/js?key=AIzaSyBROsqLrIBnocNauBUwS0jF_0Nl3e9-3XA', array('jquery'), $version, true);

        wp_enqueue_script('imagesloaded', $assetsUri . '/js/libs/imagesloaded.js', array('jquery'), $version, true);
        wp_enqueue_script('jqueryUi', '//code.jquery.com/ui/1.12.0/jquery-ui.min.js', array('jquery'), $version, true);
        wp_enqueue_script('matchHeight', $assetsUri . '/js/libs/jquery.matchHeight.js', array('jquery'), $version, true);
//		wp_enqueue_script('bootstrap', $assetsUri . '/js/libs/bootstrap.min.js', array('jquery'), $version, true);
        wp_enqueue_script('owl', $assetsUri . '/js/libs/owl.carousel.min.js', array('jquery'), $version, true);
        wp_enqueue_script('chosen', $assetsUri . '/js/libs/chosen.jquery.min.js', array('jquery'), $version, true);
        wp_enqueue_script('main', $assetsUri . '/js/scripts.min.js', array('jquery'), $version, true);
        wp_enqueue_script('development', $assetsUri . '/js/development.js', array('jquery'), $version, true);


        // app scripts
        wp_localize_script('main', 'fruitframe', array(
            'template_dir' => $assetsUri,
            'ajax_load_url' => site_url('/wp-admin/admin-ajax.php'),
            'site_url' => site_url(),
            'translations' => [
                'from' => __('from', 'amma'),
                'to' => __('to', 'amma'),
                'subscribe' => __('subscribe', 'amma'),
            ]
        ));
    }


    public function createCustomPages()
    {
        $pages = [
            'home',
            'blog',
            'contacts',
            'interior-design',
            'arhitecture',
            'cadastru',
            'why-new-homes',
            'about-us',
            'fashionable',
            'promote-yourself',
            'services',
            'projects',
            'restore-password',
            'termeni-si-conditii',
            'register',
            'login',
            'my-account',
        ];

        if (!empty($pages) && is_array($pages) && count($pages)) {
            foreach ($pages as $page) {
                $page = is_array($page) ? $page : array(
                    'slug' => $page,
                    'title' => $page,
                    'text' => $page,
                );
                $this->_addPage($page['slug'], $page['title'], $page['text']);
            }
        }
    }

    protected function _addPage($slug, $title, $text)
    {
        global $wpdb;
        $page = $wpdb->get_row("SELECT DISTINCT * FROM $wpdb->posts WHERE post_name = '$slug'");
        if (!is_object($page)) {
            $result = wp_insert_post(array(
                'post_name' => $slug,
                'post_title' => $title,
                'post_type' => 'page',
                'post_content' => $text,
                'post_status' => 'publish',
                'post_author' => 1,
            ));
        }
    }


    function register_menus()
    {
        register_nav_menus(array(
            'header_top_menu' => 'Меню в шапке',
            'footer_menu' => 'Меню в подвале (о компании)',
            'footer_second_menu' => 'Меню в подвале (Как мы работаем)',
        ));
    }

    function register_sidebars()
    {
        register_sidebar(['name' => 'Woocommerce search sidebar', 'id' => 'dynamic_sidebar']);

    }

    function register_post_types()
    {

    }


    function register_taxonomies()
    {

    }

    function add_to_context($context)
    {
        $context['header_menu'] = new TimberMenu('header_top_menu');
        $context['footer_menu'] = new TimberMenu('footer_menu');
        $context['footer_second_menu'] = new TimberMenu('footer_second_menu');

        $context['options'] = get_fields('option');

        $languages = [];
//		$current_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//		foreach (qtrans_getSortedLanguages() as $language):
//			$languages[] = [
//				'link' => esc_url_raw(qtranxf_convertURL($current_url, $language, false, true)),
//				'name' => $language,
//				'selected' => $language == qtranxf_getLanguage()
//			];
//		endforeach;

        $context['languages'] = $languages;
        $context['site'] = $this;

        return $context;
    }

    function add_to_twig($twig)
    {
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension(new Twig_Extension_StringLoader());
        $twig->addExtension(new Twig_Extension_CustomFunctions());
        $twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));

        return $twig;
    }

}

new StarterSite();

// translate text
function _t($text)
{
    return qtranxf_useCurrentLanguageIfNotFoundShowEmpty($text);
}

function getMyAccountMenu()
{
    return [
        [
            'key' => 'favorite',
            'title' => 'Favorite',
            'link' => fruitframe_get_permalink('my-account'),
        ],
        [
            'key' => 'recent',
            'title' => 'Cautari recente',
            'link' => fruitframe_get_permalink('recent-searches'),
        ],
        [
            'key' => 'messages',
            'title' => 'Mesaje',
            'link' => fruitframe_get_permalink('messages'),
        ],
        [
            'key' => 'configure',
            'title' => 'Setari',
            'link' => fruitframe_get_permalink('configure'),

        ],
    ];
}
//show_admin_bar(false);