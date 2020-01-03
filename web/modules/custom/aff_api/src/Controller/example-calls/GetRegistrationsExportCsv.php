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
class GetRegistrationsExportCsv extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_registrations_export_csv( Request $request ) {

    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $approved = $request->get('approved');
    $type = $request->get('type');
    $response_array= [];
   
    //query function 
    $registrations = $this->query_registrations_admin($start_date,$end_date,$approved,$type);

    if(count($registrations)==0){
      $response_array['no_data']= true;
      $response = new Response(json_encode($response_array));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }
    
    foreach ($registrations as $registration) {

      $registration_date_formatted = null;
      $dob_formatted = null;

      if($registration->field_tour_date->value){
        $registration_date_formatted = date("d-M-Y",$registration->field_tour_date->value);
      }
      if($registration->field_tour_date->value){
        $dob_formatted = new DrupalDateTime($registration->field_date_of_birth->value);
      }
     
      $response_array[] = [
        'id' => $registration->id->value,
        'type' =>$registration->field_type->value,
        'approved' =>$registration->field_approved->value =='1'? 'Yes':'No',
        'tour_date' => $registration_date_formatted,
        'firstName' => $registration->field_first_name->value,
        'lastName' => $registration->field_last_name->value,
        'date_of_birth' => $dob_formatted->format('d-M-Y'),
        'address' => $registration->field_address->value,
        'phone' => $registration->field_phone->value,
        'email' => $registration->field_email->value,
        'created_date' => date("d-M-Y H:i A", $registration->field_created->value),
      ];
    }

    $tourTypes = $this->queryTourTypeSettings();
    $response_array = $this->addTourTypeInfo($response_array, $tourTypes);

    $response_full = [];
    $response_full['count'] = count($response_array);
    $response_full['data'] = $response_array;
    $response_full['headers'] = array_keys($response_array[0]);



    //print_r($response_full);die;
    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_registrations_admin($start_date,$end_date,$approved,$type){
    $registrations = [];
    $ids = \Drupal::entityQuery('tour_registrations')
      ->condition('type','submissions')
      ->condition('field_tour_date',  strtotime($start_date), '>=')
      ->condition('field_tour_date',  strtotime($end_date), '<=')
      ->sort('field_tour_date' , 'ASC');
    if($approved !== '')
      $ids = $ids->condition('field_approved',  $approved);
    if($type !== '')
      $ids = $ids->condition('field_type',  $type);
    
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
          $response_array[$key]['type'] = $tourType['title'];
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
          $i++;
        }
      }
    }

    //print_r($returnArray);
    return $returnArray;
  }
}//end class

