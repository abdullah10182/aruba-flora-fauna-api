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
class GetOrderListExportExcelData extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_order_list_export_excel_data( Request $request ) {

    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $ass_worker = $request->get('ass_worker');
    $order_status = $request->get('order_status');
    $sort_by = $request->get('sort_by');
    $sort_direction = $request->get('sort_direction');
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
    $orders = $this->query_orders($user_id, $is_client, $start_date, $end_date, $order_status, $ass_worker, $sort_by, $sort_direction);
       
    foreach ($orders as $order) {
      $user = \Drupal\user\Entity\User::load($order->field_client->target_id);
      $assignedWorkerData = $this->build_assigned_worker_data($order->field_assigned_worker->target_id);
      $status_term_name = \Drupal\taxonomy\Entity\Term::load($order->field_order_state_tax->target_id)->get('name')->value;
      $service_date_time_formatted = null;

      if($order->field_service_date_and_time->value){
        $service_date_time_formatted = date("d-M-Y | g:i A", strtotime($order->field_service_date_and_time->value));
      }

      $fieldOrderData = json_decode($order->field_order_data->value);
      
      $response_array[] = [
        'Order Number' => $order->title->value,
        'Created Date and Time' => date("d-M-Y | g:i A", $order->field_created_date->value),
        'Order Status' => $status_term_name,
        'Service Date and Time' => $service_date_time_formatted ? $service_date_time_formatted : '',
        'Assigned Worker Name' => $assignedWorkerData ? $assignedWorkerData['name']: '',
        'Company Name' => $user->field_company_name->value,
        'Company Address' => $user->field_address->value,
        'Order Total Price (Afl.)' => $fieldOrderData->orderData->orderTotalPriceWithTaxes,
        'Invoice Date' => $fieldOrderData->orderData->orderInvoiceDate->formatted ? $fieldOrderData->orderData->orderInvoiceDate->formatted : '',
        'Invoice Due Date' => $fieldOrderData->orderData->orderInvoiceDueDate ? $fieldOrderData->orderData->orderInvoiceDueDate : '',
        'Contact Person' => $user->field_name->value . ' ' .  $user->field_last_name->value,
        'Phone Number' => $user->field_phone_number->value,
        'Email' => $user->mail->value,
      ];
    }

    $response_full = [];
    $response_full['count'] = count($response_array);
    $response_full['data'] = $response_array;
    $response_full['headers'] = array_keys($response_array[0]);

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

  public function query_orders($user_id, $is_client, $start_date, $end_date, $order_status, $ass_worker, $sort_by, $sort_direction){
    $orders = [];
    $query = null;

    if($is_client){
      $ids = \Drupal::entityQuery('orders_data')
        ->condition('field_order_state_tax', '%'.$order_status .'%', 'LIKE')
        ->condition('field_client', $user_id )
        ->sort($sort_by , $sort_direction) 
        ->execute();
      $orders = \Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($ids);
    } 
    else{
      $query = \Drupal::entityQuery('orders_data')
        ->condition('field_created_date',  strtotime($start_date), '>=')
        ->condition('field_created_date',  strtotime($end_date), '<=')
        ->sort($sort_by , $sort_direction);

      if($order_status){
        $and = $query->andConditionGroup();
        $and->condition('field_order_state_tax', $order_status);
        $query->condition($and);
      }
      if($ass_worker){
        $and = $query->andConditionGroup();
        $and->condition('field_assigned_worker', $ass_worker);
        $query->condition($and);
      }
      $ids = $query->execute();

      $orders = \Drupal::entityTypeManager()->getStorage('orders_data')->loadMultiple($ids);
    }
    return $orders;
  }

}//end class

