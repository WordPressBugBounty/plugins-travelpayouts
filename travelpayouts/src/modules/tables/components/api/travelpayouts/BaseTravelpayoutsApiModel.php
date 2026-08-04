<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts;

use Travelpayouts\modules\tables\components\api\BaseTokenApiModel;

abstract class BaseTravelpayoutsApiModel extends BaseTokenApiModel
{
    protected function request()
    {
        return $this->fetchApi();
    }

    /**
     * @inheritdoc
     */
    public function afterRequest()
    {
        $response = $this->response;
        if (is_array($response)) {
            if (isset($response['success']) && $response['success']) {
                $this->response = $response['data'];
            } else {
                $this->fetchErrors();
                $this->response = null;
            }
        } else {
            // Response did not parse into an array: HTML instead of JSON, a 500
            // page, broken JSON. Without this branch it looks like an empty list.
            $this->addError('response', \Travelpayouts::__('API returned an unexpected response'));
            $this->response = null;
        }
    }

    /**
     * No branch for an `errors` map on purpose: neither v1 nor v2 returns one,
     * a validation error arrives as a string in `error`.
     * A broken token is not even JSON (bare `Unauthorized`), so it ends up in the
     * "response is not an array" branch of `afterRequest()`.
     */
    protected function fetchErrors()
    {
        $response = $this->response;
        if (isset($response['error'])) {
            // Key `response`, not `token`: the API puts any of its errors here,
            // most often about request params. `token` stays with client-side
            // validation in `BaseTokenApiModel::validateApiToken()`.
            $this->addError('response', $response['error']);
            if (TRAVELPAYOUTS_DEBUG) {
                echo $response['error'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function getRequestQueryString(): string
    {
        return '?' . http_build_query($this->normalizeQueryParams($this->toArray()));
    }

    /**
     * @param $params
     * @return array
     */
    protected function normalizeQueryParams($params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $result[$key] = $value ? 'true' : 'false';
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

}
