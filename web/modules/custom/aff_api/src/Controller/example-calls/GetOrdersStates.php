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
class GetOrdersStates extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_current_orders_states( Request $request ) {

    $order_status = $request->get('order_status');

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
    $orders = $this->query_orders($user_id, $is_client, $order_status);
       
    foreach ($orders as $order){
      $status_term_name = \Drupal\taxonomy\Entity\Term::load($order->field_order_state_tax->target_id)->get('name')->value;      
      $ordersData[]=[
         'stateId' => $order->field_order_state_tax->target_id,
         'stateName' => $status_term_name
      ];
    }
    foreach ($orders as $order){
      $statesCount[]= $order->field_order_state_tax->target_id;
    }
    $statesCount= array_count_values($statesCount);

    // print_r($statesCount);
    // print_r($ordersData);
    $adminStates = [23,25];
    $clientStates = [24,27,28,29];

    $response_array = [];
    foreach ($statesCount as $key1 => $count) {
      foreach ($ordersData as $key2 => $order) {
        if($key1 == $order['stateId']){
          if($is_client){
            if(!in_array($order['stateId'],$clientStates))
              break;
          }
          else{
            if(!in_array($order['stateId'],$adminStates))
              break;
          }
          $response_array[] = [
            'count' => $count,
            'stateName' => $order['stateName'],
            'id' => $order['stateId'],
          ];
          break;
        }
      }
    }

    $response_full= $response_array;

    //print_r($response_full);die;
    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }


  public function query_orders($user_id, $is_client, $order_status){
    $orders = [];
    if($is_client){
      $ids = \Drupal::entityQuery('orders_data')
        ->condition('field_order_state_tax', [23, 24, 25, 28, 29], 'IN')
        ->condition('field_client', $user_id )
        ->execute();
      $orders = \Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($ids);
    } 
    else{
      $ids = \Drupal::entityQuery('orders_data')
        ->condition('field_order_state_tax', [23, 24, 25, 28, 29], 'IN')
        ->execute();
      $orders = \Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($ids);
    }

    return $orders;
  }

}//end class