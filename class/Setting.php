<?php

namespace AppSync;

/**
 * Object class for the Setting objects.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class Setting {
    protected $id;
    protected $setting;
    protected $value;

    public function __construct($id, $setting, $value)
    {
        $this->id      = $id;
        $this->setting = $setting;
        $this->value   = $value;
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
     * Returns the setting field
     * @return setting
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Sets the setting field for this class.
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;
    }

    /**
     * Returns the value field
     * @return value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value field for this class.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}
