<?php

namespace AppSync;

use \phpws2\Database;

/**
 * Factory class for retrieving Settings from the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class SettingFactory {

    /**
     * Retrieves the setting from the database.
     * @return SettingRestored
     */
    public static function getSetting($settingName)
    {
        $db     = PdoFactory::getPdoInstance();
        $query  = "SELECT * FROM appsync_settings WHERE setting = :setting";
        $stmt   = $db->prepare($query);
        $params = array('setting' => $settingName);

        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\SettingRestored');

        return $stmt->fetch();
    }

    /**
     * Saves a setting to the database, if the setting object already exists updates
     * it with the new values.
     */
    public static function save($setting)
    {
        $db = PdoFactory::getPdoInstance();
        $id = $setting->getId();

        if (isset($id)) {
            $query  = 'UPDATE appsync_settings SET value = :value WHERE id = :id';

            $params = array(
                'id' => $id,
                'value' => $setting->getValue()
            );

        }else{
            // Insert
            $query  = "INSERT INTO appsync_settings (id, setting, value) VALUES (nextval('appsync_settings_seq'), :setting, :value)";

            $params = array(
                'setting' => $setting->getSetting(),
                'value' => $setting->getValue()
            );
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
    }
}
