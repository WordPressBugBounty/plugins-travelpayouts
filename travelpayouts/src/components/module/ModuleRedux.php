<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\module;

use Travelpayouts\components\Module;
use Travelpayouts\components\shortcodes\ShortcodeHelper;

abstract class ModuleRedux extends Module implements IModuleRedux
{
    /**
     * @var string[]
     */
    protected $shortcodeList = [];

    /**
     * Tags of removed shortcodes: the class is gone, but the tag must still
     * answer with emptiness, or WordPress prints `[tp_...]` as plain text.
     * @var string[]
     */
    protected $disabledShortcodeList = [];

    /**
     * @see $shortcodeList
     * @see $disabledShortcodeList
     */
    public function registerShortcodes()
    {
        ShortcodeHelper::registerShortcodeList($this->shortcodeList);

        foreach ($this->disabledShortcodeList as $shortcodeTag) {
            ShortcodeHelper::registerDisabledShortcode($shortcodeTag);
        }
    }
}
