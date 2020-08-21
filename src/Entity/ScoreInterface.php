<?php

namespace Drupal\rcv_slider\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Score entities.
 *
 * @ingroup rcv_slider
 */
interface ScoreInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Score name.
   *
   * @return string
   *   Name of the Score.
   */
  public function getName();

  /**
   * Sets the Score name.
   *
   * @param string $name
   *   The Score name.
   *
   * @return \Drupal\rcv_slider\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setName($name);

  /**
   * Gets the Score creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Score.
   */
  public function getCreatedTime();

  /**
   * Sets the Score creation timestamp.
   *
   * @param int $timestamp
   *   The Score creation timestamp.
   *
   * @return \Drupal\rcv_slider\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setCreatedTime($timestamp);

}
