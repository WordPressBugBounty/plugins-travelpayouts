<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;

use TravelpayoutsSettingsFramework;
use ReflectionClass;
use Exception;
use ReflectionException;

abstract class Extension extends TravelpayoutsSettingsFramework
{
    public $field_name;
    public $extension_dir;

    protected $_reflectionClass;
    protected $_directory;

    /**
     * Class Constructor. Defines the args for the extions class
     * @param TravelpayoutsSettingsFramework $reduxFramework
     * @return      void
     * @since       1.0.0
     * @access      public
     */
    public function __construct($reduxFramework)
    {
        $this->field_name = $this->get_field_class_name();
        if (
            $reduxFramework
            && property_exists($reduxFramework, 'args')
            && isset($reduxFramework->args['opt_name'])
        ) {
            $reduxOptName = $reduxFramework->args['opt_name'];
            add_filter('redux_travelpayouts/' . $reduxOptName . '/field/class/' . $this->field_name, [&$this, 'get_field_path']);
        }
    }

    /**
     * Forces the use of the embeded field path vs what the core typically would use
     * @return string
     * @throws ReflectionException
     */
    public function get_field_path()
    {
        $fieldPath = $this->get_reflection_file_dir() . '/field_' . $this->field_name . '.php';
        if (file_exists($fieldPath)) {
            return $fieldPath;
        }
        throw new Exception("Can't find field path: valid path is '$fieldPath'");
    }

    /**
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function get_reflection_class()
    {
        if (!$this->_reflectionClass) {
            $this->_reflectionClass = new ReflectionClass($this);
        }
        return $this->_reflectionClass;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function get_reflection_file_dir()
    {
        if (!$this->_directory) {
            $child_class = $this->get_reflection_class();
            $this->_directory = dirname($child_class->getFileName());
        }
        return $this->_directory;
    }


    protected function get_field_class_name()
    {
        if (preg_match('/^TravelpayoutsSettingsFramework_extension_(?<fieldName>.*)/', get_called_class(), $fieldMatches)) {
            return $fieldMatches['fieldName'];
        }
        return null;
    }
}
