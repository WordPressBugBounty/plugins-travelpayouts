<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\model;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\includes\ReduxConfigurator;

/**
 * Class ReduxOptionCollectionModel
 * @package Travelpayouts\components\model
 */
abstract class ReduxOptionCollectionModel extends OptionCollectionModel
{
    /**
     * @Inject
     * @var ReduxConfigurator
     */
    protected $redux;

    protected function getCollection()
    {
        $data = $this->redux->getOption($this->optionPath(), '[]');
        return json_decode($data, true);
    }

    protected function setCollection($value)
    {
        return $this->redux->setOption($this->optionPath(), $value);
    }
}
