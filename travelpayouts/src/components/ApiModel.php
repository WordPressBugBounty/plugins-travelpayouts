<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;
use Travelpayouts\Vendor\glook\jsonmapper\JsonMapperException;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts;
use Travelpayouts\components\api\ResponseMapper;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\httpClient\Client;
use Travelpayouts\components\notices\Notice;
use Travelpayouts\components\notices\Notices;
use Travelpayouts\helpers\ArrayHelper;

/**
 * @property-read array $api_data
 * @property array|null $response
 * @property-read array $debugData
 */
abstract class ApiModel extends InjectedModel
{
    /**
     * @Inject
     * @var Notices
     */
    protected $notices;

    /**
     * @Inject
     * @var Travelpayouts\modules\settings\SettingsForm
     */
    protected $settingsSection;

    /**
     * @see getHttpClient()
     * @var array
     */
    protected $clientOptions = [
        'timeout' => 15,
        'headers' => [
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => '*',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
        ],
    ];
    /**
     * @var array|null
     */
    protected $_response;
    /**
     * @var string[]
     */
    private $_requestList = [];

    protected $cacheTime = 60 * 5;

    /**
     * Client injected from outside, filled in tests only.
     * @var Client|null
     */
    protected $_httpClient;

    /**
     * @param Client $client
     */
    public function setHttpClient($client): void
    {
        $this->_httpClient = $client;
    }

    /**
     * Never memoize: `fetchRemoteContent()` mixes a per-URL `Host` header into
     * `clientOptions`, so a cached client would keep the first request's host.
     * `final` keeps subclasses from bypassing the client injected by the setter.
     * @return Client
     */
    final protected function getHttpClient()
    {
        return $this->_httpClient ?: new CachedClient($this->clientOptions, $this->cacheTime);
    }

    /**
     * @return string
     */
    protected function getRequestQueryString()
    {
        return '?' . http_build_query($this->toArray());
    }

    /**
     * @return string|null
     * @see getRequestQueryString()
     */
    public function getRequestUrl()
    {
        return $this->endpointUrl()
            ? $this->endpointUrl() . $this->getRequestQueryString()
            : null;
    }

    /**
     * @param array|null $data
     */
    protected function setResponse($data)
    {
        $this->_response = $data;
    }

    /**
     * @return array|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return array|mixed
     */
    abstract protected function request();

    /**
     * @return array|bool
     */
    final public function sendRequest()
    {
        $this->response = null;
        try {
            if ($this->validate()) {
                $this->response = $this->request();
                $this->afterRequest();
                $this->notifyErrors();
                return $this->response;
            }

            $this->notifyErrors();
        } catch (\Throwable $e) {
            // `Throwable`, not `Exception`: a broken date format in the response
            // raises `TypeError`, which extends `Error`.
            Travelpayouts::getInstance()->logger->error($e->getMessage(), [
                $this->attributes,
            ]);
            // The plugin logger is a stub, so a failure must also reach the error
            // bag: otherwise "API is down" looks exactly like "list is empty".
            $this->addError('request', $e->getMessage());
            $this->notifyErrors();
        }
        return [];
    }

    /**
     * Hook for enriching data once `$this->response` is assigned.
     * @return void
     */
    protected function afterRequest()
    {
    }

    /**
     * @return array|bool
     */
    protected function fetchApi()
    {
        return $this->fetchRemoteContent($this->getRequestUrl());
    }

    /**
     * @param string $url
     * @return bool|false|mixed|null
     * @see getRequestUrl()
     */
    private function fetchRemoteContent(string $url)
    {
        $this->addRequestUrl($url);
        $this->clientOptions = ArrayHelper::mergeRecursive($this->clientOptions, [
            'headers' => [
                'Host' => parse_url($url, PHP_URL_HOST),
            ],
        ]);

        $response = $this->getHttpClient()->get($url);

        // A timeout, the most common failure, throws nothing: `WP_Error` settles
        // in `Response` and `->json` returns null, which looks like an empty list.
        if ($response->isError ?? false) {
            $this->addError('request', Travelpayouts::__('API request failed'));
        }

        return $response->json;
    }

    /**
     * @param string|array $urlList
     */
    protected function addRequestUrl($urlList)
    {
        $value = !is_array($urlList)
            ? [$urlList]
            : $urlList;
        $this->_requestList = array_merge($this->_requestList, $value);
    }

    public function getDebugData()
    {
        return $this->_requestList;
    }

    /**
     * @return string
     */
    abstract protected function endpointUrl();
    /**
     * Surfaces collected errors as admin notices.
     */
    protected function notifyErrors()
    {
        if (!$this->settingsSection->getIsTableNoticesDisabled()) {
            foreach ($this->getErrors() as $key => $error) {
                $noticeName = implode('-', [
                    TRAVELPAYOUTS_PLUGIN_NAME,
                    'validationNotice',
                    $key,
                ]);

                $this->notices->add(
                    Notice::create($noticeName)
                        ->setType(Notice::NOTICE_TYPE_ERROR)
                        ->setTitle(Travelpayouts::__('Validation failed'))
                        ->setDescription(implode(' ', $error))
                );
            }
        }
    }

    /**
     * Parsed response, one value per target class.
     * @var array<class-string, mixed>
     */
    protected $parsed = [];

    /**
     * Parses the response into a list of objects. The request runs on demand and
     * is reused, so the call order relative to `sendRequest()` does not matter.
     *
     * A parse error is deliberately not caught: a missing `@required` field must
     * reach the render boundary instead of becoming an empty table row.
     *
     * @template T
     * @param class-string<T> $class
     * @return T[]
     * @throws JsonMapperException
     */
    public function getModels(string $class): array
    {
        if (!isset($this->parsed[$class])) {
            $this->parsed[$class] = ResponseMapper::mapList($this->getResponse() ?? $this->sendRequest(), $class);
        }

        return $this->parsed[$class];
    }

    /**
     * Same for a response parsed into a single object rather than a list. Here a
     * parse error is swallowed: empty object plus an entry in the error bag, so
     * the table renders empty and the site owner gets a notice.
     *
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public function getModel(string $class)
    {
        if (!isset($this->parsed[$class])) {
            try {
                $this->parsed[$class] = ResponseMapper::map($this->getResponse() ?? $this->sendRequest(), $class);
            } catch (\Throwable $e) {
                $this->addError('response', $e->getMessage());
                $this->notifyErrors();
                $this->parsed[$class] = new $class();
            }
        }

        return $this->parsed[$class];
    }
}
