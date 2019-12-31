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
class EditOrder extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function edit_order_post( Request $request ) {

    $dataRecieved = $request->getContent();
    $base_url = getBaseUrl();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $user_id =  $user->get('uid')->value;
    $roles =  $user->getRoles();
    $company = $user->get('field_company_name')->value;
    $now = date("Y-m-d\TH:i:s");
    $month = zeroFill(date('m'),2);
    $day = zeroFill(date('d'),2);
    $order_name = 'order-'.$company.'-'.$now;
    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
    $token = getToken($base_url, $session_name, $session_id);
    
    $order_number = '';
    $dataRecieved_array = json_decode($dataRecieved, true);
    $node_id = $dataRecieved_array['id'];
    $client_email = $dataRecieved_array['mail'];
    $order_state = $dataRecieved_array['field_order_state_tax'];
    $order_state_name = $dataRecieved_array['order_state_name'];
    $order_state_name_old = $dataRecieved_array['oldState'];
    $order_data = json_encode($dataRecieved_array['field_order_data']);
    $service_date_time = $dataRecieved_array['service_date_time'];
    $sent_state_change_mail = $dataRecieved_array['field_order_data']['orderData']['stateChangeMail'];
    $sent_email_assworker = $dataRecieved_array['field_order_data']['orderData']['sentEmailAssWorker'];
    $assigned_worker_data = json_encode($dataRecieved_array['assignedWorkerData']);
    $assigned_worker_id = $dataRecieved_array['assignedWorkerId'];
    $field_results;

    $hasPersmission = $this->checkPermission($order_state_name, $order_state_name_old);
    if(!$hasPersmission)
      die('permission dienied');
  
    //create node obj for post
    $node = array(
      '_links' => array(
        'type' => array(
          'href' => getBaseUrl(false).'/rest/type/orders_data/order'    
        )
      ),
      'type' => array(
        'target_id' => 'order'
      ), 
      'field_service_date_and_time'  => array ( 0 => array ('value' => $service_date_time)),  
      //'field_assigned_worker_data'  => array ( 0 => array ('value' => $assigned_worker_data)),
      'field_assigned_worker'  => array ( 0 => array ('target_id' => $assigned_worker_id)),  
      'field_order_data'  => array ( 0 => array ('value' => $order_data)),   
      'field_order_state_tax'  => array (
        0 => array ('target_id' => $order_state),
      )
    );

    //set or delete results file
    if(isset($dataRecieved_array['results'])){
      if($dataRecieved_array['results']['file'] === 'delete'){
        $field_results = null;
      } else{
        $field_results = json_encode($dataRecieved_array['results']);
      }
      $node['field_results'] = array ( 0 => array ('value' => $field_results));
    }

    $node = json_encode($node);

    //patch (edit) node
    $ch = curl_init();
    curl_setopt_array($ch, array(
      //CURLOPT_URL => $base_url . '/admin/structure/eck/entity/orders_data/'. $node_id .'?_format=hal_json',
      CURLOPT_URL => $base_url . '/orders_data/'. $node_id .'?_format=hal_json',
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

    //if mail bool on and states is not the same (if results file uploaded for example)
    if($sent_state_change_mail && ($order_state_name_old != $order_state_name)){
      $result['mail'] = emailEditOrderClient('order_status_changed', $client_email, $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    }
    //sent mail to assigned worker
    if($sent_email_assworker && isset($assigned_worker_data)){
      $result['mailAssWorker'] = emailAssWorker('order_status_changed', json_decode($assigned_worker_data), $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    }
    //if order apporved
    if($order_state == '25'){
      $adminSettings = getDashboardAdminSettings();
      emailOrderApprovedAdmin('order_status_changed', $adminSettings['emails'], $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
      //emailOrderApprovedAdmin('order_status_changed', 'abdullah10182@gmail.com', $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    }

    $result = json_encode($result);
    $response = new Response($result);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  public function checkPermission($order_state_name, $order_state_name_old){
    // print_r($order_state_name);
    // print_r($order_state_name_old);
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $is_client = false;
    foreach ($roles as $key => $value) {
      if($value == 'client'){
        $is_client = true;
        break;
      }
    }
    //print_r($is_client);

    if(!$is_client) return true;


    if($order_state_name == 'Order Approved' && $order_state_name_old == 'Order Pending Approval' && $is_client)
      return true;
    else
      return false;
      
  }

}//end class