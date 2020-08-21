<?php

namespace Drupal\rcv_slider\Plugin\Block;


//use Symfony\Component\DependencyInjection\ContainerInterface;
//use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
//use Drupal\Core\Database\Connection;



use Drupal\Core\Block\BlockBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;


/**
 * Provides a Compiled Links Block.
 *
 * @Block(
 *   id = "compile_block",
 *   admin_label = @Translation("Ranked Choice Widget"),
 *   category = @Translation("Social"),
 * )
 */
class SurveyBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {

    $nid = 0;
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();

//      dpm($nid);

      $node = Node::load($nid);
      $candidatelist = $node->get('field_choices')->getValue();

//      dpm($candidatelist);

    }

    return [
      '#markup' => '<div id="root"></div>',
      '#attached' => [
        'library' => [
          'rcv_slider/rcv-slider'
        ],
      'drupalSettings' => [
        'rcv_slider_app' => [
          'reactNID' => $nid,
          'candidatelist' => $candidatelist
        ]
        ]
      ],
    ];

  }//end build()


  public function getCacheMaxAge() {
    return 0;
  }


}//end class
