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
      $response_array[] = [
        'id' => $plant->nid->value,
        'common_name' => $plant->title->value,
        'papiamento_name' => $plant->field_papiamento_name->value,
        'protected_localy' => $protected_locally ? true : false,
        'category' => \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($plant->field_category->target_id)->getName(),
        'short_description' => $plant->field_description_short->value,
        //'custom_dates' => $plant->field_more_info_link->getValue()[0]['uri'],
        'thumbnail' => ImageStyle::load('crop_thumbnail')->buildUrl($plant->field_main_image->entity->getFileUri())
      ];
    }

    $response_full['species'] =$response_array;
    $response_full['count'] = $species['count'];

    $response = new Response(json_encode($response_full));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_flora_species(){
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

}//end class

