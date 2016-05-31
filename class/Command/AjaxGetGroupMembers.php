<?php

namespace AppSync\Command;


/**
* Controller class for getting the members currently in a group.
*
* @author Chris Detsch
* @package appsync
*/
class AjaxGetGroupMembers {

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

        // Attempt to retrieve the group members, echoing the error message back if there is an exception
        try
        {
            $groupMembers = $this->getGroupMembers($_REQUEST['groupId']);

            echo json_encode($groupMembers);
        }
        catch(\Exception $e)
        {
            echo '<div style="display: none;">'.$e->getMessage().'</div>';
        }
        exit;
    }


    /**
     * Retrieves the group members for a given groupId.
     * @return group members array
     */
    public function getGroupMembers($group_id)
    {
        // Use the UtilityFunctions to retrieve the info to be passed to the API
        $key      = \AppSync\UtilityFunctions::getOrgSyncKey();
        $base_url = \AppSync\UtilityFunctions::getOrgSyncURL();

        // Initialize the curl request
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $base_url."groups/$group_id/accounts?key=$key"));

        // Execute the curl request and store the result
        $group_members = curl_exec($curl);

        // Check to make sure the result was valid
        if($group_members)
        {
            $group_members = json_decode($group_members);
        }
        else
        {
            $group_members = FALSE;
        }

        // Close curl
        curl_close($curl);
        return $group_members;
    }
}
