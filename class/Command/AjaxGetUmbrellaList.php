<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to get a list of umbrellas for the user
 * to choose from.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxGetUmbrellaList extends \AppSync\Command {

      public function getRequestVars(){
          return array('action'=>'AjaxGetUmbrellaList');
      }

      /**
       * The main function for executing the command.
       */
      public function execute()
      {
        // Retrieve the current user's username
        $username    = \Current_User::getUsername();

        // Retrieve the users permissions
        $permissions = \AppSync\UmbrellaAdminFactory::getUmbrellaAdminByUsername($username);

        $umbrellas  = array();
        $i          = 0;

        // For each permission add the id and name to the umbrella's array
        foreach($permissions as $permission)
        {
            $umbrella                       = \AppSync\UmbrellaFactory::getUmbrellaByOrgId($permission->getUmbrellaId());
            $umbrellas[$i]['umbrella_id']   = $umbrella->getOrgSyncId();
            $umbrellas[$i]['umbrella_name'] = $umbrella->getName();
            $i++;
        }

        // Json Encode the umbrellas array and pass it to the front end
        echo json_encode($umbrellas);
        exit;
      }
}
