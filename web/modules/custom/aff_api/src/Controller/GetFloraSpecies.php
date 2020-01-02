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
    $species = $this->query_flora_species();

    foreach ($species['data'] as $plant) {
      $protected_locally = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_protected_locally->target_id);
      $category = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_category->target_id);
      $family = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_family->target_id);

      $main_image = $this->createImageObject($plant->field_main_image);
      $additional_images = $this->createMainAdditionalImagesObject($plant);

      $response_array[] = [
        'id' => $plant->nid->value,
        'common_name' => $plant->title->value,
        'papiamento_name' => $plant->field_papiamento_name->value,
        'scientific_name' => $plant->field_scientific_name->value,
        'protected_localy' => $protected_locally ? true : false,
        'category' => $category ? $category->getName() : null,
        'family' => $family ? $family->getName() : null,
        'short_description' => $plant->field_description_short->value,
        'description' => $plant->field_description->value,
        'more_info_link' => $plant->field_more_info_link->getValue()[0]['uri'],
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

  public function query_flora_species() {
    $species = [];
    $query = \Drupal::entityQuery('node')
    ->condition('type','flora')
    ->condition('status',1);
    //$count = $query->count()->execute();
    $ids = $query->execute();
    global $pager_total_items;
    $species['count'] = count($ids);  
    $species['data'] = \Drupal\node\Entity\Node::loadMultiple($ids);  
  
    return $species;
    
  }

  public function createImageObject($image_field) {
    $main_image = new \stdClass();
    $main_image->main_image_large = ImageStyle::load('large_1920w')->buildUrl($image_field->entity->getFileUri());
    $main_image->main_image_thumbnail = ImageStyle::load('crop_thumbnail')->buildUrl($image_field->entity->getFileUri());
    $main_image->title = $image_field->title;
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
      $additional_image->additional_image_large = ImageStyle::load('large_1920w')->buildUrl($file->getFileUri());
      $additional_image->additional_image_thumbnail = ImageStyle::load('crop_thumbnail')->buildUrl($file->getFileUri());
      $additional_image->title = $plant_value['title'];
      array_push($additional_images, $additional_image);
    }

    return $additional_images;
  }

}//end class

