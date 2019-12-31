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

/**
 * Controller routines for weblab_dashboard routes.
 */
class Packages extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
   * Callback for `my-api/post.json` API method.
   */
  public function _create_order_post( Request $request ) {
    //get request json from javascript
    $data = json_decode( $request->getContent(), FALSE );

    //print_r($data);die;
    
    $base_url = $this->getBaseUrl();

    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); 
    $company = $user->get('field_company_name')->value;

    $now = date("Y-m-d_His");
    $order_name = 'order-'.$company.'-'.$now;

    $session_manager = \Drupal::service('session_manager');
    $session_id = $session_manager->getId();
    $session_name = $session_manager->getName();

   
    $ch1 = curl_init();
    curl_setopt_array($ch1, array(
      CURLOPT_URL => $base_url . '/rest/session/token',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_HTTPHEADER => array(
            'Accept: application/hal+json',
            'Cookie: '.$session_name.'='.$session_id,
        ),
    ));
    $token=curl_exec($ch1);
    //print_r($token);die;

    $node = array(
      '_links' => array(
        'type' => array(
          'href' => $this->getBaseUrl(false).'/rest/type/orders/orders'
        )
      ),
      'field_order_type' => array (0 => array ('value' => $data->type)),
      'field_message' => array (0 => array ('value' => $data->message)),
      'field_standard_tests' => array (
        0 => array ('target_id' => '18'), 
        1 => array ('target_id' => '19'), 
        2 => array ('target_id' => '20'), 
        3 => array ('target_id' => '21'), 
      ),
      'field_extra_tests' => array (
        0 => array ('target_id' => '22'), 
      ),
    );
    $data = json_encode($node);
    //die($data);
   
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $base_url . '/entity/orders',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-type: application/hal+json',
        'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
        ),
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER =>true,
      CURLOPT_POSTFIELDS => $data,
    ));
    $result=curl_exec($ch);
    $result = json_decode($result,true);
    

    //email test
    $mailManager = \Drupal::service('plugin.manager.mail');
    $key = 'order_save';
    $to = \Drupal::currentUser()->getEmail();
    $params['message'] ='message';
    $params = array(
      'subject' => 'Order proccessing',
      'body' => "<h1>Order is proccessing</h1><img src='http://webaruba.com/sites/all/themes/web/img/logo/logo-web-nv-85.png' alt='Home'>",
    );
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $mail_result = $mailManager->mail('weblab_dashboard', $key, $to, $langcode, $params, NULL, $send);

    if ($mail_result['result'] !== true) {
     // drupal_set_message(t($this->getOwner()->mail->value.' There was a problem sending your message and it was not sent.'), 'error');
     $result['mail'] = "mailn not sent";
    }
    else {
     // drupal_set_message(t('Your message has been sent.'));
     $result['mail'] = "success mail sent";
    }

    $result = json_encode($result);
    $response = new Response($result);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

    /**
   * Callback for `my-api/post.json` API method.
   */
  public function get_packages( Request $request ) {

    $base_url = $this->getBaseUrl();
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
      CURLOPT_URL => $base_url.'/api/packages',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/hal+json',
        //'Content-type: application/hal+json',
        //'X-CSRF-Token: '.$token,
        'Cookie: '.$session_name.'='.$session_id,
        ),
      CURLOPT_RETURNTRANSFER =>true,
    ));
    $result=curl_exec($ch);
    print_r($result);die;
    return new JsonResponse( $result );
  }

  /**
   * Callback for `my-api/delete.json` API method.
   */
  public function delete_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'DELETE';

    return new JsonResponse( $response );
  }

  public function getBaseUrl($auth=true){

    $httaccessAuth= '';
    
    $this->domain = $_SERVER['HTTP_HOST'];
    //https enabled
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' && $ssh_enabled) { 
      $this->prefix = 'https://';
    }else
      $this->prefix = 'http://'; 
    
 
    if($this->httaccessAuthEnabled && $this->domain == 'lab.webaruba.com'){
      $httaccessAuth = 'admin:webaruba123@';
    }

    if($auth){
      $base_url = $this->prefix . $httaccessAuth . $this->domain;
     }else {
        $base_url = $this->prefix . $this->domain;
     }
     
    return $base_url;
  }

}//end class