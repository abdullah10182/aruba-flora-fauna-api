<?php

namespace Drupal\aff_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'React ready' Block
 *
 * @Block(
 *   id = "dashboard_main_react_id",
 *   admin_label = @Translation("DashboardReactBlock main block"),
 * )
 */
class DashboardReactBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $build = array(
      '#markup' => '<div id="dashboard_main_react_id">Loading form...</div>',
    );

    // Add the example library.
    $build['#attached']['library'][] = 'aff_api/main-bundle-app';

    return $build;
  }
}