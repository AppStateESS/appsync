<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to remove a student from a particular
 * portal in OrgSync.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxRemovePortalStudent extends \AppSync\Command {

    public function getRequestVars(){
        return array('action'=>'AjaxRemovePortalStudent');
    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $input  = $_REQUEST['inputData'];
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
            echo json_encode(array('status' => 0, 'message' => 'You do not have permission to add students to this group.'));
            exit;
        }

        // If the input is not a number then it must be a username, retrieve the banner
        // Retrieve the student and make sure it is not null, passing an error
        // back to the front end if it is
        if(!is_numeric($input))
        {
	  if(stripos($input,'@') === false){
	    $input .= '@appstate.edu';
	  }
	  $student = \AppSync\UtilityFunctions::getOrgSyncAcctByEmail($input);
        }
        else {
	  $student = \AppSync\UtilityFunctions::getOrgSyncAcctByBannerID($input);
        }
	
        if(!$student || !is_numeric($student->id))
        {
	  $returnData = array('status' => 0,'id' => $input);
            echo json_encode($returnData);
            exit;
        }

        // Pass the student info and group id to the function responsible for interacting
        // with the OrgSync API
        $status = $this->removeAccount($student->id, $portal);

        // Create the response to the front end
        $name = $student->first_name . ' ' . $student->last_name;
        if($status)
        {
            $returnData = array('status' => 1, 'name' => $name);
        }
        else
        {
            $returnData = array('status' => 2, 'name' => $name);
        }

        // Echo the values back to the front end after encoding them.
        echo json_encode($returnData);
        exit;
    }

    /**
    * Remove an account or multiple accounts from an organization. $ids can be one id or and array of ids.
    *
    *
    */
    public function removeAccount($id, $org_id){
        // Use the UtilityFunctions to retrieve the info to be passed to the API
        $key      = \AppSync\UtilityFunctions::getOrgSyncKey();
        $base_url = \AppSync\UtilityFunctions::getOrgSyncURL();

        // Create the url
        $url = $base_url."/orgs/$org_id/accounts/remove";

        // Initialize curl
        $curl = curl_init();
        curl_setopt_array($curl,
                            array(CURLOPT_TIMEOUT => 900,
                                    CURLOPT_RETURNTRANSFER => 1,
                                    CURLOPT_URL => $url,
                                    CURLOPT_POST => 1,
                                    CURLOPT_POSTFIELDS => "ids=$id&key=$key"));

        // Execute the curl request and store the result
        $result = curl_exec($curl);

        // Close curl
        curl_close($curl);

        // Check to make sure the result was valid
        if($result){
            $result = json_decode($result);
            if(is_object($result) && isset($result->success) && $result->success == true)
            {
                return TRUE;
            }
            else
            {
                // Log that an attempt to interact with the API failed
                $logEntry = new \AppSync\LogEntry(null,
                                     'Attempted to remove user from portal in Orgsync API via removeAccount function, response was ' . $result->message,
                                     \Current_User::getUsername(),
                                     time());
                \AppSync\LogEntryFactory::save($logEntry);
                return FALSE;
            }
        }

        // Log that an attempt to interact with the API failed
        $logEntry = new \AppSync\LogEntry(null,
                             'Attempted to from user from portal in Orgsync API via removeAccount function, response was ' . $result->message,
                             \Current_User::getUsername(),
                             time());
        \AppSync\LogEntryFactory::save($logEntry);
    }

}
