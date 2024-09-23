<?php

namespace Travelpayouts\modules\tables\components\railway;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\brands\BrandsPartnerPermissions;
use Travelpayouts\components\dictionary\Campaigns;
use Travelpayouts\components\brands\Subscriptions;

class Section extends ModuleSection
{
    public function __construct(Travelpayouts\modules\tables\components\Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        $campaign = Campaigns::getInstance()->getItem('45');

        return [
            'title' => $campaign ? $campaign->name : Travelpayouts::__('Railway'),
            'icon' => 'el el-road',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'railway';
    }

    /**
     * @inheritDoc
     * Отключаем программу для тех, кому эта кампания недоступна
     */
    public static function isActive(): bool
    {
        $tutuBrandPermissions = new BrandsPartnerPermissions();
        $tutuBrandPermissions->brand_id = Subscriptions::TP_TUTU_ID;

        if(TRAVELPAYOUTS_DEBUG){
            return true;
        }
        return $tutuBrandPermissions->hasRule('can_promote_trains');
    }
}
