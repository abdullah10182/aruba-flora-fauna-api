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
class GetTourTypes extends ControllerBase {

  private $domain;
  private $prefix;
  private $httaccessAuthEnabled = true;
  private $ssh_enabled = false;

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_tour_types( Request $request ) {
    $lang = $request->get('lang');
    
    //query function 
    $tours = $this->query_tours($lang);

    //print_r(($tours));
    foreach ($tours as $tour) {
      if($tour->getTranslation($lang))
        $tour = $tour->getTranslation($lang);
      $icon_catergory_id = $tour->field_icon->target_id;
      $icon_url;
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($icon_catergory_id);
      $icon_id = $term->field_white_icon->target_id;
      $file = \Drupal\file\Entity\File::load($icon_id);
      $icon_url = file_create_url($file->getFileUri());
      
      $response_array[] = [
        'id' => $tour->id->value,
        'title' => $tour->title->value,
        'field_description' => $tour->field_description->value,
        'instructions' => $tour->field_instructions_text->value,
        'textStep3' => $tour->field_text_step3->value,
        'icon' => $icon_url,
      ];
    }

    $response_full = $response_array;

    //print_r($response_full);die;
    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }


  public function query_tours($lang){
    $ids = \Drupal::entityQuery('tours')
      ->condition('type', 'tour_types')
      ->sort('created')
      ->condition('langcode', $lang)
      ->execute();
    $tour_types = \Drupal::entityTypeManager()->getStorage('tours')->loadMultiple($ids);

    return $tour_types;
  }

}//end class

