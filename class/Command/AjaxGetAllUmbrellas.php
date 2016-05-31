<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to retrieve all the possible umbrellas
 * This is used when adding permissions.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxGetAllUmbrellas extends \AppSync\Command {

    public function getRequestVars(){
        return array('action'=>'AjaxGetAllUmbrellas');
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

        // Retrieve the umbrellas
        $umbrellasResult = \AppSync\UmbrellaFactory::getUmbrellas();
        $umbrellas       = array();
        $i               = 0;
        
        // For each umbrella add it to the umbrellas array as an id and a name
        foreach($umbrellasResult as $umbrella)
        {
            $umbrellas[$i]['umbrella_id']   = $umbrella->getOrgSyncId();
            $umbrellas[$i]['umbrella_name'] = $umbrella->getName();
            $i++;
        }

        // Echo the json encoded array back to the front end.
        echo json_encode($umbrellas);
        exit;
    }
}
