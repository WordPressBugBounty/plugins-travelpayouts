<?php

namespace Travelpayouts\modules\tables\components\settings;

use Travelpayouts;
use Travelpayouts\components\tables\style\InlineStyles;
use Travelpayouts\helpers\StringHelper;

/**
 * Class CustomTableStylesSection
 * @package Travelpayouts\modules\tables\components\settings
 */
class CustomTableStylesSection extends Fields
{
    const CUSTOM_THEME = 'custom';

    /**
     * @var string
     */
    public $customize_header;
    /**
     * @var
     */
    public $customize_body;
    /**
     * @var string
     */
    public $customize_buttons;
    /**
     * @var string
     */
    public $bg_header;
    /**
     * @var string
     */
    public $bg_header_active;
    /**
     * @var string
     */
    public $text_header;
    /**
     * @var string
     */
    public $text_header_active;
    /**
     * @var string
     */
    public $bg_body_odd;
    /**
     * @var string
     */
    public $bg_body_even;
    /**
     * @var string
     */
    public $text_body;
    /**
     * @var string
     */
    public $bg_body_hover;
    /**
     * @var string
     */
    public $bg_button;
    /**
     * @var string
     */
    public $bg_button_hover;
    /**
     * @var string
     */
    public $border_button;
    /**
     * @var string
     */
    public $text_button;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $requireForTableHeader = $this->requiredRule('customize_header', 'equals', true);
        $requireForTableBody = $this->requiredRule('customize_body', 'equals', true);
        $requireForTableButtons = $this->requiredRule('customize_buttons', 'equals', true);

        return [
            'customize_header' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Customize table header')),
            'bg_header' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header background color'))
                ->setDefault('#0099cc')
                ->setRequired($requireForTableHeader),
            'bg_header_active' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header active background color'))
                ->setDefault('#099dc7')
                ->setRequired($requireForTableHeader),
            'text_header' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header text color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableHeader),
            'text_header_active' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header active text color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableHeader),
            'customize_body' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Customize table body')),
            'bg_body_odd' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body odd row background color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableBody),
            'bg_body_even' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body even row background color'))
                ->setDefault('#f5f6f9')
                ->setRequired($requireForTableBody),
            'text_body' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body text color'))
                ->setDefault('#6c7a87')
                ->setRequired($requireForTableBody),
            'bg_body_hover' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table row hovered background color'))
                ->setDefault('#c1dfdd')
                ->setRequired($requireForTableBody),
            'customize_buttons' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Customize table buttons')),
            'bg_button' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button background color'))
                ->setDefault('#fcb942')
                ->setRequired($requireForTableButtons),
            'bg_button_hover' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button hovered background color'))
                ->setDefault('#fcb02d')
                ->setRequired($requireForTableButtons),
            'border_button' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button border color'))
                ->setDefault('#ce6408')
                ->setRequired($requireForTableButtons),
            'text_button' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button text color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableButtons),
        ];
    }

    /**
     * @return bool
     */
    public function getCustomizeHeader(): bool
    {
        return StringHelper::toBoolean($this->customize_header);
    }

    /**
     * @return bool
     */
    public function getCustomizeBody(): bool
    {
        return StringHelper::toBoolean($this->customize_body);
    }

    /**
     * @return bool
     */
    public function getCustomizeButtons(): bool
    {
        return StringHelper::toBoolean($this->customize_buttons);
    }

    /**
     * @param string $prefixSelector
     * @return InlineStyles
     */
    public function getInlineStyles($prefixSelector): InlineStyles
    {
        $styles = InlineStyles::create($prefixSelector . '.' . self::CUSTOM_THEME);

        // header styles
        if ($this->getCustomizeHeader()) {
            $styles->add(
                'table thead tr th',
                [
                    'background' => $this->bg_header,
                    'color' => $this->text_header,
                ]
            )
                ->add(
                    'table thead tr th:hover',
                    [
                        'background' => $this->bg_header_active,
                        'color' => $this->text_header_active,
                    ]
                )->add(
                    'table thead tr th.sorting_asc, table thead tr th.sorting_desc',
                    [
                        'background' => $this->bg_header_active,
                        'color' => $this->text_header_active,
                    ]
                );
        }

        // body styles
        if ($this->getCustomizeBody()) {
            $styles->add(
                'table tbody tr',
                [
                    'background-color' => $this->bg_body_odd,
                ]
            )->add(
                'table tbody tr:nth-child(2n)',
                [
                    'background-color' => $this->bg_body_even,
                ]
            )->add(
                'table tbody tr:hover',
                [
                    'background-color' => $this->bg_body_hover,
                ]
            )->add(
                'table tbody tr td',
                [
                    'color' => $this->text_body,
                ]
            )->add(
                'table>tbody>tr>td:before',
                [
                    'background' => $this->bg_header . ' !important',
                    'color' => $this->text_header . ' !important',
                ]
            );
        }

        // button and pagination buttons styles
        if ($this->getCustomizeButtons()) {
            $styles->add(
                'table tbody tr td a.travelpayouts-table-button',
                [
                    'background-color' => $this->bg_button,
                    'border-color' => $this->border_button,
                    'color' => $this->text_button,
                ]
            )->add(
                'table tbody tr td a.travelpayouts-table-button:hover',
                [
                    'background-color' => $this->bg_button_hover,
                ]
            )->add(
                '.dataTables_paginate .paginate_button.current, .dataTables_paginate .paginate_button.current:hover, .dataTables_paginate .paginate_button:hover',
                [
                    'background-color' => $this->bg_button,
                    'border-color' => $this->bg_button,
                    'color' => $this->text_button,
                ]
            )->add(
                '.dataTables_paginate .paginate_button',
                [
                    'background-color' => $this->bg_body_odd,
                    'color' => $this->text_body,
                ]
            );
        }

        return $styles;
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'custom_styles';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Customize tables design');
    }
}
