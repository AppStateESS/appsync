<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to retrieve the settings in the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxGetSettings extends \AppSync\Command {

      public function getRequestVars(){
          return array('action'=>'AjaxGetUmbrellaList');
      }

      /**
       * The main function for executing the command.
       */
      public function execute()
      {
          // Make sure the user has the appropriate permissions to access settings.
          // Basically only deities will have access to settings.
          if(!(\Current_User::isDeity()))
          {
              echo json_encode('user does not have permission to retrieve live state');
              exit;
          }

          // Set the variables to base values
          $liveUrl   = '';
          $testUrl   = '';
          $key       = '';
          $bannerUrl = '';

          // Retrieve the settings from the database
          $liveUrlSetting   = \AppSync\SettingFactory::getSetting('orgsync_live_url');
          $testUrlSetting   = \AppSync\SettingFactory::getSetting('orgsync_test_url');
          $keySetting       = \AppSync\SettingFactory::getSetting('orgsync_key');
          $bannerUrlSetting = \AppSync\SettingFactory::getSetting('banner_url');


          // If the settings are non null then set the variables, otherwise leave them empty
          if($liveUrlSetting)
          {
              $liveUrl = $liveUrlSetting->getValue();
          }
          if($testUrlSetting)
          {
              $testUrl = $testUrlSetting->getValue();
          }
          if($keySetting)
          {
              $key = $keySetting->getValue();
          }
          if($bannerUrlSetting)
          {
              $bannerUrl = $bannerUrlSetting->getValue();
          }

          // If the state is not set it, set it to LIVE and use that as the state,
          // otherwise use the current state.
          if($_SESSION['state'] != null)
          {
              $state = $_SESSION['state'];
          }
          else
          {
              $state             = 'LIVE';
              $_SESSION['state'] = 'LIVE';
          }

          // Echo the values back to the front end after encoding them.
          echo json_encode(array('state' => $state, 'liveUrl' => $liveUrl, 'testUrl' => $testUrl,
                                    'key' => $key, 'bannerUrl' => $bannerUrl));
          exit;
      }
}
