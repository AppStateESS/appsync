<?php

/**
 * @author Chris Detsch
 */
function appsync_update(&$content, $currentVersion)
{
    switch ($currentVersion) {
        case version_compare($currentVersion, '0.1.2', '<') :
            Users_Permission::registerPermissions('intern', $content);
        case version_compare($currentVersion, '0.1.3', '<') :
            $db = new PHPWS_DB();
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/intern/boost/updates/update_00.01.00.sql');
            if (PEAR::isError($result)) {
                return $result;
            }
    }
    return true;
}
