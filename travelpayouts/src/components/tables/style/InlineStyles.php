<?php

namespace Travelpayouts\components\tables\style;

use Travelpayouts\components\interfaces\IBuilderResult;

/**
 * Class InlineStyles
 * @package Travelpayouts\components\tables\style
 */
class InlineStyles implements IBuilderResult
{
    protected $baseCssClassName = '';
    protected $stylesArray = [];
    protected $selectorPriority = 1;

    /**
     * InlineStyles constructor.
     * @param string $cssClassName
     */
    public function __construct($cssClassName)
    {
        $this->baseCssClassName = $cssClassName;
    }

    /**
     * @param string $cssClassName
     * @return static
     */
    public static function create($cssClassName)
    {
        return new static($cssClassName);
    }

    /**
     * @param string $class
     * @param array $styles
     * @return $this
     */
    public function add($class, $styles = [])
    {
        if (is_array($styles) && !empty($styles)) {
            if (isset($this->stylesArray[$class]) && is_array($this->stylesArray[$class])) {
                $this->stylesArray[$class] = array_merge($this->stylesArray[$class], $styles);
            } else {
                $this->stylesArray[$class] = $styles;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        $results = [];
        foreach ($this->stylesArray as $class => $styles) {
            $results[] = $this->wrap(
                $this->addBaseClass($class),
                $this->arrayToInlineStyles($styles)
            );
        }

        if (empty($results)) {
            return '';
        }

        return implode(' ', $results);
    }

    private function addBaseClass($classes)
    {
        $data = [];
        $classArray = explode(',', $classes);
        foreach ($classArray as $class) {
            // повторяем базовый класс согласно значению указанному в selectorPriority
            $baseCssClassName = str_repeat($this->baseCssClassName,$this->selectorPriority);
            $data[] = implode(' ', [$baseCssClassName, $class]);
        }

        return implode(', ', $data);
    }


    private function arrayToInlineStyles($styles)
    {
        $style = '';
        foreach ($styles as $key => $value) {
            if (!empty($value)) {
                $style .= $key . ': ' . $value . ';';
            }
        }

        return $style;
    }

    private function wrap($name, $styles)
    {
        if (empty($styles)) {
            return '';
        }

        return $name . ' {' . $styles . '}';
    }

    /**
     * Устанавливаем приоритетность стилей
     * @param int $selectorPriority
     * @return InlineStyles
     */
    public function setSelectorPriority($selectorPriority)
    {
        if (is_numeric($selectorPriority)) {
            $this->selectorPriority = $selectorPriority;
        }
        return $this;
    }

}
