<?php

/**
 * @file
 * Contains \Drupal\weblab_dashboard\Controller\CreateOrder.
 */

namespace Drupal\aff_api\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * Controller routines for weblab_dashboard routes.
 */
class CreateRegistration extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function create_registration_post( Request $request ) {
    //data
    $dataRecieved = $request->getContent();
    $dataRecieved_array = json_decode($dataRecieved,true);
    $selectedTourId = $dataRecieved_array['selectedTourId'];
    $selectedDate = strtotime($dataRecieved_array['selectedDate']);
    $firstName = $dataRecieved_array['regsitrationFields']['firstName'];
    $lastName = $dataRecieved_array['regsitrationFields']['lastName'];
    $dob = $dataRecieved_array['regsitrationFields']['dob'];
    $address = $dataRecieved_array['regsitrationFields']['address'];
    $phone = $dataRecieved_array['regsitrationFields']['phone'];
    $email = $dataRecieved_array['regsitrationFields']['email'];
    $peopleAmount = $dataRecieved_array['regsitrationFields']['people'];
    $lang = $dataRecieved_array['lang'];
    $tourDateLinkAdmin = new DrupalDateTime($dataRecieved_array['selectedDate']);

    //config
    $base_url = getBaseUrl();
    $now = date("Y-m-d_H:i:s");
    $month = zeroFill(date('m'),2);
    $day = zeroFill(date('d'),2);
    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
    $token = getToken($base_url, $session_name, $session_id);
    $registration_number = 'registration-' . $now;

    //data to post
    $node = array(
      '_links' => array(
        'type' => array(
          'href' => getBaseUrl(false).'/rest/type/tour_registrations/submissions'
        )
      ),
      'type' => array(
        'target_id' => 'submissions'
      ),      
      //'field_id_image'  => array ( 0 => array ('target_id' => '554')),
      'title' => array (0 => array ('value' => $registration_number )),
      'field_type'  => array (0 => array ('value' => $selectedTourId)),
      'field_tour_date'  => array (0 => array ('value' => $selectedDate)),
      'field_first_name'  => array (0 => array ('value' => $firstName)),
      'field_last_name'  => array (0 => array ('value' => $lastName)),
      'field_date_of_birth'  => array (0 => array ('value' => $dob)),
      'field_address'  => array (0 => array ('value' => $address)),
      'field_phone'  => array (0 => array ('value' => $phone)),
      'field_email'  => array (0 => array ('value' => $email)),
      'field_amount_people_group'  => array (0 => array ('value' => $peopleAmount)),
      'field_language'  => array (0 => array ('value' => $lang)),
      'field_created'  => array (0 => array ('value' => strtotime(date("Y-m-d\TH:i:s")))),
    );
    $node = json_encode($node);


    //----------------------------------

    //query db for last check if date availeble
    $registrationCount = $this->query_registrations($selectedDate);
    $maxPerDay;
    if($selectedTourId == '1')
      $maxPerDay = 8;
    else
      $maxPerDay = 1;

    if($registrationCount >= $maxPerDay){
      $result['full_error'] = true;
      $result = json_encode($result);
      $response = new Response($result);
      $response->headers->set('Content-Type', 'application/json');
  
      return $response;
    }
   
    //post order
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url . '/entity/tour_registrations?_format=hal_json',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-type: application/hal+json',
        'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
        ),
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_POSTFIELDS => $node,
    ));
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    if(curl_error($ch)){ echo 'error:' . curl_error($ch);}
    $result=curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result,true);

    //sent emails
    $dataRecieved_array['tourTypeSettings'] = $this->queryTourTypeSettings($selectedTourId, $lang);
    $dataRecieved_array['tourTypeSettingsAdmin'] = $this->queryTourTypeSettings($selectedTourId, 'en');
    $dataRecieved_array['dateTimeCreated'] = date('l jS \of F Y h:i A');
    $dataRecieved_array['tourDateLinkAdmin'] = $tourDateLinkAdmin->format('Y-m-d');
    
    $result['mail'] = emailCreateRegistrationClient('registration_status_changed', $email, $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    $adminSettings = getDashboardAdminSettings();
    emailCreateRegistrationAdmin('registration_status_changed', $adminSettings['emails'], $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    
    $result = json_encode($result);
    $response = new Response($result);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  public function query_registrations($selected_date){
    $orders = [];
    $ids = \Drupal::entityQuery('tour_registrations')
      ->condition('field_tour_date',  $selected_date, '=')
      ->condition('type','submissions')
      ->execute();
    return count($ids);
  }

  public function queryTourTypeSettings($selectedTourId, $lang){
    $tour = \Drupal::entityTypeManager()->getStorage('tours')->load($selectedTourId);
    if($tour->getTranslation($lang))
      $tour = $tour->getTranslation($lang);
    $tourTypeSettings = [
      'title' => $tour->title->value,
      //'instructions' => $tour->field_instructions_text->value,
    ];
    return $tourTypeSettings;
  }

}//end class