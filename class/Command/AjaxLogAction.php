<?php

namespace AppSync\Command;
/**
 * Controller class for handling a request to add a log entry to the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */
class AjaxLogAction extends \AppSync\Command {

    public function getRequestVars(){
        return array('action'=>'AjaxLogAction');
    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $id       = $_REQUEST['logId'];
        $action   = $_REQUEST['logAction'];
        $category = $_REQUEST['logCategory'];

        // Set the variable to 'to' initially
        $toFrom   = 'to';
        
        // Retrieve the username
        $username = \Current_User::getUsername();

        // If the action is set as removing then switch the variable to from
        if($action == 'Removing')
        {
            $toFrom = 'from';
        }

        // Create a new logEntry with the values
        $logEntry = new \AppSync\LogEntry(null,
                             $action . ' students ' . $toFrom . ' ' . $category . ' with ' . $category . ' id ' . $id,
                             $username,
                             time());
        \AppSync\LogEntryFactory::save($logEntry);

        // Send a confirmation back to the front end
        echo json_encode('logged');
        exit;
    }
}
