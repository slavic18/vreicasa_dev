<?php

/**
 * Класс для определения всех ajax-функции и вызовов fruitframe.
 */
class Ajax_Actions
{
    protected $_controllerPrefix;

    protected function _addAction($hook, $functionName)
    {
        add_action('wp_ajax_' . $hook, array($this, trim($functionName)));
        add_action('wp_ajax_nopriv_' . $hook, array($this, trim($functionName)));
    }

    public static function init()
    {
        static $object;
        if (!is_object($object)) {
            $object = new self();
        }
        return $object;
    }


    /**
     * Специальный метод, который позволяет вызывать любой контроллер с помощью Ajax
     * Для вызова контроллера достаточно передать два параметра:
     * 1) $_REQUEST['controller'] - его название в формате Greenevolution_<название>_Controller
     * 2) $_REQUEST['controller_action'] - название action-метода контроллера
     * 3) $_REQUEST['use_json'] - перехватывать вывод в буфер и передавать ли его в переменную HTML
     */
    public function loadController()
    {
        if (empty($_REQUEST['controller']) || empty($_REQUEST['controller_action'])) {
            $this->_responseError('Не указан контроллер или действие 1');
        }
        if (!class_exists($controller = $this->_getControllerPrefix() . '_' . $_REQUEST['controller'] . '_Controller')) {
            $this->_responseError('Указан несуществующий контроллер');
        }

        if (empty($_REQUEST['use_json'])) {
            /**
             * Перехват вывода буфера для того, чтобы все отображаемые данные попали в одну переменную ответа HTML.
             * Кроме того можно отслеживать ошибки — если вывод пуст, то действие не сработало
             */
            ob_start();
            $controller::init()->route($_REQUEST['controller_action'], TRUE);
            $html = ob_get_clean();

            if (empty($html)) {
                $this->_responseError('Пустой ответ');
            }
            $this->_sendResponse(array('html' => $html));
        } else {
            $controller::init()->route($_REQUEST['controller_action'], TRUE);
            exit;
        }
    }

    /**
     * Отправка сообщения об ошибке
     * @uses Ajax_Actions::_sendResponse()
     * @param string $message
     */
    protected function _responseError($message = 'Неизвестная ошибка')
    {
        $this->_sendResponse(array($message));
    }

    /**
     * Отправка кодированного в формат JSON массива данных в вывод браузера
     * @param array $data
     */
    protected function _sendResponse(array $data = array())
    {
        die(json_encode($data));
    }

    public function initAllActions()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (strpos($method, 'Hook')) {
                $hook = str_replace('Hook', '', $method);
                $this->_addAction('_' . $hook, $method);
            }
        }
    }

    /**
     * demo method
     */
//    public function _getAllCitiesHook()
//    {
//        try {
//            $data = [
//                'success' => true,
//                'html' => $html,
//                'offset' => $sanitizedForm['offset'] + count($posts),
//                'lastPosts' => $lastPosts
//            ];
//
//        } catch (Exception $e) {
//            $data = [
//                'success' => false,
//                'msg' => __($e->getMessage(), 'project')
//            ];
//        }
//        wp_send_json($data);
//        exit();
//    }

    public function getAllCitiesHook()
    {
        try {
            $citiesArr = [];
            $citiesTerms = get_terms('cities', [
                'hide_empty' => false
            ]);

            foreach ($citiesTerms as $city) {
                $citiesArr[$city->term_id] = $city->name;
            }

            $data = [
                'success' => true,
                'cities' => $citiesArr
            ];
        } catch (Exception $e) {
            $data = [
                'success' => false,
                'msg' => __($e->getMessage(), 'project')
            ];
        }
        wp_send_json($data);
        exit();
    }

    public function toggleFavoriteHook()
    {

        try {
            $postId = (int)$_POST['data']['post_id'];

            if (!$postId) {
                throw new Exception("Post id is required");
            }


            if (isset($_COOKIE["favoritePosts"])) {

                $favoritePosts = $_COOKIE["favoritePosts"];
                $favoritePostsArr = explode(',', $favoritePosts);

                if (in_array($postId, $favoritePostsArr)) {
                    unset($favoritePostsArr[array_search($postId, $favoritePostsArr)]);
                } else {
                    $favoritePostsArr[] = $postId;
                }

                $favoritePosts = implode(',', $favoritePostsArr);
            } else {
                $favoritePosts = $postId;
            }
            setcookie('favoritePosts', $favoritePosts, time() + 3600 * 24 * 30, '/');
            $data = [
                'success' => true
            ];

        } catch (Exception $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        wp_send_json($data);
        exit();
    }


}