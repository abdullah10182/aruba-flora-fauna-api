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
class GetRegistrationsAdminCalendar extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_registrations_calendar_admin( Request $request ) {
    $start_date = $request->get('start');
    $end_date = $request->get('end');

    $response_array= [];
    $timezone = drupal_get_user_timezone();
    
    // $start_date = date('Y-m-d', $start_date);
    // $end_date = date('Y-m-d', $end_date);

    //query function 
    $registrations = $this->query_registrations_calendar_admin($start_date, $end_date);
 
    foreach ($registrations as $registration) {     
      $response_array[] = [
        'title' => $registration->title->value,
        'id' => $registration->id->value,
        'type' =>$registration->field_type->value,
        'approved' =>$registration->field_approved->value,
        'tour_date' => date("Y-m-d", $registration->field_tour_date->value),
        'people_in_group' => $registration->field_amount_people_group->value,
        // 'firstName' => $registration->field_first_name->value,
        // 'lastName' => $registration->field_last_name->value,
        // 'dob' => $registration->field_date_of_birth->value,
        // 'address' => $registration->field_address->value,
        // 'phone' => $registration->field_phone->value,
        // 'email' => $registration->field_email->value,
      ];
    }
  
    //$response_full = $this->buildResponseArray($response_array);
    $tourTypes = $this->queryTourTypeSettings();
    $response_array = $this->addTourTypeInfo($response_array, $tourTypes);
    $response_array = $this->buildResponseArray($response_array);
    $response = new Response(json_encode($response_array));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function get_registrations_admin( Request $request ) {
    //$date = $request->get('date');

    $response_array= [];
    
    //print_r($request->get('approved'));

    //query function 
    $registrations = $this->query_registrations_admin($request);
 
    foreach ($registrations as $registration) {

      $registration_date_formatted = null;

      if($registration->field_tour_date->value){
        $registration_date_formatted = date("d-M-Y",$registration->field_tour_date->value);
      }
     
      $response_array[] = [
        'title' => $registration->title->value,
        'id' => $registration->id->value,
        'type' =>$registration->field_type->value,
        'approved' =>$registration->field_approved->value,
        'tour_date' => $registration->field_tour_date->value,
        'tour_date_display' => $registration_date_formatted,
        'firstName' => $registration->field_first_name->value,
        'lastName' => $registration->field_last_name->value,
        'dob' => $registration->field_date_of_birth->value,
        'address' => $registration->field_address->value,
        'phone' => $registration->field_phone->value,
        'email' => $registration->field_email->value,
        'people_in_group' => $registration->field_amount_people_group->value,
        'lang' => $registration->field_language->value,
        'created_date' => $registration->field_created->value,
        'created_date_display' => date("d-M-Y H:i A", $registration->field_created->value),
      ];
    }
  
    //$response_full = $this->buildResponseArray($response_array);
    $tourTypes = $this->queryTourTypeSettings();
    $response_array = $this->addTourTypeInfo($response_array, $tourTypes);
    $response = new Response(json_encode($response_array));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_registrations_calendar_admin($start_date, $end_date){
    $orders = [];
    $ids = \Drupal::entityQuery('tour_registrations')
      ->condition('field_tour_date',  $start_date, '>=')
      ->condition('field_tour_date',  $end_date, '<=')
      ->condition('type','submissions')
      ->sort('field_tour_date' , 'DESC')
      ->execute();
    $orders = \Drupal::entityTypeManager()->getStorage('tour_registrations')->loadMultiple($ids);  
  
    return $orders;
  }

  public function query_registrations_admin($request){
    $registrations = [];
    $ids = \Drupal::entityQuery('tour_registrations')
      ->condition('type','submissions')
      ->sort('field_tour_date' , 'ASC');
    if($request->get('date') !== 'null')
      $ids = $ids->condition('field_tour_date', $request->get('date'), '=');
    if($request->get('approved') !== 'null')
      $ids = $ids->condition('field_approved',  $request->get('approved'));
    
    $ids = $ids->execute();
    $registrations = \Drupal::entityTypeManager()->getStorage('tour_registrations')->loadMultiple($ids);  
    return $registrations;
  }

  public function queryTourTypeSettings(){
    $ids = \Drupal::entityQuery('tours')
      ->condition('type','tour_types')
      ->execute();
    $tourTypes = \Drupal::entityTypeManager()->getStorage('tours')->loadMultiple($ids);  
    $response_array = [];
    foreach ($tourTypes as $tourType) {
      $response_array[] = [
        'title' => $tourType->title->value,
        'id' => $tourType->id->value,
      ];
    }
    return $response_array;
  }

  public function addTourTypeInfo($response_array,$tourTypes){
    foreach ($response_array as $key => $item) {
      foreach ($tourTypes as $tourType) {
        if($item['type'] == $tourType['id']){
          $response_array[$key]['typeTitle'] = $tourType['title'];
          break;
        }
      }
    }
    return $response_array;
  }

  public function buildResponseArray($response_array){

    $groupedPerDate = [];
    $groupedPerType = [];
    $returnArray= [];

    //group per date
    foreach ( $response_array as $value ) {
        $groupedPerDate[$value['tour_date']][] = $value;
    }
    
    foreach ($groupedPerDate as $key => $groupedDate) {
      //group per tour type
      foreach ( $groupedDate as $typeGroup ) {
        //group per approved
        if($typeGroup['approved'])
            $groupedPerType[$key][$typeGroup['type']]['approved'][] = $typeGroup;
        else
            $groupedPerType[$key][$typeGroup['type']]['notApproved'][] = $typeGroup;
      }
    }
    $i = 0;
    foreach ($groupedPerType as $date => $typeGroup) {
      foreach ($typeGroup as $type => $approvedGroup) {
        foreach ($approvedGroup as $key => $value) {
          // print_r($value[0]['tour_date']);
          // print('---');
          // print_r($value[0]['approved']);
          // print('---');
          $returnArray[$i]['count'] = count($value);
          $returnArray[$i]['date'] = $value[0]['tour_date'];
          $returnArray[$i]['approved'] = $value[0]['approved'];
          $returnArray[$i]['type'] = $value[0]['type'];
          $returnArray[$i]['typeTitle'] = $value[0]['typeTitle'];
          if($value[0]['type'] == '2')
            $returnArray[$i]['people_in_group'] = $value[0]['people_in_group'];
          $i++;
        }
      }
    }

    //print_r($returnArray);
    return $returnArray;
  }
}//end class

