<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;
use Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Html;
use Travelpayouts\components\formatters\AirportNameFormatter;
use Travelpayouts\components\formatters\DirectionNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnOriginDestination extends GridColumn
{
    const ONE_WAY_ARROW = '&#8594;';
    const ROUND_TRIP_ARROW = '&#8596;';

    protected $locale = 'en';
    /**
     * @var string
     * @required
     */
    public $originAttribute;
    /**
     * @var string
     * @required
     */
    public $destinationAttribute;

    public $delimiter = self::ROUND_TRIP_ARROW;

    public $useAirportNameFormatter = false;

    public function getDataCellValue($model, $key, $index)
    {
        $originValue = $model->{$this->originAttribute};
        $destinationValue = $model->{$this->destinationAttribute};

        return Html::tag('span', implode(Html::tag('span', " {$this->delimiter} ", ['style' => 'margin: 0 2px;']), [
            Html::tag('span', $this->getName($originValue), ['style' => 'white-space: nowrap;']),
            Html::tag('span', $this->getName($destinationValue), ['style' => 'white-space: nowrap;']),
        ]));
    }

    protected function getName($value): ?string
    {
        if ($this->useAirportNameFormatter) {
            $cityCode = AirportNameFormatter::getInstance()->getCityCode($value, $this->locale);
            if ($cityCode) {
                $value = $cityCode;
            }
        }

        return DirectionNameFormatter::getInstance()
            ->getName($value, $this->locale);
    }

    public function getSortOrderValue($model, $key, int $index)
    {
        $originValue = $model->{$this->originAttribute};
        $destinationValue = $model->{$this->destinationAttribute};
        return implode('', [
            $this->getName($originValue),
            $this->getName($destinationValue),
        ]);
    }
}
