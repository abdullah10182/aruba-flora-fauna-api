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
class GetOrders extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_current_orders( Request $request ) {
    $query = $request->get('q');
    $order_status = $request->get('order_status');
    $current_page = $request->get('page');
    $pager_range = $request->get('page_range');
    $response_array= [];
    $ordersCount= null;
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

    $current_pager_offset = $current_page * $pager_range;

    //query function 
    $orders = $this->query_orders($user_id, $is_client, $query, $order_status, $current_pager_offset, $pager_range, $ordersCount);
       
    foreach ($orders as $order) {
      $user = \Drupal\user\Entity\User::load($order->field_client->target_id);
      $assignedWorkerData = $this->build_assigned_worker_data($order->field_assigned_worker->target_id);
      $status_term_name = \Drupal\taxonomy\Entity\Term::load($order->field_order_state_tax->target_id)->get('name')->value;
      $service_date_time_formatted = null;

      if($order->field_service_date_and_time->value){
        $service_date_time_formatted = date("d-M-Y | g:i A", strtotime($order->field_service_date_and_time->value));
      }
      
      $response_array[] = [
        'title' => $order->title->value,
        'field_order_data' => $order->field_order_data->value,
        'assignedWorkerData' => $assignedWorkerData,
        'id' => $order->id->value,
        'field_order_state_tax' => $order->field_order_state_tax->target_id,
        'order_state_name' => $status_term_name,
        'field_created_date' => date("d-M-Y | g:i A", $order->field_created_date->value),
        'service_date_time' => $order->field_service_date_and_time->value,
        'service_date_time_formatted' => $service_date_time_formatted,
        'field_name' => $user->field_name->value,
        'field_last_name' => $user->field_last_name->value,
        'field_company_name' => $user->field_company_name->value,
        'field_address' => $user->field_address->value,
        'field_phone_number' => $user->field_phone_number->value,
        'mail' => $user->mail->value,
      ];
    }

    $response_full = [];
    $response_full['count'] = $ordersCount;
    $response_full['results'] = $response_array;

    //print_r($response_full);die;
    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function build_assigned_worker_data($uid) {
    if(!isset($uid))
      return null;

    $assWorkerData = [];
    $worker = \Drupal\user\Entity\User::load($uid);


    $assWorkerData = [
      'name' => $worker->field_last_name->value . ' ' . $worker->field_name->value,
      'field_phone_number' => $worker->field_phone_number->value,
      'mail' => $worker->mail->value,
      'uid' => $uid
    ];

    return $assWorkerData;  
  }

  public function query_orders($user_id, $is_client, $query, $order_status, $current_pager_offset, $pager_range, &$ordersCount){
    $orders = [];
    // $daysAgo = strtotime('-7 days');
    // $daysAgo2 = strtotime('-5 days');
    if($is_client){
        $idsCount = \Drupal::entityQuery('orders_data')
        ->condition('title', '%'.$query.'%','LIKE')
        ->condition('field_order_state_tax', '%'.$order_status .'%', 'LIKE')
        ->condition('field_client', $user_id )
        ->sort('field_created_date' , 'DESC') 
        ->execute();
      $ordersCount = count(\Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($idsCount));

      $ids = \Drupal::entityQuery('orders_data')
        ->condition('title', '%'.$query.'%','LIKE')
        ->condition('field_order_state_tax', '%'.$order_status .'%', 'LIKE')
        ->condition('field_client', $user_id )
        ->sort('field_created_date' , 'DESC') 
        ->range($current_pager_offset, $pager_range)
        ->execute();
      $orders = \Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($ids);
    } 
    else{
      $idsCount = \Drupal::entityQuery('orders_data')
        ->condition('title', '%'.$query.'%','LIKE')
        ->condition('field_order_state_tax', '%'.$order_status .'%', 'LIKE')
        ->sort('field_created_date' , 'DESC') 
        ->execute();
      $ordersCount = count(\Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($idsCount));

      $ids = \Drupal::entityQuery('orders_data')
        ->condition('title', '%'.$query.'%','LIKE')
        ->condition('field_order_state_tax', '%'.$order_status .'%', 'LIKE')
        ->sort('field_created_date' , 'DESC')
        ->range($current_pager_offset, $pager_range)
        ->execute();
      $orders = \Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($ids);
    }

    return $orders;
  }

}//end class

