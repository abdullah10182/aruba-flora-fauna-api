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
class GetRegistrationsCalendar extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_registrations_calendar( Request $request ) {
    $start_date = $request->get('start');
    $end_date = $request->get('end');

    
    $response_array= [];
    $timezone = drupal_get_user_timezone();
    
    // $start_date = date('Y-m-d', $start_date);
    // $end_date = date('Y-m-d', $end_date);

    //query function 
    $registrations = $this->query_registrations($start_date, $end_date);
 
    foreach ($registrations as $registration) {

      $registration_date_formatted = null;

      if($registration->field_tour_date->value){
        $registration_date_formatted = date("d-M-Y | g:i A", strtotime($registration->field_tour_date->value));
      }
     
      $response_array[] = [
        'title' => $registration->title->value,
        'id' => $registration->id->value,
        'type' => $registration->field_type->value,
        'tour_date' => date("Y-m-d", $registration->field_tour_date->value),
        'people_in_group' => $registration->field_amount_people_group->value,
      ];
    }

  
    $response_full = $this->buildResponseArray($response_array);

    //print_r($response_full);die;
    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_registrations($start_date, $end_date){
    $orders = [];
    $ids = \Drupal::entityQuery('tour_registrations')
      ->condition('field_tour_date',  $start_date, '>=')
      ->condition('field_tour_date',  $end_date, '<=')
      ->condition('type','submissions')
      ->sort('field_tour_date' , 'DESC')
      ->execute();
    $orders = \Drupal::entityTypeManager()->getStorage('tour_registrations')->loadMultiple($ids);  
  
    //print_r($orders);
    return $orders;
  }

  public function buildResponseArray($response_array){

    $groupDate = [];
    $returnArray= [];
    //group by date
    foreach ( $response_array as $value ) {
        $groupDate[$value['tour_date']][] = $value;
    }
    $groupDate = $this->checkForDifferentTourTypeSameDay($groupDate);
    $i = 0;
    foreach ($groupDate as $key => $value) {
      //print_r($value);
      $returnArray[$i]['date'] = $key;
      $returnArray[$i]['type'] = $value[0]['type'];
      $returnArray[$i]['values'] = $value[0]['count'];
      $i++;
    }

    return $returnArray;
  }

  public function checkForDifferentTourTypeSameDay($groupDate){
    //loop trough each date, if multiple check for mixed types, else count is 1
    foreach ($groupDate as $key => $group) {

      //check if mixed types on same date
      foreach ($group as $key2 => $registration) {
        if($registration['type'] == '2'){
          //mixed type found
          $group = $this->setCountValues($group, $registration);
          $groupDate[$key] = $group;
          break;
        }else{
          $groupDate[$key][$key2]['count']= count($group);
        }
      }
      
    }
    return $groupDate;
  }

  public function setCountValues($group, $registration){
    //set count from all on this date to match the sum of people of both event types
    $count = $registration['people_in_group'] + count($group) - 1;
    foreach ($group as $key => $registration) {
      $group[$key]['count'] = $count;
    }

    return $group;
  }

}//end class

