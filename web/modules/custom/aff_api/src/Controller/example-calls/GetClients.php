<?php

/**
 * @file
 * Contains \Drupal\aff_api\Controller\GetClients.
 */

namespace Drupal\aff_api\Controller;

use Drupal\Core\Controller\ControllerBase;


use  Drupal\user\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;

/**
 * Controller routines for weblab_dashboard routes.
 */
class GetClients extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_clients( Request $request ) {
    
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $authorized = false;

    foreach ($roles as $key => $value) {
      //print $value;
      if($value == 'dashboard_admin' || $value == 'administrator'){
        $authorized = true;
        break;
      }else {
        $authorized = false;
      }
    }

    if(!$authorized){
      return new Response('permission denied');
    }
    
    $query = $request->get('q');
    //print_r($query);die;
    $response_array= [];

    $ids = \Drupal::entityQuery('user')
    ->condition('status', 1)
    ->condition('roles', 'client')
    ->condition('field_company_name', '%'.$query.'%','LIKE')
    ->execute();
    $users = User::loadMultiple($ids);

    foreach ($users as $user) {
      $response_array[] = [
        'uid' => $user->uid->value,
        'field_company_name' => $user->field_company_name->value
      ];
    }

    //print_r(json_encode(array_values($users)));die;
    $response = new Response(json_encode($response_array));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
    
    // dpm($users);
  }
}//end class

