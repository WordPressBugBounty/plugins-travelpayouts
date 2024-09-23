<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\dictionary\Campaigns;
use Travelpayouts\admin\redux\ReduxOptions;

class Section extends ModuleSection
{
    /**
     * @inheritdoc
     */
    public function __construct(Travelpayouts\modules\widgets\components\Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        $campaign = Campaigns::getInstance()->getItem('100');
        return [
            'title' => $campaign ? $campaign->name : Travelpayouts::__('Flights'),
            'desc' => ReduxOptions::widgetsSectionDesc(),
            'icon' => 'el el-plane',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'flights';
    }
}
