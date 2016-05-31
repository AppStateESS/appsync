<?php

namespace AppSync;

/**
 * Object class for the Umbrella
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class Umbrella {

    protected $id;
    protected $name;
    protected $orgsync_id;

    // TODO: make first parameter an instance of $student
    public function __construct($id, $name, $orgsync_id)
    {
    	$this->id         = $id;
        $this->name       = $name;
        $this->orgsync_id = $orgsync_id;
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
     * Returns the name field
     * @return name
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * Sets the name field for this class.
     */
    public function setName($name)
    {
      $this->name = $name;
    }

    /**
     * Returns the orgsync_id field
     * @return orgsync_id
     */
    public function getOrgSyncId()
    {
    	return $this->orgsync_id;
    }

    /**
     * Sets the orgsync_id field for this class.
     */
    public function setOrgSyncId($orgsyncId)
    {
      $this->orgsync_id = $orgsyncId;
    }
}
