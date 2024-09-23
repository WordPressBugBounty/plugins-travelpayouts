<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response\Station;

class ColumnRoute extends GridColumn
{

    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var mixed|Station[] $value */
        $value = $this->getDataCellValue($model, $key, $index);
        if (is_array($value)) {
            $delimiterElement = Html::tag('div', ['class' => 'TP-train-route__delimiter'], '&#8594;');
            $result = [];
            foreach ($value as $stationIndex => $station) {
                $isFirst = $stationIndex === 0;
                $isLast = $stationIndex === count($value) - 1;

                $stationElement = Html::tag(
                    'div',
                    [
                        'class' => Html::classNames([
                            'TP-train-route__name',
                            $station->type === Station::STATION_TYPE_MAIN ? 'TP-train-route__name--main' : null,
                            $station->type === Station::STATION_TYPE_SECONDARY ? 'TP-train-route__name--secondary' : null,
                        ]),
                    ],
                    $station->getName()
                );

                if (!$isLast) {
                    $stationElement = implode(' ', [
                        $stationElement,
                        $delimiterElement,
                    ]);
                }

                $result[] = $this->renderStationWrapper($station, $stationElement, $isFirst, $isLast);
            }

            return Html::tagArrayContent('div', ['class' => 'TP-train-routes'], $result);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return is_array($value) ? count($value) : 0;
    }

    /**
     * @param Station $station
     * @param string $content
     * @param bool $isFirst
     * @param bool $isLast
     * @return string
     */
    protected function renderStationWrapper(Station $station, string $content, bool $isFirst = false, bool $isLast = false): string
    {
        return Html::tagArrayContent('div', [
            'class' =>
                Html::classNames([
                    'TP-train-route',
                    $isFirst ? 'TP-train-route--first' : null,
                    $isLast ? 'TP-train-route--last' : null,
                    $station->type === Station::STATION_TYPE_MAIN ? 'TP-train-route--main' : null,
                    $station->type === Station::STATION_TYPE_SECONDARY ? 'TP-train-route--secondary' : null,
                ]),
        ],
            $content);
    }

    /**
     * @inheritDoc
     */
    protected function getComputedCellValue($model, $value)
    {
        if (is_array($value)) {
            return array_values(array_filter($value, static function ($item) {
                if (!$item instanceof Station) {
                    return false;
                }
                return $item->getName() !== null;
            }));
        }
        return [];
    }

}
