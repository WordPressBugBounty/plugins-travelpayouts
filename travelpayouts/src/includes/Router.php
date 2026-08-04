<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\includes;

use Exception;
use Travelpayouts\Vendor\FastRoute\Dispatcher;
use Travelpayouts\Vendor\FastRoute\RouteCollector;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\web\Request;

use function Travelpayouts\Vendor\FastRoute\simpleDispatcher;

class Router extends BaseObject
{
    public $actionParam = 's';
    public $routeParam = 'page';

    protected $_routes = [];
    /**
     * @var callable[]
     */
    protected $_routeGroups = [];

    public function init()
    {
        if ($this->actionParam) {
            /** @see Router::run() */
            add_action($this->actionParam, [
                $this,
                'run',
            ]);
        } else {
            throw new Exception('property $actionParam must be set');
        }
    }

    public function addRoute($httpMethod, $route, $callback)
    {
        $httpMethod = is_string($httpMethod) ? strtoupper($httpMethod) : $httpMethod;
        $validationList = [
            is_string($httpMethod) && in_array($httpMethod, [
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE',
            ]),
            is_string($route),
            is_callable($callback),
        ];

        if (!in_array(false, $validationList, true)) {
            $this->_routes[] = [
                'httpMethod' => $httpMethod,
                'route' => $route,
                'callback' => $callback,
            ];
        }
    }

    public function addRoutes($routeList)
    {
        if (is_array($routeList)) {
            foreach ($routeList as $route) {
                $this->addRoute(...$route);
            }
        }
    }

    public function addGroup($prefix, callable $callback)
    {
        $this->_routeGroups = array_merge($this->_routeGroups, [$prefix => $callback]);
    }

    public function run()
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->_routes as $route) {
                $r->addRoute($route['httpMethod'], $route['route'], $route['callback']);
            }

            foreach ($this->_routeGroups as $prefix => $callback) {
                $r->addGroup($prefix, $callback);
            }

        });
        if (!isset($_GET[$this->routeParam])) {
            $this->refuse(404);
            return;
        }

        $routeInfo = $dispatcher->dispatch(Request::getInstance()->getMethod(), $_GET[$this->routeParam]);
        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $handler(...array_values($routeInfo[2]));
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // The path exists under other verbs, and saying so turns "my PUT answers not found"
                // into something the caller can act on. FastRoute hands the list over for free.
                $this->refuse(405, ['allowed_methods' => $routeInfo[1]]);
                break;
            default:
                $this->refuse(404);
                break;
        }
    }

    /**
     * An unmatched route used to fall through the switch, which left `admin-ajax.php` to finish the
     * request with 200 and an empty body - unparseable, and indistinguishable from a broken handler.
     * The status is real, not 200, so that a client calling a route that no longer exists finds out.
     *
     * @param int $statusCode
     * @param array $data
     * @return void
     */
    protected function refuse($statusCode, $data = [])
    {
        // Set apart from `wp_send_json()`, which skips the status once anything has been printed.
        status_header($statusCode);

        wp_send_json([
            'success' => false,
            'data' => $data,
            'meta' => [],
        ]);
    }
}
