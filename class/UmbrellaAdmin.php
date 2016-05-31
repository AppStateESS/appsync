<?php

namespace AppSync;

/**
 * Object class for the UmbrellaAdmin
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class UmbrellaAdmin {
    protected $id;
    protected $username;
    protected $umbrella_id;

    public function __construct($id, $username, $umbrella_id)
    {
        $this->id          = $id;
        $this->username    = $username;
        $this->umbrella_id = $umbrella_id;
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
     * Returns the umbrella_id field
     * @return umbrella_id
     */
    public function getUmbrellaId()
    {
        return $this->umbrella_id;
    }

    /**
     * Sets the umbrella_id field for this class.
     */
    public function setUmbrellaId($umbrellaId)
    {
        $this->umbrella_id = $umbrellaId;
    }

}
