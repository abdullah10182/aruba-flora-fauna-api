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

/**
 * Controller routines for weblab_dashboard routes.
 */
class CreateOrder extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function create_order_post( Request $request ) {
    $dataRecieved = $request->getContent();
    $base_url = getBaseUrl();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $user_id =  $user->get('uid')->value;
    $roles =  $user->getRoles();
    $now = date("Y-m-d\TH:i:s");
    $now = strtotime($now);
    $month = zeroFill(date('m'),2);
    $day = zeroFill(date('d'),2);
    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
    $node_id = '';
    $order_number = '';
    $dataRecieved_array = json_decode($dataRecieved,true);
    $selectedClientId = $dataRecieved_array['orderData']['selectedClientId'];
    $client_id = checkForSelectedClientId($selectedClientId, $roles, $user_id);
    $client_email = null;

    if($selectedClientId){
      $user = \Drupal\user\Entity\User::load($selectedClientId);
      $client_email = $user->getEmail();
    }else{
      $client_email = $user->get('mail')->value;
    }

    $company_name = $user->get('field_company_name')->value;
    $company_address = $user->get('field_address')->value;
    $company_phone = $user->get('field_phone_number')->value;
    $company_first_name = $user->get('field_name')->value;
    $company_last_name = $user->get('field_last_name')->value;

    $order_name = 'order-'.$company_name.'-'.$now;

    //print_r($base_url);die;

    //get token
    $token = getToken($base_url, $session_name, $session_id);

    //post node object
    $node = array(
      '_links' => array(
        'type' => array(
          'href' => getBaseUrl(false).'/rest/type/orders_data/order'
        )
      ),
      'type' => array(
        'target_id' => 'order'
      ),      
      'title' => array (0 => array ('value' => $order_name )),
      'field_order_data' => array (0 => array ('value' => $dataRecieved)),
      'field_created_date'  => array ( 0 => array ('value' => $now)),
      //'field_service_date_time_1'  => array ( 0 => array ('value' => '')),
      'field_client'  => array (
        0 => array ('target_id' => $client_id), 
      ),
      'field_order_state_tax'  => array (
        0 => array ('target_id' => '23'),//order created taxonomy
      ),
    );
    $node = json_encode($node);
   
    //post order
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url . '/entity/orders_data'. $node_id .'?_format=hal_json',
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
    $node_id = $result['id'][0]['value'];

    // print_r($result);
    // die;

    $order_number = '1600-' . $day . $month . '-' . zeroFill($node_id, 4);

    //patch order number object
    $node = array(
      '_links' => array(
        'type' => array(
          'href' => getBaseUrl(false).'/rest/type/orders_data/order'    
        )
      ),
      'type' => array(
        'target_id' => 'order'
      ),
      'title' => array (0 => array ('value' => $order_number)),    
    );
    $node = json_encode($node);

    //patch order number
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

    //add data for email
    $result = json_decode($result,true);
    $dataRecieved_array['results'] = $result;
    $dataRecieved_array['user_data']['company_name'] = $company_name;
    $dataRecieved_array['user_data']['company_address'] = $company_address;
    $dataRecieved_array['user_data']['company_phone'] = $company_phone;
    $dataRecieved_array['user_data']['company_mail'] = $client_email;
    $dataRecieved_array['user_data']['contact_person'] = $company_first_name . ' ' . $company_last_name;
    
    //sent emails
    $result['mail'] = emailCreateOrderClient('order_status_changed', $client_email, $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    $adminSettings = getDashboardAdminSettings();
    emailCreateOrderAdmin('order_status_changed', $adminSettings['emails'], $dataRecieved_array, \Drupal::service('plugin.manager.mail'));
    
    $result = json_encode($result);
    $response = new Response($result);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

}//end class