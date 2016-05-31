<?php

namespace AppSync\Command;

/**
 * Controller class for handling a request to update the Settings.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class AjaxSetSettings extends \AppSync\Command {

      public function getRequestVars(){
          return array('action'=>'AjaxGetUmbrellaList');
      }

      /**
       * The main function for executing the command.
       */
      public function execute()
      {
          // Make sure the user has the appropriate permissions to make changes to the permissions settings.
          // Basically only deities will have access to permissions.
          if(!(\Current_User::isDeity()))
          {
              echo json_encode('user does not have permission to set live state');
              exit;
          }

          // Retrieve the input values from the request
          $newState     = $_REQUEST['state'];
          $newLiveUrl   = $_REQUEST['liveUrl'];
          $newTestUrl   = $_REQUEST['testUrl'];
          $newKey       = $_REQUEST['key'];
          $newBannerUrl = $_REQUEST['bannerUrl'];

          // If the newLiveUrl is not empty set the value in the database to the new value
          if($newLiveUrl != '')
          {
              $liveUrlSetting = \AppSync\SettingFactory::getSetting('orgsync_live_url');
              if(!$liveUrlSetting)
              {
                  $liveUrlSetting = new \AppSync\Setting(NULL, 'orgsync_live_url', $newLiveUrl);
              }
              else
              {
                  $liveUrlSetting->setValue($newLiveUrl);
              }
              \AppSync\SettingFactory::save($liveUrlSetting);
          }

          // If the newTestUrl is not empty, set the value in the database to the new value
          if($newTestUrl != '')
          {
              $testUrlSetting = \AppSync\SettingFactory::getSetting('orgsync_test_url');
              if(!$testUrlSetting)
              {
                  $testUrlSetting = new \AppSync\Setting(NULL, 'orgsync_test_url', $newTestUrl);
              }
              else
              {
                  $testUrlSetting->setValue($newTestUrl);
              }
              \AppSync\SettingFactory::save($testUrlSetting);
          }

          // If the newKey is not empty, set the value in the database to the new value
          if($newKey != '')
          {
              $keySetting = \AppSync\SettingFactory::getSetting('orgsync_key');
              if(!$keySetting)
              {
                  $keySetting = new \AppSync\Setting(NULL, 'orgsync_key', $newKey);
              }
              else
              {
                  $keySetting->setValue($newKey);
              }
              \AppSync\SettingFactory::save($keySetting);
          }

          // If the newBannerUrl is not empty, set the value in the database to the new value
          if($newBannerUrl != '')
          {
              $bannerUrlSetting = \AppSync\SettingFactory::getSetting('banner_url');
              if(!$bannerUrlSetting)
              {
                  $bannerUrlSetting = new \AppSync\Setting(NULL, 'banner_url', $newBannerUrl);
              }
              else
              {
                  $bannerUrlSetting->setValue($newBannerUrl);
              }
              \AppSync\SettingFactory::save($bannerUrlSetting);
          }

          // If the newState is 'LIVE' set the session to 'LIVE', if it is 'TEST'
          // set it to 'TEST', otherwise throw an error
          if($newState == 'LIVE')
          {
              $_SESSION['state'] = 'LIVE';
          }
          else if($newState == 'TEST')
          {
              $_SESSION['state'] = 'TEST';
          }
          else
          {
              echo json_encode(array('type' => 'error', 'message' => 'input does not match a valid server type: '.$newState));
              exit;
          }

          echo json_encode(array('type' => 'success', 'message' => 'Changes saved successfully.'));
          exit;
      }
}
