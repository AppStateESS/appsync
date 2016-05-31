<?php

namespace AppSync\UI;

/**
 * TopUI
 *
 * @author Chris Detsch
 */
class TopUI implements UI
{
    /**
     * Returns the proccessed template that will be displayed
     * @return template
     */
    public function display(){
      $tpl = array();

      javascript('jquery');
      javascriptMod('appsync', 'organization');

      return \PHPWS_Template::process($tpl, 'appsync', 'top.tpl');
    }
}
