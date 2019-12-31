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
class EditRegistration extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function edit_registration_post( Request $request ) {
    $dataRecieved = json_decode($request->getContent(),true);

    $base_url = getBaseUrl();
    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
    $token = getToken($base_url, $session_name, $session_id);
    $nodeData = $this->queryNodeData($dataRecieved['id']);
    
    //create node obj for patch
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

    //if no unique hash present, add to node
    if(!$nodeData['field_unique_hash']){
      $uniqueId = uniqid($dataRecieved['id']) ;
      $node['field_unique_hash']  = array ( 0 => array ('value' => $uniqueId ));
    }

    if(isset($dataRecieved['approve']) && $dataRecieved['approve'])
      $node['field_approved']  = array ( 0 => array ('value' => true));

    if(isset($dataRecieved['reschedule']) && $dataRecieved['reschedule']){
      $selectedTourDate = strtotime($dataRecieved['tourDate'] . ' 9:30 AM');
      $isFull = $this->checkIfFull($selectedTourDate, $dataRecieved);
        
      if($isFull['flag']){
        $result['full_error'] = true;
        $result['type'] = $isFull['type'];
        $result = json_encode($result);
        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
      }
      
      $node['field_tour_date']  = array ( 0 => array ('value' => $selectedTourDate ));
    }
    
    $node = json_encode($node);
    
    //patch (edit) node
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url . '/tour_registrations/'. $dataRecieved['id'] .'?_format=hal_json',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-type: application/hal+json',
        'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
      ),
      CURLOPT_CUSTOMREQUEST => 'PATCH',
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
    
    //sent aproval mail to client
    if(isset($dataRecieved['sentApprovalMail']) && $dataRecieved['sentApprovalMail'] ){
      $dataRecieved['nodeData']= $nodeData;
      $dataRecieved['tourTypeSettings'] = $this->queryTourTypeSettings($dataRecieved['nodeData']);
      $dataRecieved['hash'] = $result['field_unique_hash'][0]['value'];
      $client_email = $dataRecieved['nodeData']['field_email'][0]['value'];
      $dataRecieved['nodeData']['field_tour_date'][0]['value'] = date("d-M-Y | g:i A",$dataRecieved['nodeData']['field_tour_date'][0]['value']);
      $dataRecieved['nodeData']['field_created'][0]['value'] = date("d-M-Y | g:i A",$dataRecieved['nodeData']['field_created'][0]['value']);
      $result['mail'] = emailRegistrationApprovedClient('registration_status_changed', $client_email, $dataRecieved, \Drupal::service('plugin.manager.mail'), 'approve');
    }
    
    //sent reshedule mail
    if(isset($dataRecieved['sentRescheduleMail']) && $dataRecieved['sentRescheduleMail'] ){
      $dataRecieved['nodeData']= $nodeData;
      $dataRecieved['tourTypeSettings'] = $this->queryTourTypeSettings($dataRecieved['nodeData']);
      $dataRecieved['hash'] = $result['field_unique_hash'][0]['value'];
      $client_email = $dataRecieved['nodeData']['field_email'][0]['value'];
      $dataRecieved['nodeData']['field_tour_date'][0]['value'] = date("d-M-Y | g:i A",$dataRecieved['nodeData']['field_tour_date'][0]['value']);
      $dataRecieved['nodeData']['field_created'][0]['value'] = date("d-M-Y | g:i A",$dataRecieved['nodeData']['field_created'][0]['value']);
      $result['mail'] = emailRegistrationRescheduledClient('registration_status_changed', $client_email, $dataRecieved, \Drupal::service('plugin.manager.mail'), 'reschedule');
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

  public function queryTourTypeSettings($node){
    $selectedTourId = $node['field_type'][0]['value'];
    $lang = $node['field_language'][0]['value'];
    $tour = \Drupal::entityTypeManager()->getStorage('tours')->load($selectedTourId);
    if($tour->getTranslation($lang))
      $tour = $tour->getTranslation($lang);
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

}//end class