<?php

namespace AppSync\Command;

/**
* Controller class for adding permissions to the site for particular users.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxAddPermission {

    public function __construct()
    {

    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Make sure the user has the appropriate permissions to make changes to the permissions settings.
        // Basically only deities will have access to permissions.
        if(!(\Current_User::isDeity()))
        {
            echo json_encode("user does not have permission to change permissions");
            exit;
        }

        // Retrieve the input values from the request
        $username   = $_REQUEST['username'];
        $umbrellaId = $_REQUEST['umbrella'];

        // Retrieve the permissions from the database
        $permissions = \AppSync\UmbrellaAdminFactory::getUmbrellaAdmin($username, $umbrellaId);

        // If they already have permission to the given umbrella, then do nothing, otherwise
        // add the permission to their account.
        if(sizeof($permissions) == 0)
        {
            $newAdmin = new \AppSync\UmbrellaAdmin(null, $username, $umbrellaId);

            \AppSync\UmbrellaAdminFactory::save($newAdmin);

            echo json_encode("success");
            exit;
        }
        else
        {
            echo json_encode("already exists");
            exit;
        }
    }
}
