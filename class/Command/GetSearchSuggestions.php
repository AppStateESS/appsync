<?php

namespace AppSync\Command;


/**
 * Controller class for getting portal search suggestion data in JSON format.
 *
 * @author Chris Detsch
 * @package appsync
 */
class GetSearchSuggestions {

    const tokenLimit        = 2;
    const fuzzyTolerance    = 3;
    const resultLimit       = 10;

    public function __construct()
    {

    }

    /**
     * The main function for executing the command.
     */
    public function execute()
    {
        // Retrieve the values from the request
        $umbrellaId = $_REQUEST['umbrellaId'];

        // Retrieve other important values and objects
        $username    = \Current_User::getUsername();
        $permissions = \AppSync\UmbrellaAdminFactory::getUmbrellaAdmin($username, $umbrellaId);

        // If the permissions array is empty then the user does not have permission to use this command
        // throw an error back to the front end.
        if(sizeof($permissions) == 0)
        {
            echo '<div style="display: none;">User does not have permission to access this data.</div>';
            exit;
        }

        // Attempt to retrieve the portals and do a fuzzy search of them for the searchString
        try
        {
            $portals = \AppSync\PortalFactory::getPortals();

            $searchString = $_REQUEST['searchString'];
            $umbrella     = $_REQUEST['umbrellaId'];

            $portList = $this->portalFuzzySearch($searchString, $umbrella, $portals);
            echo $this->encodePortals($portList);
        }catch(\Exception $e)
        {
            echo '<div style="display: none;">'.$e->getMessage().'</div>';
        }
        exit;
    }


    private function portalFuzzySearch($string, $umbrellaId, $orgList)
    {
        $portList = array();
        $lowerString = strtolower($string);
        foreach ($orgList as $org) {
            if($org->getUmbrellaId() == $umbrellaId)
            {
                $name = strtolower($org->getName());
                if(strpos($name, $lowerString) !== false)
                {
                    array_push($portList, $org);
                }
            }
        }
        return $portList;
    }


    /**
     * Takes an array of Student objects and encodes them into a
     * json_encoded string.
     */
    private function encodePortals($portals) {
        $portalsEncoded = array();

        $i = 0;
        foreach($portals as $portal) {
            $portalsEncoded[$i]['name'] = $portal->getName();
            $portalsEncoded[$i]['id']   = $portal->getOrgSyncId();
            $i++;
        }

        return json_encode($portalsEncoded);
    }

}
