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
class GetFloraCategories extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_flora_categories( Request $request ) {
    //query function 
    $categories = $this->query_flora_categories($request);

    foreach ($categories['data'] as $category) {

      $category_image = $this->createImageObject($category->field_category_image);

      $response_array[] = [
        'id' => $category->tid->value,
        'name' => $category->name->value,
        'description' => $category->field_description->value,
        'category_image' => $category_image,
      ];
    }

    $response_full['flora_categories'] =$response_array;
    $response_full['count'] = $categories['count'];

    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_flora_categories($request) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('status',1)
      ->sort('name' , 'ASC')
      ->condition('vid', 'flora_simple_category');
    $ids = $query->execute();
    $categories = [];
    $categories['count'] = count($ids);  
    $categories['data'] =  \Drupal\taxonomy\Entity\Term::loadMultiple($ids); 
  
    return $categories;
  }

  public function createImageObject($image_field) {
    $main_image = new \stdClass();
    $main_image->image_large = null;
    $main_image->image_thumbnail = null;
    $main_image->image_title = null;
    $image_field_array = $image_field->getValue();
    if(count($image_field_array) > 0){      
      $main_image->image_large = ImageStyle::load('large_1920w')->buildUrl($image_field->entity->getFileUri());
      $main_image->image_thumbnail = ImageStyle::load('crop_thumbnail')->buildUrl($image_field->entity->getFileUri());
      $main_image->image_title = $image_field->title;
    }
    
    return $main_image;
  }

}//end class

