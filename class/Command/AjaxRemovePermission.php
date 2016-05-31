<?php

namespace AppSync\Command;

/**
* Controller class for removing a permission from the given user.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxRemovePermission {

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

        // Remove the permission from the database
        \AppSync\UmbrellaAdminFactory::removeUmbrellaAdmin($username, $umbrellaId);

        // Echo the fact that it succeeded back to the front end
        echo json_encode("success");
        exit;
    }
}
