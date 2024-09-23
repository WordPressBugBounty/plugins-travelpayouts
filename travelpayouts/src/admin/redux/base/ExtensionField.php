<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;

use TravelpayoutsSettingsFramework;
use Travelpayouts;
use Travelpayouts\components\Model;

/**
 * Class ExtensionField
 * @package Travelpayouts\src\admin\redux\base
 */
abstract class ExtensionField extends Model
{
    protected $parent;
    protected $field;
    protected $value;
    /**
     * Webpack assets to enqueue
     * @var array
     * @deprecated
     */
    protected $_assets = [];

    /**
     * Field Constructor.
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     * @param array $field
     * @param string $value
     * @param TravelpayoutsSettingsFramework $parent
     * @since Redux_Options 1.0.0
     */
    public function __construct($field = [], $value = '', $parent = '')
    {
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;
        $this->init();
    }

    /**
     * Basic init method
     */
    public function init()
    {

    }

    abstract public function render();

    /**
     * Basic enqueue method
     */
    public function enqueue()
    {
    }
}
