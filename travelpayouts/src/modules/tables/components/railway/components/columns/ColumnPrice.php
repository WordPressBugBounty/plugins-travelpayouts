<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response\ITrainCategory;

/**
 * @method ITrainCategory[] getDataCellValue($model, $key, $index)
 */
class ColumnPrice extends GridColumn
{

    public function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if (count($value)) {
            $result = [];
            foreach ($value as $category) {
                $result[] = $this->renderCategoryPrice($category);
            }
            return Html::tagArrayContent('div', ['class' => 'TP-train-prices'], $result);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    protected function getComputedCellValue($model, $value)
    {
        if (is_array($value)) {
            return array_values(array_filter($value, static function ($item) {
                return $item instanceof ITrainCategory;
            }));
        }

        return [];
    }

    protected function renderCategoryPrice(ITrainCategory $model): string
    {
        return Html::tagArrayContent('div', ['class' => 'TP-train-price'], [
            Html::tag('div', ['class' => 'TP-train-price__type'], $model->getLabel()),
            Html::tag('div', ['class' => 'TP-train-price__price'], PriceFormatter::getInstance()
                ->format($model->getValue(), 'rub')),
        ]);
    }

    protected function getSortOrderValue($model, $key, int $index): float
    {
        $value = $this->getDataCellValue($model, $key, $index);
        $priceList = array_column($value, 'price');
        return (float)min($priceList);
    }

}
