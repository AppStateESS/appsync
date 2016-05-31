<?php

namespace AppSync;

use \Database;

/**
 * Factory class for retrieving and saving UmbrellaAdmins to and from the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */


class UmbrellaAdminFactory {

    /**
     * Retrieves the Umbrella admins associated with this username.
     * @return array of UmbrellaAdminRestored
     */
    public static function getUmbrellaAdminByUsername($username)
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_umbrella_admin WHERE username = :username';
        $stmt  = $db->prepare($query);

        $params = array(
            'username' => $username
        );

        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\UmbrellaAdminRestored');

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the UmbrellaAdmin associated with the given username and umbrellaId
     * @return UmbrellaAdminRestored
     */
    public static function getUmbrellaAdmin($username, $umbrellaId)
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_umbrella_admin WHERE username = :username AND umbrella_id = :umbrellaId';
        $stmt  = $db->prepare($query);

        $params = array(
            'username' => $username,
            'umbrellaId' => $umbrellaId
        );


        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\UmbrellaAdminRestored');

        return $stmt->fetchAll();
    }

    /**
     * Retrieves all the UmbrellaAdmins
     * @return array of UmbrellaAdminRestored objects
     */
    public static function getAllUmbrellaAdmins()
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_umbrella_admin';
        $stmt  = $db->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\UmbrellaAdminRestored');

        return $stmt->fetchAll();
    }

    /**
     * Removes an UmbrellaAdmin with the given username and umbrellaId
     *
     */
    public static function removeUmbrellaAdmin($username, $umbrellaId)
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'DELETE FROM appsync_umbrella_admin WHERE username = :username AND umbrella_id = :umbrellaId';
        $stmt  = $db->prepare($query);

        $params = array(
            'username' => $username,
            'umbrellaId' => $umbrellaId
        );

        $stmt->execute($params);
    }

    /**
     * Saves an umbrellaAdmin to the database, if the umbrellaAdmin object already exists updates
     * it with the new values.
     */
    public static function save($umbrellaAdmin)
    {
        $db = PdoFactory::getPdoInstance();
        $id = $umbrellaAdmin->getId();

        if (isset($id)) {
            $query = "UPDATE appsync_umbrella_admin SET (username, umbrella_id ) = (:username, :umbrellaId) WHERE id = :id";

            $params = array(
                'id' => $id,
                'username' => $umbrellaAdmin->getUsername(),
                'umbrellaId' => $umbrellaAdmin->getUmbrellaId()
            );

        }else{
            // Insert
            $query = "INSERT INTO appsync_umbrella_admin (id, username, umbrella_id) VALUES (nextval('appsync_umbrella_admin_seq'), :username, :umbrellaId)";

            $params = array(
                'username' => $umbrellaAdmin->getUsername(),
                'umbrellaId' => $umbrellaAdmin->getUmbrellaId()
            );
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        // Update ID for a new object
        if (!isset($id)) {
            $umbrellaAdmin->setId($db->lastInsertId('appsync_umbrella_admin_seq'));
        }
    }
}
