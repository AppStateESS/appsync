<?php

namespace AppSync\Command;


/**
* Controller class for getting portal search suggestion data in JSON format.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxGetPortalGroups {

    public function __construct()
    {

    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $portal = $_REQUEST['portalId'];

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

        // Attempt to retrieve the portal groups, echoing the error message back if there is an exception
        try
        {
            $portalGroups = $this->getOrgGroups($_REQUEST['portalId']);

            echo json_encode($portalGroups);
        }
        catch(\Exception $e)
        {
            echo '<div style="display: none;">'.$e->getMessage().'</div>';
        }
        exit;
    }


    /**
     * Retrieve the groups for a given portal.
     * @return array of groups
     */
    public function getOrgGroups($org_id)
    {
        // Use the UtilityFunctions to retrieve the info to be passed to the API
        $key = \AppSync\UtilityFunctions::getOrgSyncKey();
        $base_url = \AppSync\UtilityFunctions::getOrgSyncURL();

        // Initialize the curl request
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $base_url."orgs/$org_id/groups?key=$key"));

        // Execute the curl request and store the result
        $org_groups = curl_exec($curl);

        // Check to make sure the result was valid
        if($org_groups){
            $org_groups = json_decode($org_groups);
        }
        else
        {
            $org_groups = FALSE;
        }

        // Close curl
        curl_close($curl);
        return $org_groups;
    }
}
