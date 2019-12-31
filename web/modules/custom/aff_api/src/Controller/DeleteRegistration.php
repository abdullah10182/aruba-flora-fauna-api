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
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;

use  Drupal\user\Entity\User;

/**
 * Controller routines for weblab_dashboard routes.
 */
class DeleteRegistration extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function delete_registration_post( Request $request ) {
    $dataRecieved = json_decode($request->getContent(),true);
    $base_url = getBaseUrl();
    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
    $token = getToken($base_url, $session_name, $session_id);

    $authorized = $this->checkUserPermission();
    if(!$authorized)
      return new Response('permission denied');
     
    //create node obj for post
    $node = array(
      '_links' => array(
        'type' => array(
          'href' => getBaseUrl(false).'/rest/type/tour_registrations/submissions'    
        )
      ),
      'type' => array(
        'target_id' => 'submissions'
      )
    );
    
    $node = json_encode($node);
    
    //delete (edit) node
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url . '/tour_registrations/'. $dataRecieved['registration']['id'] .'?_format=hal_json',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-type: application/hal+json',
        'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
      ),
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_POSTFIELDS => $node,
    ));
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    if(curl_error($ch)){ echo 'error:' . curl_error($c);}
    $result=curl_exec($ch);
    curl_close($ch);
    //sent emails
    $result = json_decode($result,true);
    $result['mail'] = null;
  
    // //sent aproval mail to client
    if(isset($dataRecieved['cancelationMail']) && $dataRecieved['cancelationMail'] ){
      $client_email = $dataRecieved['registration']['email'];
      $result['mail'] = emailRegistrationCanceledClient('registration_status_changed', $client_email, $dataRecieved, \Drupal::service('plugin.manager.mail'),'delete');
    }
    

    $result = json_encode($result);
    $response = new Response($result);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  public function queryNodeData($id){
    $registration = \Drupal::entityTypeManager()->getStorage('tour_registrations')->load($id);
    return $registration->toArray();
  }

  public function queryTourTypeSettings($selectedTourId){
    $tour = \Drupal::entityTypeManager()->getStorage('tours')->load($selectedTourId);
    return $tour->toArray();
  }

  public function checkIfFull($selected_date, $dataRecieved){
    $orders = [];
    $ids = \Drupal::entityQuery('tour_registrations')
      ->condition('field_tour_date',  $selected_date, '=')
      ->condition('type','submissions')
      ->execute();
    $registrationCount = count($ids);
    $registrations = \Drupal::entityTypeManager()->getStorage('tour_registrations')->loadMultiple($ids); 

    //check if other type already on date
    $registrationDateSelectedType = null;
    if($registrationCount > 0){
      $registrations_array = [];
      foreach ($registrations as $registration) {
        $registrations_array[] = [
          'title' => $registration->title->value,
          'tourType' => $registration->field_type->value,
        ];
      }
      $registrationDateSelectedType = $registrations_array[0]['tourType'];
      if($dataRecieved['tourType'] != $registrationDateSelectedType){
        $returnObj['flag'] = true; 
        $returnObj['type'] = 'not_same'; 
        return $returnObj;
      }
    }
    //check of max reached
    $maxPerDay;
    if($dataRecieved['tourType'] == '1')
      $maxPerDay = 8;
    else
      $maxPerDay = 1;

    if ($registrationCount >= $maxPerDay){
      $returnObj['flag'] = true; 
      $returnObj['type'] = 'full'; 
      return $returnObj;
    }
    else{
      $returnObj['flag'] = false; 
      return $returnObj;
    }
  }

  public function checkUserPermission(){
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $authorized = false;

    foreach ($roles as $key => $value) {
      //print $value;
      //if($value == 'tour_admin' || $value == 'administrator'){
      if($value == 'tour_admin' || $value == 'administrator'){
        $authorized = true;
        break;
      }else {
        $authorized = false;
      }
    }

    return $authorized;
  }

}//end class