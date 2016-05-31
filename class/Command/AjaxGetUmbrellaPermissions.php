<?php

namespace AppSync\Command;

/**
* Controller class for getting umbrella permissions based on a username.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxGetUmbrellaPermissions {

    public function __construct()
    {

    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $username = $_REQUEST['username'];

        // Retrieve the permissions from the database
        $permissions = \AppSync\UmbrellaAdminFactory::getUmbrellaAdminByUsername($username);

        $returnData = array();
        $i          = 0;

        // For each permission add the id and name to the umbrella's array
        foreach($permissions as $permission)
        {
            $umbrella                        = \AppSync\UmbrellaFactory::getUmbrellaByOrgId($permission->getUmbrellaId());
            $returnData[$i]['umbrella_id']   = $umbrella->getOrgSyncId();
            $returnData[$i]['umbrella_name'] = $umbrella->getName();
            $i++;
        }

        // Json Encode the umbrellas array and pass it to the front end
        echo json_encode($returnData);
        exit;
    }
}
