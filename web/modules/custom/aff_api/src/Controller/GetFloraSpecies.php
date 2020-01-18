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
use Drupal\image\Entity\ImageStyle;

/**
 * Controller routines for weblab_dashboard routes.
 */
class GetFloraSpecies extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_flora_species( Request $request ) {
    //query function 
    $species = $this->query_flora_species($request );
    $response_array = array();

    foreach ($species['data'] as $plant) {
      $protected_locally = null;
      $category = null;
      $family = null;
      $status = null;
      if(isset($plant->field_protected_locally->target_id))
        $protected_locally = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_protected_locally->target_id);
      if(isset($plant->field_category->target_id))
        $category = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_category->target_id);
      if(isset($plant->field_family->target_id))
        $family = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_family->target_id);
      if(isset($plant->field_status->target_id))
        $status = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_status->target_id);

      $main_image = $this->createImageObject($plant->field_main_image);
      $additional_images = $this->createMainAdditionalImagesObject($plant);

      $response_array[] = [
        'id' => $plant->nid->value,
        'common_name' => $plant->title->value,
        'papiamento_name' => $plant->field_papiamento_name->value,
        'scientific_name' => $plant->field_scientific_name->value,
        'protected_locally' => $protected_locally ? true : false,
        'category_id' => $plant->field_category->target_id,
        'category_name' => $category ? $category->getName() : null,
        'status_id' => $plant->field_status->target_id,
        'status_name' => $status ? $status->getName() : null,
        'family' => $family ? $family->getName() : null,
        'short_description' => $plant->field_description_short->value,
        'description' => $plant->field_description->value,
        'more_info_link' => $plant->field_more_info_link->getValue() ? $plant->field_more_info_link->getValue()[0]['uri'] : null,
        'main_image' => $main_image,
        'additional_images' => $additional_images
      ];
    }

    $response_full['species'] =$response_array;
    $response_full['count'] = $species['count'];

    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function get_flora_species_search_suggestions( Request $request ) {
    //query function 
    $species = $this->query_flora_species($request);
    $response_array = array();

    foreach ($species['data'] as $plant) {
      $main_image = $this->createImageObject($plant->field_main_image);
      $additional_images = $this->createMainAdditionalImagesObject($plant);
      $category = null;

      if(isset($plant->field_category->target_id))
        $category = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_category->target_id);

      $response_array[] = [
        'id' => $plant->nid->value,
        'common_name' => $plant->title->value,
        'papiamento_name' => $plant->field_papiamento_name->value,
        'category_name' => $category ? $category->getName() : null
      ];
    }

    $response_full['species'] =$response_array;
    $response_full['count'] = $species['count'];

    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_flora_species($request) {
    $species = [];
    $query = \Drupal::entityQuery('node')
    ->condition('type','flora')
    ->condition('status',1);
    if($request->get('sort_by'))
      $query->sort($request->get('sort_by'), 'DESC');
    else
      $query->sort('title' , 'ASC');
    if($request->get('category') !== null)
      $query = $query->condition('field_category', $request->get('category'), '=');
    if($request->get('species_id') !== null)
      $query = $query->condition('nid', $request->get('species_id'), '=');
    if($request->get('q') !== null) {
      $q = $request->get('q');
      $group = $query->orConditionGroup()
        ->condition('title', $q, 'CONTAINS')
        ->condition('field_scientific_name', $q, 'CONTAINS')
        ->condition('field_description_short', $q, 'CONTAINS')
        ->condition('field_status.entity.name', $q, 'CONTAINS')
        ->condition('field_category.entity.name', $q, 'CONTAINS')
        ->condition('field_family.entity.name', $q, 'CONTAINS')
        ->condition('field_papiamento_name', $q, 'CONTAINS');
        $ids = $query->condition($group)->execute();
    } else {
      $ids = $query->execute();
    }
    //$count = $query->count()->execute();
    $species['count'] = count($ids);  
    $species['data'] = \Drupal\node\Entity\Node::loadMultiple($ids);  
  
    return $species;
  }

  public function createImageObject($image_field) {
    $main_image = new \stdClass();
    if($image_field->getValue()) {      
      $main_image->image_large = ImageStyle::load('large_1920w')->buildUrl($image_field->entity->getFileUri());
      $main_image->image_medium = ImageStyle::load('medium_960_x_auto')->buildUrl($image_field->entity->getFileUri());
      $main_image->image_thumbnail = ImageStyle::load('crop_thumbnail')->buildUrl($image_field->entity->getFileUri());
      $main_image->image_small = ImageStyle::load('small')->buildUrl($image_field->entity->getFileUri());
      $main_image->image_title = $image_field->title;
    }
    return $main_image;
  }

  public function createMainAdditionalImagesObject($plant) {
    if(!$plant->field_additional_images->getValue()) {
      return [];
    }
    $additional_images = [];
    $additional_images_fields = $plant->field_additional_images->getValue();

    foreach ($additional_images_fields as $key => $plant_value) {
      $file = \Drupal\file\Entity\File::load($plant_value['target_id']);
      $additional_image = new \stdClass(); 
      $additional_image->image_large = ImageStyle::load('large_1920w')->buildUrl($file->getFileUri());
      $additional_image->image_medium = ImageStyle::load('medium_960_x_auto')->buildUrl($file->getFileUri());
      $additional_image->image_thumbnail = ImageStyle::load('crop_thumbnail')->buildUrl($file->getFileUri());
      $additional_image->image_small = ImageStyle::load('small')->buildUrl($file->getFileUri());
      $additional_image->image_title = $plant_value['title'];
      array_push($additional_images, $additional_image);
    }

    return $additional_images;
  }

}//end class

