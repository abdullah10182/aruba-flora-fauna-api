<?php

/**
 * @file
 * Contains \Drupal\weblab_dashboard\Controller\TestAPIController.
 */

namespace Drupal\weblab_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Utility\Html;

/**
 * Controller routines for weblab_dashboard routes.
 */
class TestAPIController extends ControllerBase {

  /**
   * Callback for `my-api/get.json` API method.
   */
  public function get_example( Request $request ) {
    die('deafffff');
    $id = $request->query->get('id');

    $query = \Drupal::database()->select('test_table', 'tt')
    ->fields('tt',['test_text'])
    ->condition('tt.id', 1)
    ->range(0, 1);

    $result = $query->execute()->fetchField();

    $query2 = \Drupal::database()->insert('test_table');
    $query2->fields([
      'test_text',
      'test_int'
    ]);
    $query2->values([
      'My event',
      4
    ]);
    $result2 =  $query2->execute();

     //print_r($result);
    // die;

    $result = Html::escape($result);
    print_r($result);

    return new JsonResponse( $result );
  }

  /**
   * Callback for `my-api/put.json` API method.
   */
  public function put_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'PUT';

    return new JsonResponse( $response );
  }

  /**
   * Callback for `my-api/post.json` API method.
   */
  public function post_example( Request $request ) {
   // /entity/orders?_format=hal_json
    //$user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    
    $comapny = $user->get('field_comany')->value;

    $now = date("Y-m-d_His");
    $order_name = 'order-'.$comapny.'-'.$now;

    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
   
    $ch1 = curl_init();
    curl_setopt_array($ch1, array(
      CURLOPT_URL => 'web-lab-copy/rest/session/token',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_HTTPHEADER => array(
            'Accept: application/hal+json',
            'Cookie: '.$session_name.'='.$session_id,
        ),
      //CURLOPT_CUSTOMREQUEST => 'POST',
      //CURLOPT_POST => true,
    ));
    $token=curl_exec($ch1);
   // die;
    //print_r($token);die;
    //sleep(500);


    //print_r($session_id);die;
    $node = array(
      '_links' => array(
        'type' => array(
          'href' => 'http://web-lab-copy/rest/type/orders/orders'
        )
      ),
        'name' => array (0 => array ('value' => $order_name)),
        'field_test_text' => 
        array (
          0 => 
          array (
            'value' => 'anotkjljkljkljkljkljkluh again',
          ),
          1 => 
          array (
            'value' => 'woop woop',
          ),
        ),
        'field_time' => 
        array (
          0 => 
          array (
            'value' => '1994-04-12T08:00:00',
          ),
        ),
        'field_user' => 
        array (
          0 => 
          array (
            'target_id' => '6',
          ),
        ),
    );
    $data = json_encode($node);
   
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => 'web-lab-copy/entity/orders',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/hal+json',
        'Content-type: application/hal+json',
        'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
        ),
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      //CURLOPT_COOKIESESSION=> true,
     // CURLOPT_COOKIE=> 'SESS780a815d5221fca43b4c37b923c285eb=CUFodODDvUqWjf_ffd1U8iambc4xLL_SwragFiTSZxk'
      //CURLOPT_HTTPAUTH => CURLAUTH_COOKIE,
      //CURLOPT_USERPWD => 'admin:1q2w3e',
    ));
    $result=curl_exec($ch);
   // print $result;
    //return;

    
    //$user = \Drupal::currentUser()->getRoles();

    // if(!in_array("administrator", $user))  {
    //   $response['role'] = false;
    //   $response['text'] = 'not logged in bro';
    //   return new JsonResponse( $response );
    // }else{
      // This condition checks the `Content-type` and makes sure to 
      // decode JSON string from the request body into array.
      // if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      //   $data = json_decode( $request->getContent(), TRUE );
      //   $request->request->replace( is_array( $data ) ? $data : [] );
      // }

      // $query = \Drupal::database()->select('node_field_data', 'nfd');
      // $query->fields('nfd', ['title']);
      // $result = $query->execute()->fetchAllKeyed(0,0);

      return new JsonResponse( $result );
    //  }
  }

  /**
   * Callback for `my-api/delete.json` API method.
   */
  public function delete_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'DELETE';

    return new JsonResponse( $response );
  }

    /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_current_orders( Request $request ) {
    die('test');

    $base_url = getBaseUrl();
    //print_r($base_url);

    $data = json_decode( $request->getContent(), FALSE );

    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $company = $user->get('field_company_name')->value;

    $now = date("Y-m-d_His");
    $order_name = 'order-'.$company.'-'.$now;

    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();
   
    $ch1 = curl_init();
    curl_setopt_array($ch1, array(
      CURLOPT_URL => $base_url.'/rest/session/token',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_HTTPHEADER => array(
            'Accept: application/hal+json',
            'Cookie: '.$session_name.'='.$session_id,
        ),
    ));
    $token=curl_exec($ch1);
   
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url.'/orders-api/orders',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/hal+json',
        //'Content-type: application/hal+json',
        //'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
        ),
      CURLOPT_RETURNTRANSFER =>true,
    ));
    $result=curl_exec($ch);
    //print_r($result);die;
    return new JsonResponse( $result );
  }

}
