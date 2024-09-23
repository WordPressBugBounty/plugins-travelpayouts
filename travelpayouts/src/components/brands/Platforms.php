<?php

namespace Travelpayouts\components\brands;

use Travelpayouts;
use Travelpayouts\components\api\ApiEndpoint;
use Travelpayouts\components\httpClient\CachedResponse;

/**
 * Class Platforms
 * @package Travelpayouts\components
 *
 */
class Platforms extends ApiEndpoint
{
    public function getData(): ?PlatformResponse
    {
        $response = $this->getResponse();
        return $response && $response->getIsSuccess() ? PlatformResponse::createFromArray($response->getJSON()) : null;
    }

    /**
     * @return array
     */
    public function getSelectOptions(): array
    {
        $data = $this->getData();
        $options = [
            Travelpayouts::__("Your account doesn't have traffic sources yet"),
        ];
        if ($data && !empty($data->sources)) {
            $options = [
                Travelpayouts::__('Please select a source for your website'),
            ];
            foreach ($data->sources as $source) {
                $options[$source->id] = $source->name . ' #' . $source->id;
            }

        }
        return $options;
    }

    public function getResponse(): ?CachedResponse
    {
        return $this->getClient()->get('https://api.travelpayouts.com/users/v1/get_traffic_sources');
    }

    protected function clientOptions(): array
    {
        return [
            'timeout' => 15,
            'headers' => [
                'X-Access-Token' => Travelpayouts::getInstance()->account->getToken(),
                'Wp-Domain' => get_site_url()
            ],
        ];
    }

    /**
     * @return bool
     */
    public function isActivePlatformSelected(): bool
    {
        if ($data = $this->getData()) {
            return $data->getCurrentSource() !== null;
        }
        return false;
    }

    /**
     * Получаем список доступных программ для выбранной площадки
     * @return int[]
     */
    public function getActivePrograms(): array
    {
        $data = $this->getData();
        return $data ? $data->getActivePrograms() : [];
    }

    /**
     * @return bool
     */
    public function isActiveRequiredPrograms(): bool
    {
        $selectedPlatform = $this->getSelectedPlatform();
        if (empty($selectedPlatform)) {
            return true;
        }

        $requiredPlatforms = [Subscriptions::AVIASALES_ID, Subscriptions::HOTELLOOK_ID];

        return count(array_intersect($requiredPlatforms, $this->getActivePrograms())) == count($requiredPlatforms);
    }

    /**
     * @param $programId
     * @return bool
     */
    public function isActive($programId): bool
    {
        return in_array((int)$programId, $this->getActivePrograms(), true);
    }

    /**
     * Если от апи пришли площадки для выбора и
     * выбрана площадка или установлена кука "hide"
     * мы не показываем notice
     *
     * @return bool
     */
    public function showSelectPlatformNotice(): bool
    {
        if ($data = $this->getData()) {
            return !empty($data->sources) && !$this->isActivePlatformSelected();
        }
        return false;
    }

    /**
     * Получаем ссылку на скрипт
     * @return string|null
     */
    public function getScriptLink(): ?string
    {
        if ($data = $this->getData()) {
            return $data->script_link;
        }

        return null;
    }

    /**
     * Получаем выбранную площадку
     * @return string|null
     */
    protected function getSelectedPlatform(): ?string
    {
        return Travelpayouts::getInstance()->account->section->platform;
    }
}
