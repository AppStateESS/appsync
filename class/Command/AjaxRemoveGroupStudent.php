<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to remove a student from a particular
 * group in OrgSync.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxRemoveGroupStudent extends \AppSync\Command {

    public function getRequestVars(){
        return array('action'=>'AjaxRemoveGroupStudent');
    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $input   = $_REQUEST['inputData'];
        $portal  = $_REQUEST['portalId'];
        $groupId = $_REQUEST['groupId'];

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
            $student = \AppSync\UtilityFunctions::getStudentByEmail($input);
        }
        else {
            $student = \AppSync\UtilityFunctions::getStudentByBanner($input);
        }
        
        if($student == null || isset($student->Message) || $student->emailAddress == null)
        {
            $returnData = array('status' => 0);
            echo json_encode($returnData);
            exit;
        }


        $id     = \AppSync\UtilityFunctions::getIDFromUsername($student->{'emailAddress'});

        // Pass the student info and group id to the function responsible for interacting
        // with the OrgSync API
        $status = $this->removeGroupAccount($id, $groupId);

        // Create the response to the front end
        $name = $student->{'firstName'} . ' ' . $student->{'lastName'};
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
    * Place a user or users into a group. User can be a single user id or and array of ids
    *
    * @param int $user_id (can be array of user id's), int $group_id (groups id)
    * @return boolean (success or not)
    */
    public function removeGroupAccount($id, $group_id){
        // Use the UtilityFunctions to retrieve the info to be passed to the API
        $key = \AppSync\UtilityFunctions::getOrgSyncKey();
        $base_url = \AppSync\UtilityFunctions::getOrgSyncURL();

        // Create the url
        $import_url = $base_url."groups/$group_id/accounts/remove";

        // Initialize curl
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $import_url, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => "ids=$id&key=$key"));

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
                                     'Attempted to from user from group in Orgsync API via removeGroupAccount function, response was ' . $result->message,
                                     \Current_User::getUsername(),
                                     time());
                \AppSync\LogEntryFactory::save($logEntry);
                return FALSE;
            }
        }
        else
        {
            // Log that an attempt to interact with the API failed
            $logEntry = new \AppSync\LogEntry(null,
                                 'Attempted to from user from group in Orgsync API via removeGroupAccount function, response was ' . $result->message,
                                 \Current_User::getUsername(),
                                 time());
            \AppSync\LogEntryFactory::save($logEntry);
            return FALSE;
        }
    }

}
