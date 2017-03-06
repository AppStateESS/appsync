<?php

namespace AppSync;

use \phpws2\Database;

/**
 * Factory class for retrieving and saving Log Entries to and from the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */


class LogEntryFactory {

    /**
     * Retrieves an ordered array of Log Entries.
     * @return array of LogEntryRestored
     */
    public static function getOrderedLogEntries()
    {
        $db    = PdoFactory::getPdoInstance();
        $query = 'SELECT * FROM appsync_log_entry ORDER BY occurred_on DESC';
        $stmt  = $db->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\LogEntryRestored');

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the LogEntries associated with this username.
     * @return array of LogEntryRestored
     */
    public static function getLogEntriesByUsername($username)
    {
        $db     = PdoFactory::getPdoInstance();
        $query  = 'SELECT * FROM appsync_log_entry WHERE username = :username';
        $stmt   = $db->prepare($query);

        $params = array(
            'username' => $username
        );


        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\LogEntryRestored');

        return $stmt->fetchAll();
    }

    /**
     * Retrieves the LogEntry associated with the given Id
     * @return logEntryRestored
     */
    public static function getLogEntryById($id)
    {
        $db     = PdoFactory::getPdoInstance();
        $query  = 'SELECT * FROM appsync_log_entry WHERE id = :id';
        $stmt   = $db->prepare($query);

        $params = array(
            'id' => $id
        );


        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'AppSync\LogEntryRestored');

        return $stmt->fetch();
    }

    /**
     * Saves a LogEntry to the database, if the logEntry object already exists updates
     * it with the new values.
     */
    public static function save($logEntry)
    {
        $db = PdoFactory::getPdoInstance();
        $id = $logEntry->getId();

        if (isset($id)) {
            $query  = 'UPDATE appsync_log_entry SET (description, username, occurred_on) = (:description, :username, :occurredOn) WHERE id = :id';
            $params = array(
                'id' => $id,
                'description' => $logEntry->getDescription(),
                'username'    => $logEntry->getUsername(),
                'occurredOn'  => $logEntry->getOccurredOn()
            );

        }else{
            // Insert
            $query  = "INSERT INTO appsync_log_entry (id, description, username, occurred_on) VALUES (nextval('appsync_log_entry_seq'), :description, :username, :occurredOn)";
            $params = array(
                'description' => $logEntry->getDescription(),
                'username'    => $logEntry->getUsername(),
                'occurredOn'  => $logEntry->getOccurredOn()
            );
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        // Update ID for a new object
        if (!isset($id)) {
            $logEntry->setId($db->lastInsertId('appsync_log_entry_seq'));
        }
    }
}
