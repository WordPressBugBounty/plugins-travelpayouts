<?php

namespace Travelpayouts\modules\moneyScript;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\account\AccountForm;
use Travelpayouts\modules\moneyScript\components\SubscribedCampaign;

class MoneyScriptSection extends ModuleSection
{
    /**
     * @var string
     */
    protected $active;
    /**
     * @var array<string,string>
     */
    protected $exclude = [];
    /**
     * @Inject
     * @var AccountForm
     */
    protected $accountSection;

    /**
     * @var array
     */
    protected $_excludedCampaignIds;

    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Money Script'),
            'icon' => 'el el-usd',
            'desc' => $this->getSectionDescription(),
        ];
    }

    public function fields(): array
    {
        $options = $this->getOptions();

        return [
            'active' => $this->fieldSwitcher()
                ->setTitle(Travelpayouts::__('Enable Money Script'))
                ->setDefault(false),
            count($options) ? [
                'id' => 'exclude',
                'type' => 'checkbox',
                'multi' => true,
                'title' => Travelpayouts::__('Exclude programs'),
                'options' => $options,
                'placeholder' => '',
                'select2' => [
                    'theme' => 'travelpayouts',
                    'allowClear' => false,
                    'minimumResultsForSearch' => 10,
                ],
            ] : null,
        ];
    }

    public function optionPath(): string
    {
        return 'money_script';
    }

    protected function getOptions(): array
    {
        $marker = $this->accountSection->api_marker;
        if ($marker) {

            $client = new CachedClient([], 60 * 60 * 3);
            $response = $client->get("https://app.travelpayouts.com/money_script_api/get_subscribed_campaings?marker=$marker");
            if (!$response->isError && $data = $response->json) {
                return $this->mapSubscribedCampaigns($data);
            }
        }
        return [];
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return StringHelper::toBoolean($this->active);
    }

    /**
     * Получаем id кампаний для исключения
     * @return string[]
     */
    public function getExcludedCampaignIds(): array
    {
        if (!$this->_excludedCampaignIds && $this->exclude && is_array($this->exclude)) {
            $activeCompanyList = array_filter($this->exclude, static function ($isActive) {
                if (is_string($isActive)) {
                    return $isActive === '1';
                }
                return false;
            });

            $this->_excludedCampaignIds = array_keys($activeCompanyList);
        }

        return $this->_excludedCampaignIds;
    }

    /**
     * @param $data
     * @return array|string[]
     */
    protected function mapSubscribedCampaigns($data): array
    {
        $campaigns = [];
        foreach ($data as $item) {
            $model = new SubscribedCampaign($item);
            if (isset($campaigns[$model->campaign_id])) {
                $campaigns[$model->campaign_id] = array_merge($campaigns[$model->campaign_id], $model->campaign_domains);
            } else {
                $campaigns[$model->campaign_id] = $model->campaign_domains;
            }
        }

        $result = [];
        foreach ($campaigns as $campaignId => $domainsList) {
            // Убираем неуникальные домены
            $domainsList = array_unique($domainsList);
            $campaignIdTitle = Travelpayouts::_x('Campaign id: {id}', 'moneyscript.excludedCampaigns', ['id' => $campaignId]) . '. ';
            $domainsListTitle = Travelpayouts::_x('Excluded domains', 'moneyscript.excludedCampaigns') . ': ' . implode(', ', $domainsList);
            $result[$campaignId] = $campaignIdTitle . $domainsListTitle;

        }
        return ksort($result) ? $result : [];
    }

    /**
     * @return string
     */
    protected function getSectionDescription(): string
    {
        $url = LanguageHelper::isRuDashboard() ?
            'https://support.travelpayouts.com/hc/ru/articles/360012913480' :
            'https://support.travelpayouts.com/hc/en-us/articles/360012913480-Automatic-replacement-of-links-on-the-website';

        $anchorLink = HtmlHelper::tag('a',
            [
                'class' => 'tp-link',
                'href' => $url,
                'target' => '_blank',
            ],
            Travelpayouts::_x('Knowledge Base', 'moneyscript knowledge url title')
        );

        return ReduxOptions::alert([
            HtmlHelper::tag('div', [], Travelpayouts::_x('With Money Script you can quickly replace links to travel resources such as Booking.com, Kiwitaxi and others with Travelpayouts affiliate links.',
                'moneyscript description text')),
            HtmlHelper::tag('div', ['class' => 'tp-mt-2'], Travelpayouts::_x('Find out more in our {link}.', 'moneyscript description text', [
                'link' => $anchorLink,
            ])),
        ], ['class' => 'tp-alert--info']);
    }

}
