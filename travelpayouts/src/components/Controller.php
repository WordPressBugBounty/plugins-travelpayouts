<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use Exception;
use Travelpayouts;
use Travelpayouts\admin\AdminHooks;
use Travelpayouts\traits\SingletonTrait;

abstract class Controller extends BaseInjectedObject
{
    use SingletonTrait;

    private $_queryParams;

    protected function requireCapability(string $capability): void
    {
        if (!current_user_can($capability)) {
            $this->refuse();
        }
    }

    /**
     * The body is read from php://input whatever its content type, so a cross-site text/plain form can submit it.
     */
    protected function requireNonce(): void
    {
        if (!check_ajax_referer(AdminHooks::AJAX_NONCE_ACTION, false, false)) {
            $this->refuse();
        }
    }

    /**
     * Ends the request: `wp_send_json()` dies after printing.
     */
    private function refuse(): void
    {
        // Set apart from the envelope: `wp_send_json()` skips the status once anything is printed.
        status_header(403);
        $this->response(false, ['message' => Travelpayouts::__('Insufficient access rights!')]);
    }

    protected function getInputData()
    {
        try {
            $jsonData = file_get_contents('php://input');
            return json_decode($jsonData, true);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getInputParam($name, $defaultValue = null)
    {
        $inputData = $this->getInputData();
        if ($inputData) {
            if ($name) {
                return isset($inputData[$name])
                    ? $inputData[$name]
                    : $defaultValue;
            }
            return $inputData;
        }
        return $defaultValue;
    }

    /***
     * @param bool $success
     * @param array $content
     * @param array $meta
     * @param bool $return
     * @return false|string|void
     */
    public function response($success, $content = [], $meta = [], $return = false)
    {
        $output = [
            'success' => $success,
            'data' => $content,
            'meta' => $meta,
        ];
        if ($return) {
            return wp_json_encode($output);
        }

        wp_send_json($output);
    }

    /**
     * Returns the request parameters given in the [[queryString]].
     * This method will return the contents of `$_GET` if params where not explicitly set.
     * @return array the request GET parameter values.
     * @see setQueryParams()
     */
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            return $_GET;
        }

        return $this->_queryParams;
    }

    /**
     * Sets the request [[queryString]] parameters.
     * @param array $values the request query parameters (name-value pairs)
     * @see getQueryParam()
     * @see getQueryParams()
     */
    public function setQueryParams($values)
    {
        $this->_queryParams = $values;
    }

    /**
     * Returns GET parameter with a given name. If name isn't specified, returns an array of all GET parameters.
     * @param string $name the parameter name
     * @param mixed $defaultValue the default parameter value if the parameter does not exist.
     * @return array|mixed
     */
    public function get($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        }

        return $this->getQueryParam($name, $defaultValue);
    }

    /**
     * Returns the named GET parameter value.
     * If the GET parameter does not exist, the second parameter passed to this method will be returned.
     * @param string $name the GET parameter name.
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @see getBodyParam()
     */
    public function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

}
