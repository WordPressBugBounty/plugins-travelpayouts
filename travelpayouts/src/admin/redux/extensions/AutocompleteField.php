<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions;

use Travelpayouts\admin\redux\base\ConfigurableField;

/**
 * @deprecated
 */
class AutocompleteField extends ConfigurableField
{
    public const TYPE = 'travelpayouts_autocomplete';

    public function render()
    {
        if (TRAVELPAYOUTS_DEBUG) {
            echo '<div class="tp-alert tp-alert--warning">AutocompleteField is deprecated</div>';
        }
    }
}