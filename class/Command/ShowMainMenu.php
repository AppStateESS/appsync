<?php

namespace AppSync\Command;

/**
 * Controller class for setting up the Top level UI to be displayed.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class ShowMainMenu extends \AppSync\Command {


    public function getRequestVars(){
        return array('action'=>'ShowMainMenu');
    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        $view = new \AppSync\UI\TopUI();
        $this->display($view->display());
    }
}
