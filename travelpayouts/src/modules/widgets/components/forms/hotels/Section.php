<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\dictionary\Campaigns;

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
        $campaign = Campaigns::getInstance()->getItem('101');
        return [
            'title' => $campaign ? $campaign->name : Travelpayouts::__('Hotels'),
            'desc' => ReduxOptions::widgetsSectionDesc(),
            'icon' => 'el el-home',

        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'hotels';
    }
}
