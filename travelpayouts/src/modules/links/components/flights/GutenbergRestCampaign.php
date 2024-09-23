<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\links\components\flights;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\rest\models\BaseGutenbergRestCampaign;
use Travelpayouts\components\brands\Subscriptions;

class GutenbergRestCampaign extends BaseGutenbergRestCampaign
{
    /**
     * @Inject
     * @var Shortcode
     */
    public $tp_link;

    /**
     * @inheritDoc
     */
    protected function campaignId()
    {
        return Subscriptions::AVIASALES_ID;
    }
}
