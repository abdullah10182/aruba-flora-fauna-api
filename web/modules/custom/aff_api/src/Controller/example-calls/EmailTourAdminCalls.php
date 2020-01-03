<?php

/**
 * @file
 * Contains \Drupal\aff_api\Controller\Users.
 */

namespace Drupal\aff_api\Controller;

use Drupal\Core\Controller\ControllerBase;


use  Drupal\user\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Controller routines for weblab_dashboard routes.
 */
class EmailTourAdminCalls extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_tour_admin_emails( Request $request ) {
    $type = $request->get('type');
    
    $settings = \Drupal::entityTypeManager()->getStorage('tours')->load('5');

    //emails
    if($type == '1' || $type == '2')
      $emails = $settings->field_tour_admin_emails->getValue();
    else
      $emails = $settings->field_bus_tour_admin_emails->getValue();
    $response = new Response(json_encode($emails));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function sent_tour_admin_email( Request $request ) {
    $dataRecieved = $request->getContent();
    $dataRecieved_array = json_decode($dataRecieved,true);

    // print_r($dataRecieved_array);
    // die;
    $tourAdminEmails = $dataRecieved_array['tourAdminEmails'];

    $tourAdminEmails = $this->commaSeperateEmail($tourAdminEmails);
    
    //sent mail
    $result['mail'] = emailTourAdmins('registration_status_changed', $tourAdminEmails, $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
      
    $response_full = $result;

    //print_r($response_full);die;
    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }


  public function commaSeperateEmail($emails){
    $emailsArray = [];
    for ($i=0; $i < count($emails); $i++) { 
      $emailsArray[$i] = $emails[$i]['value'];
    }
    $emailsString = implode(', ', $emailsArray);
  
    return $emailsString;
  }

}//end class

