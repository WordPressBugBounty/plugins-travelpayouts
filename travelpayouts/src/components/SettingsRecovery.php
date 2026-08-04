<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use Travelpayouts;
use Travelpayouts\components\notices\Notice;
use Travelpayouts\components\notices\Notices;
use Travelpayouts\includes\HooksLoader;

/**
 * Repairs a damaged settings blob before Redux gets a chance to overwrite it with defaults.
 *
 * get_option() returns false both for a missing row and for a row maybe_unserialize() failed to
 * unpack; Redux does not tell those apart and writes defaults in either case, destroying the config.
 * Hooking option_{$name} - which WP applies inside get_option() after maybe_unserialize() - hands
 * Redux an already restored array, so its write-defaults branch is never reached.
 */
class SettingsRecovery extends HookableObject
{
    /**
     * @Inject
     * @var Notices
     */
    protected $notices;

    /**
     * Suffix of the option that keeps the raw damaged blob.
     */
    public const CORRUPTED_SUFFIX = '_corrupted';

    /**
     * Slug of the settings page carrying the warning.
     */
    public const SETTINGS_PAGE = 'travelpayouts_options';

    /**
     * Watched options. The value of each one must always be an array.
     * @var string[]
     */
    protected $optionNameList = [TRAVELPAYOUTS_REDUX_OPTION];

    /**
     * Recovery result for the current request: array on success, false on failure.
     * Kept locally so the outcome does not depend on the state of the options cache.
     * @var array<string, array|false>
     */
    protected $recoveredList = [];

    /**
     * @inheritDoc
     */
    protected function hookList(HooksLoader $hooksLoader)
    {
        foreach ($this->optionNameList as $optionName) {
            $hooksLoader->addFilter(
                'option_' . $optionName,
                function ($value) use ($optionName) {
                    return $this->recover($value, $optionName);
                }
            );
        }

        // Before AdminHooks::renderNotices at priority 10: this only queues the notice into the
        // Notices component, and that callback is what actually prints and then clears the queue.
        $hooksLoader->addAction('admin_notices', [$this, 'renderCorruptedNotice'], 9);
    }

    /**
     * Returns the option value, restoring it when damaged.
     *
     * @param mixed $value value as it comes out of maybe_unserialize()
     * @param string $optionName
     * @return array|false array on success, otherwise false - Redux then writes defaults,
     *                     but the original is already stored in the twin option
     */
    public function recover($value, $optionName)
    {
        // Healthy value - the branch taken by virtually every call.
        if (is_array($value)) {
            return $value;
        }

        if (array_key_exists($optionName, $this->recoveredList)) {
            return $this->recoveredList[$optionName];
        }

        // Memoize before the work, not after: add_option() down the line fires hooks that may read
        // this very option, and a second entry would recurse until the stack blows up.
        $this->recoveredList[$optionName] = false;
        $this->recoveredList[$optionName] = $this->recoverFromDatabase($optionName);

        return $this->recoveredList[$optionName];
    }

    /**
     * Reads the raw option string and tries to repair it.
     *
     * @param string $optionName
     * @return array|false
     */
    protected function recoverFromDatabase($optionName)
    {
        $raw = $this->readRawOption($optionName);

        // No row at all - fresh install, writing defaults is the right thing to do.
        if ($raw === null || $raw === '') {
            return false;
        }

        $repaired = self::repair($raw);

        // Back the original up either way: a successful repair overwrites the row, and a failed one
        // gets Redux defaults written over it.
        $this->backupCorrupted($optionName, $raw);

        if ($repaired === null) {
            return false;
        }

        $this->writeRawOption($optionName, maybe_serialize($repaired));

        return $repaired;
    }

    /**
     * Tries to restore a serialized string.
     *
     * Pure function with no WP calls - tested directly.
     *
     * @param string $raw raw value from the database
     * @return array|null array on success, null when the value could not be restored
     */
    public static function repair($raw)
    {
        if (!is_string($raw) || $raw === '') {
            return null;
        }

        foreach (self::repairCandidates($raw) as $candidate) {
            $result = @unserialize($candidate);
            if (is_array($result) && $result !== []) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Repair attempts, from the cheapest to the most aggressive.
     *
     * @param string $raw
     * @return string[]
     */
    protected static function repairCandidates($raw)
    {
        $unescaped = stripslashes($raw);

        return [
            $raw,
            self::fixStringLengths($raw),
            $unescaped,
            self::fixStringLengths($unescaped),
        ];
    }

    /**
     * Recounts the length prefixes of s:N:"..."; tokens.
     *
     * Lengths drift when migration plugins replace a domain inside serialized data and when
     * 4-byte characters collapse on a transfer through utf8mb3.
     *
     * ponytail: the lazy pattern breaks on a value that itself contains `";`. That yields no false
     * positives - unserialize() validates the result anyway, such a blob just stays unrepaired.
     * Swap for a real serialization parser once such cases actually show up.
     *
     * @param string $raw
     * @return string
     */
    protected static function fixStringLengths($raw)
    {
        $fixed = preg_replace_callback(
            '/s:\d+:"(.*?)";/s',
            function ($matches) {
                return 's:' . strlen($matches[1]) . ':"' . $matches[1] . '";';
            },
            $raw
        );

        return $fixed === null ? $raw : $fixed;
    }

    /**
     * Reads the option value directly, bypassing the object cache and deserialization.
     *
     * @param string $optionName
     * @return string|null null when there is no such row in the table
     */
    protected function readRawOption($optionName)
    {
        global $wpdb;

        $raw = $wpdb->get_var(
            $wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $optionName)
        );

        return $raw === null ? null : (string)$raw;
    }

    /**
     * Writes the value directly, bypassing update_option().
     *
     * update_option() starts with get_option(), i.e. with a recursive entry into this very filter,
     * and on $old_value === false falls through to add_option(), which also resets autoload.
     *
     * @param string $optionName
     * @param string $serialized
     * @return void
     */
    protected function writeRawOption($optionName, $serialized)
    {
        global $wpdb;

        $wpdb->update($wpdb->options, ['option_value' => $serialized], ['option_name' => $optionName]);

        wp_cache_delete($optionName, 'options');
        wp_cache_delete('alloptions', 'options');
    }

    /**
     * Stores the damaged blob in a separate option. The first copy is never overwritten -
     * it was taken before any intervention and is therefore worth more than the later ones.
     *
     * @param string $optionName
     * @param string $raw
     * @return void
     */
    protected function backupCorrupted($optionName, $raw)
    {
        add_option($optionName . self::CORRUPTED_SUFFIX, $raw, '', 'no');
    }

    /**
     * Name of the notice. Doubles as its dismissal key, so it must not be shared with
     * another notice - dismissing that one would hide this warning as well.
     */
    public const NOTICE_NAME = 'settings-recovery-notice';

    /**
     * Warning on the settings page, shown for as long as the backup sits in the database.
     * @return void
     */
    public function renderCorruptedNotice()
    {
        if (!isset($_GET['page']) || $_GET['page'] !== self::SETTINGS_PAGE) {
            return;
        }

        foreach ($this->optionNameList as $optionName) {
            $backupName = $optionName . self::CORRUPTED_SUFFIX;
            if (get_option($backupName, '') === '') {
                continue;
            }

            $this->notices->add(
                Notice::create(self::NOTICE_NAME)
                    ->setType(Notice::NOTICE_TYPE_WARNING)
                    ->setTitle(Travelpayouts::__(
                        'Travelpayouts settings in the database were damaged and have been restored automatically'
                    ))->setDescription(
                        Travelpayouts::__(
                            'This usually happens after a site migration. A copy of the damaged data is kept in the {option} option — check the settings on this page, then ask your developer to delete it.',
                            ['option' => '<code>' . esc_html($backupName) . '</code>']
                        )
                    )->setCloseable()
            );
        }
    }
}
