<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to retrieve all the log entries.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxGetLog extends \AppSync\Command {

    public function getRequestVars(){
        return array('action'=>'AjaxGetLogPager');
    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the logs from the database
        $logs = \AppSync\LogEntryFactory::getOrderedLogEntries();

        $response = array();

        // For each logEntry create a row in the response array.
        foreach ($logs as $logEntry)
        {
            $row = array('occurredOn' => date('m/d/Y h:m:s a', $logEntry->getOccurredOn()),
                         'username'   => $logEntry->getUsername(),
                         'desc'       => $logEntry->getDescription());
            $response[] = $row;
        }

        // Echo the json encoded data back to the front end.
        echo json_encode($response);
        exit;
    }
}
