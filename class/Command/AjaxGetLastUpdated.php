<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to update the portals in the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxGetLastUpdated extends \AppSync\Command {

      public function getRequestVars(){
          return array('action'=>'AjaxGetLastUpdated');
      }

      /**
       * The main function for executing the command.
       * This command is currently turned off as it will probably be put into scheduled action in the future.
       */
      public function execute()
      {
          $lastUpdatedSetting = \AppSync\SettingFactory::getSetting('last_updated');

          if($lastUpdatedSetting)
          {
              $lastUpdated = $lastUpdatedSetting->getValue();

              $dateUpdated = date("h:i:s a m-d-Y ", $lastUpdated);

              // Echo the values back to the front end after encoding them.
              echo json_encode(array('lastUpdated' => $dateUpdated));
              exit;
          }
          else
          {
              // Echo the values back to the front end after encoding them.
              echo json_encode(array('lastUpdated' => 'never'));
              exit;
          }
      }
}
