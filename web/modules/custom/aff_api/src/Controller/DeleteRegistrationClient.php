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
class DeleteRegistrationClient extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function delete_registration_client_post( Request $request ) {
    $dataRecieved = json_decode($request->getContent(),true);
    $base_url = getBaseUrl();
    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
    $token = getToken($base_url, $session_name, $session_id);
    $nodeData = $this->queryNodeData($dataRecieved['id']);

    if(!isset($nodeData['field_unique_hash'][0]) || !isset($nodeData))
      return new Response('permission denied');

    if($nodeData['field_unique_hash'][0]['value'] != $dataRecieved['hash'])
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
      CURLOPT_URL => $base_url . '/tour_registrations/'. $dataRecieved['id'] .'?_format=hal_json',
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

    //sent aproval mail to client
    $client_email = $nodeData['field_email'][0]['value'];

    //email admin
    $nodeData['field_tour_date'][0]['value'] = date("d-M-Y / H:i A", $nodeData['field_tour_date'][0]['value']);
    $adminSettings = getDashboardAdminSettings();
    $nodeData['tourTypeSettings'] = $this->queryTourTypeSettings($nodeData);
    emailRegistrationCanceledByClientAdmin('registration_status_changed', $adminSettings['emails'], $nodeData, \Drupal::service('plugin.manager.mail'),'delete_by_client_admin');
    //email client
    $result['mail'] = emailRegistrationCanceledByClient('registration_status_changed', $client_email, $nodeData, \Drupal::service('plugin.manager.mail'),'delete_by_client');
    
    $result = json_encode($result);
    $response = new Response($result);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  public function queryNodeData($id){
    $registration = \Drupal::entityTypeManager()->getStorage('tour_registrations')->load($id);
    if($registration)
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

}//end class