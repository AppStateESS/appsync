<?php

namespace AppSync\Command;

/**
* Controller class for getting all permissions to be listed in the permissions
* list for deities.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxRetrieveUsers {

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
            echo json_encode('user does not have permission to retrieve other user information');
            exit;
        }

        // Retrieve the permissions from the database
        $permissions = \AppSync\UmbrellaAdminFactory::getAllUmbrellaAdmins();

        $userList   = array();
        $returnData = array();

        // For each permission check to see if the username is in the userList array,
        // if not then add it to the array
        foreach($permissions as $permission)
        {
            $username = $permission->getUsername();

            if(!in_array($username, $userList, true))
            {
                array_push($userList, $username);
            }
        }

        // For each username add it to an associative array to be sent to the front end
        foreach($userList as $user)
        {
            $node         = array('username' => $user);
            $returnData[] = $node;
        }

        echo json_encode($returnData);
        exit;
    }
}
