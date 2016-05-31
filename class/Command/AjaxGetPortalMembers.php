<?php

namespace AppSync\Command;


/**
* Controller class for getting students currently in a portal.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxGetPortalMembers {

    public function __construct()
    {

    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $portal = $_REQUEST['org_id'];

        // Retrieve other important values and objects
        $username    = \Current_User::getUsername();
        $portalObjs  = \AppSync\PortalFactory::getPortalById($portal);
        $umbrellaId  = $portalObjs[0]->getUmbrellaId();
        $permissions = \AppSync\UmbrellaAdminFactory::getUmbrellaAdmin($username, $umbrellaId);

        // If the permissions array is empty then the user does not have permission to use this command
        // throw an error back to the front end.
        if(sizeof($permissions) == 0)
        {
            echo '<div style="display: none;">User does not have permission to access this data.</div>';
            exit;
        }

        // Attempt to retrieve the portal members, echoing the error message back if there is an exception
        try
        {
            $portalMembers = $this->getOrgMembers($_REQUEST['org_id']);

            echo json_encode($portalMembers);

        }catch(\Exception $e)
        {
            echo '<div style="display: none;">'.$e->getMessage().'</div>';
        }
        exit;
    }


    /**
     *  Retrieve the current members of a portal from orgsync.
     *  @return array
     */
    public function getOrgMembers($org_id){
        // Use the UtilityFunctions to retrieve the info to be passed to the API
        $key      = \AppSync\UtilityFunctions::getOrgSyncKey();
        $base_url = \AppSync\UtilityFunctions::getOrgSyncURL();

        // Initialize the curl request
        $curl = curl_init();

        //get organization members by organization id
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $base_url."orgs/$org_id/accounts?key=$key"));

        // Execute the curl request and store the result
        $org_members = curl_exec($curl);

        // Check to make sure the result was valid
        if($org_members){
            $org_members = json_decode($org_members);
        }
        else
        {
            $org_members = FALSE;
        }
        
        // Close curl
        curl_close($curl);
        return $org_members;
    }
}
