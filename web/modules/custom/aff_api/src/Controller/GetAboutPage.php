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
class GetAboutPage extends ControllerBase {

  /**
  * Callback for `my-api/post.json` API method.
  */
  public function get_about_page( Request $request ) {
    //query function 
    $aboutPage = $this->query_about_page();
    $response_array = array();
    $response_array['body'] = array(
      'title' => $aboutPage->get('title')->value,
      'body' => $aboutPage->get('body')->value
    );


    $response = new Response(json_encode($response_array));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function query_about_page() {
    $nid = 65;     // about page id
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $node_storage->load($nid);
    return $node;
  }

}//end class

