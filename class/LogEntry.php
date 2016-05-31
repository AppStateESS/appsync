<?php

namespace AppSync;

/**
 * Object class for the LogEntry
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class LogEntry {
    protected $id;
    protected $username;
    protected $occurred_on;
    protected $description;

    public function __construct($id, $description , $username, $occurred_on)
    {
        $this->id          = $id;
        $this->username    = $username;
        $this->occurred_on = $occurred_on;
        $this->description = $description;
    }

    /**
     * Returns the id field
     * @return id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id field for this class.
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the description field
     * @return description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description field for this class.
     */
    public function setDescription($desc)
    {
        $this->description = $desc;
    }


    /**
     * Returns the username field
     * @return username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username field for this class.
     */
    public function setName($username)
    {
        $this->username = $username;
    }

    /**
     * Returns the occurred_on field
     * @return occurred_on
     */
    public function getOccurredOn()
    {
        return $this->occurred_on;
    }

    /**
     * Sets the occurred_on field for this class.
     */
    public function setOccurredOn($timestamp)
    {
        $this->occurred_on = $timestamp;
    }
}
