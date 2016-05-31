<?php

namespace AppSync;

/**
 * Controller class for handling the requests from the client, performing the
 * appropriate action for each request.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AppSync {

    private $content;


    public function __construct()
    {

    }

    /**
     * Returns this classes content.
     * @return content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Handles the requests by first retrieving them and setting the content to the correct
     * command corresponding to the action.  If the request is not set just goes to the main menu.
     */
    public function handleRequest()
    {
        // Fetch the action from the REQUEST.
        if (!isset($_REQUEST['action'])) {
            $action = 'ShowMainMenu';
        } else {
            $action = $_REQUEST['action'];
        }

        $action = 'AppSync\Command\\' . $action;

        $ctrl = new $action();
        $this->content = $ctrl->execute($this);

    }

}
