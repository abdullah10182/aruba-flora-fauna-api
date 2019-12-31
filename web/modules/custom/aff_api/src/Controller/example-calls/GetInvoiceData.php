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
class GetInvoiceData extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_invoice_data( Request $request ) {

    $response_array= [];
    $is_client = false;

    $current_user = \Drupal::currentUser();
    $current_user_id =  User::load(\Drupal::currentUser()->id())->get('uid')->value;
    $roles = $current_user->getRoles();
    foreach ($roles as $key => $value) {
      if($value == 'client'){
        $user_id = $current_user_id;
        $is_client = true;
        break;
      }else
        $user_id = $request->get('user');
    }
   
    //query function 
    $invoiceData = $this->query_invoice_data($user_id, $is_client);

    //print_r($response_full);die;
    $response = new Response(json_encode($invoiceData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_invoice_data($user_id, $is_client){
      $ids = \Drupal::entityQuery('admin_settings')
      ->condition('id', 1)
      ->execute();
      $settings = \Drupal::entityTypeManager()->getStorage('admin_settings')->loadMultiple($ids);
   
      //values    
      $settingsToReturn = [];
      $settingsToReturn['adminCosts'] = $settings[1]->field_ad->getValue()[0]['value'];
      $settingsToReturn['orderInvoiceDueDays'] = $settings[1]->field_in->getValue()[0]['value'];
      $settingsToReturn['bankAccount'] = $settings[1]->field_bank_account->getValue()[0]['value'];
      $settingsToReturn['companyName'] = $settings[1]->field_company_name->getValue()[0]['value'];
      $settingsToReturn['companyWebsite'] = $settings[1]->field_company_website->getValue()[0]['value'];
      $settingsToReturn['contactPerson'] = $settings[1]->field_contact_person->getValue()[0]['value'];
      $settingsToReturn['contactPersonTitle'] = $settings[1]->field_contact_person_email->getValue()[0]['value'];
      $settingsToReturn['contactPersonTelephone'] = $settings[1]->field_contact_person_telephone->getValue()[0]['value'];
      $settingsToReturn['contactPersonEmail'] = $settings[1]->field_contact_person_title->getValue()[0]['value'];
      $settingsToReturn['termsAndConditions'] = $settings[1]->field_terms_and_conditions->getValue()[0]['value'];
      return $settingsToReturn;
    
  }

}//end class

