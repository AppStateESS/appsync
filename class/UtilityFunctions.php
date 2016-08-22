<?php

namespace AppSync;

use \Database;

/**
 * A class for all the static utility functions that are used in multiple classes.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */


class UtilityFunctions {

    /**
     * Retrieves the OrgSync URL from the database, if the state is not set it
     * will set it to LIVE and use that as the state, otherwise it will use the
     * current state.
     * @return string - the url
     */
    public static function getOrgSyncURL()
    {
        if(isset($_SESSION['state']))
        {
            $state = $_SESSION['state'];
        }
        else
        {
            $state             = 'LIVE';
            $_SESSION['state'] = 'LIVE';
        }

        // If the state is not set to live then it will be set to test.
        if($state == 'LIVE')
        {
            return \AppSync\SettingFactory::getSetting('orgsync_live_url')->getValue();
        }
        else
        {
            return \AppSync\SettingFactory::getSetting('orgsync_test_url')->getValue();
        }
    }

    /**
     * Retrieves the OrgSync Key from the database.
     * @return string - the key
     */
    public static function getOrgSyncKey()
    {
        return \AppSync\SettingFactory::getSetting('orgsync_key')->getValue();
    }

    /**
     * Retrieves student objects from banner given the bannerid
     * @return student
     */
    public static function getStudentByBanner($banner)
    {
        // Retrieve the url for Banner from the database
        $base_url = \AppSync\SettingFactory::getSetting('banner_url')->getValue();


        // Initialize curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $base_url."Student/$banner");
        // Execute the curl request and store its result
        $student = json_decode(curl_exec($curl));
        curl_close($curl);
	
        if($student)
        {
	  return $student;
        }
        else
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $base_url."Faculty/$banner");
            // Execute the curl request and store its result
            $faculty = json_decode(curl_exec($curl));

            return $faculty;
        }
    }

    /**
     * Retrieves a student orgsync id when provided with a banner id
     * @return student_id
     */
    public static function getOrgSyncAcctByBannerID($banner_id){
      // Retrieve the orgsync url values using the static functions in this class
      $key = self::getOrgSyncKey();
      $base_url = self::getOrgSyncURL();
      $banner_profile_id = BANNER_ELEMENT_ID;
      $curl = curl_init();
      curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $base_url."accounts/custom_profile/$banner_profile_id/$banner_id?key=$key"));
      $account_result = curl_exec($curl);
      curl_close($curl);  
      if($account_result){
	$account = json_decode($account_result);
	if(is_array($account))
	  $account = array_shift($account);
      }else{
	$account = FALSE;
      }

      return $account;
    }

    /**
     * Retrieves a student orgsync id when provided with an email address.
     * @return student_id
     */
    public static function getOrgSyncAcctByEmail($email){
      // Retrieve the orgsync url values using the static functions in this class
      $key = self::getOrgSyncKey();
      $base_url = self::getOrgSyncURL();

      $curl = curl_init();
      curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $base_url."accounts/email/$email?key=$key"));
      $result = curl_exec($curl);
      curl_close($curl);
      if($result){
        $result = json_decode($result);
	return $result;
      }
    
      return false;
    }

    /**
     * Retrieves student objects from banner given an email address
     * @return student
     */
    public static function getStudentByEmail($email)
    {
        // Retrieve the url for Banner from the database
        $base_url = \AppSync\SettingFactory::getSetting('banner_url')->getValue();

        $parts = explode('@', $email);
        $username = strtolower($parts[0]);

        // Initialize curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $base_url."Student/$username");
        // Execute the curl request and store its result
        $student = json_decode(curl_exec($curl));
        curl_close($curl);

        if($student)
        {
            return $student;
        }
        else
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $base_url."Faculty/$username");
            // Execute the curl request and store its result
            $faculty = json_decode(curl_exec($curl));

            return $faculty;
        }
    }

    /**
     * Retrieves the Id for a user from orgsync
     * @return id
     */
    public static function getIDFromUsername($username){
        // Retrieve the orgsync url values using the static functions in this class
        $key = self::getOrgSyncKey();
        $base_url = self::getOrgSyncURL();
        // Initialize curl
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $base_url."accounts/username/$username?key=$key"));
        // Execute the curl request and store its result
        $result = curl_exec($curl);
        // Close curl
        curl_close($curl);
        // Check to see if the result is valid
        if($result){
            $result = json_decode($result);
            if(!empty($result->id))
            {
                return $result->id;
            }
        }
        // Log that an attempt to interact with the API failed
        $logEntry = new \AppSync\LogEntry(null,
                                 'Attempted to retrieve Id from Orgsync API via getIDFromUsername function, response was ' . $result->message,
                                 \Current_User::getUsername(),
                                 time());
        \AppSync\LogEntryFactory::save($logEntry);
        return false;
    }

        /**
     * Add an account to OrgSync.  Remember that you must be setup for SSO and know the proper
     * username format for your university.  Usually is the email but it could be different.
     *
     *
     */
    public static function addAccount($username, $first_name, $last_name, $student_id, $send_welcome=FALSE){
        // Retrieve the orgsync url values using the static functions in this class
        $key = self::getOrgSyncKey();
        $base_url = self::getOrgSyncURL();

        $json_data = array("username" => $username,
                           "send_welcome" => $send_welcome,
                           "account_attributes" => array("email_address" => $username,
                                                         "first_name" => $first_name,
                                                         "last_name" => $last_name),
                           "identification_card_numbers" => array($student_id));
        //    $json_data = array("username" => $username, "send_welcome" => true, "account_attributes" => array("email_address" => $username, "first_name" => $first_name, "last_name" => $last_name));
        $json_data = json_encode($json_data);
        $url = $base_url."/accounts?key=$key";
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_TIMEOUT => 900, CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $json_data));
        $result = curl_exec($curl);
        curl_close($curl);
        if($result){
            $result = json_decode($result);
            if(!empty($result->id)){
                return TRUE;
            }else{
                echo var_dump($result); //need to write this to log instead of echo
                return FALSE;
            }
        }
    }


}
