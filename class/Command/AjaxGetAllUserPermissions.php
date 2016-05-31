<?php

namespace AppSync\Command;

/**
* Controller class for getting all permissions to be listed in the permissions
* list for deities.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxGetAllUserPermissions {

    public function __construct()
    {

    }

    public function execute()
    {
        // Make sure the user has the appropriate permissions to make changes to the permissions settings.
        // Basically only deities will have access to permissions.
        if(!(\Current_User::isDeity()))
        {
            echo json_encode('user does not have permission to retrieve permissions');
            exit;
        }

        // Retrieve the permissions from the database
        $permissions = \AppSync\UmbrellaAdminFactory::getAllUmbrellaAdmins();


        $userList   = array();
        $returnData = array();

        // For each permission if the username is not in the userList array add them
        foreach($permissions as $permission)
        {
            $username = $permission->getUsername();

            if(!in_array($username, $userList, true))
            {
                array_push($userList, $username);
            }
        }

        // For each user in the userList array create a permissionList that will be
        // returned to the front end
        foreach ($userList as $username)
        {
            $permissionList = "";
            $first = true;
            foreach ($permissions as $permission)
            {
                if($permission->getUsername() == $username)
                {
                    $umbrella = \AppSync\UmbrellaFactory::getUmbrellaByOrgId($permission->getUmbrellaId());
                    if($first)
                    {
                        $permissionList = $umbrella->getName();
                        $first = false;
                    }
                    else {
                        $permissionList .= ', ' . $umbrella->getName();
                    }
                }
            }
            $node = array('username' => $username, 'permissions' => $permissionList);
            array_push($returnData, $node);
        }

        // Echo the json encoded data back to the front end.
        echo json_encode($returnData);
        exit;
    }
}
