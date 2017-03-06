<?php

namespace AppSync;

use \phpws2\Database;

/**
 * Factory class for retrieving and saving Portals to and from the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class PortalFactory {

    /**
     * Retrieves all the portals from the database.
     * @return array of PortalRestored objects
     */
    public static function getPortals()
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_portal';
        $stmt  = $db->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\PortalRestored');

        return $stmt->fetchAll();
    }


    /**
     * Retrieves the portal matching the given orgsyncId from the database.
     * @return array containing one PortalRestored object
     */
    public static function getPortalById($orgsyncId)
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_portal WHERE orgsync_id = :orgsyncId';
        $stmt  = $db->prepare($query);

        $params = array(
            'orgsyncId' => $orgsyncId
        );

        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\PortalRestored');

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the portal matching the given name from the database.
     * @return array containing one PortalRestored object
     */
    public static function getPortalByName($name)
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_portal WHERE name = :name';
        $stmt  = $db->prepare($query);

        $params = array(
            'name' => $name
        );

        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\PortalRestored');

        return $stmt->fetchAll();
    }

    /**
     * Removes all the portals from the database, uses truncate;
     */
    public static function emptyPortals()
    {
        $db     = PdoFactory::getPdoInstance();
        $query  = 'TRUNCATE appsync_portal';
        $stmt   = $db->prepare($query);
        $stmt->execute();
    }

    /**
     * Saves a portal to the database, if the portal object already exists updates
     * it with the new values.
     */
    public static function save($portal)
    {
        $db         = PdoFactory::getPdoInstance();
        $orgsync_id = $portal->getOrgSyncId();
        $portalExists     = self::getPortalById($orgsync_id);

        if (!empty($portalExists)) {
            $query = "UPDATE appsync_portal SET (name, umbrella_id) = (:name, :umbrellaId) WHERE orgsync_id = :orgsyncId";

            $params = array(
                'name' => $portal->getName(),
                'umbrellaId' => $portal->getUmbrellaId(),
                'orgsyncId' => $orgsync_id
            );
        }else{
            // Insert
            $query = "INSERT INTO appsync_portal (orgsync_id, name, umbrella_id) VALUES (:orgsyncId, :name, :umbrellaId)";

            $params = array(
                'name' => $portal->getName(),
                'orgsyncId' => $portal->getOrgSyncId(),
                'umbrellaId' => $portal->getUmbrellaId()
            );
        }
        $stmt = $db->prepare($query);
        $stmt->execute($params);
    }
}
