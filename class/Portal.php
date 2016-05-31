<?php

namespace AppSync;

/**
 * Object class for the Portal
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class Portal {
    protected $orgsync_id;
    protected $name;
    protected $umbrella_id;

    public function __construct($orgsync_id, $name, $umbrella_id)
    {
        $this->orgsync_id  = $orgsync_id;
        $this->name        = $name;
        $this->umbrella_id = $umbrella_id;
    }

    /**
     * Returns the orgsync_id
     * @return orgsync_id
     */
    public function getOrgSyncId()
    {
        return $this->orgsync_id;
    }

    /**
     * Sets the orgsync_id for this portal.
     */
    public function setOrgSyncId($orgsyncId)
    {
        $this->orgsync_id = $orgsyncId;
    }

    /**
     * Returns the name
     * @return name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name for this portal.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the umbrella_id
     * @return umbrella_id
     */
    public function getUmbrellaId()
    {
        return $this->umbrella_id;
    }

    /**
     * Sets the umbrella_id for this portal.
     */
    public function setUmbrellaId($umbrellaId)
    {
        $this->umbrella_id = $umbrellaId;
    }

}
