<?php

namespace Drupal\rcv_slider\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Score entity.
 *
 * @ingroup rcv_slider
 *
 * @ContentEntityType(
 *   id = "score",
 *   label = @Translation("Score"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rcv_slider\ScoreListBuilder",
 *     "views_data" = "Drupal\rcv_slider\Entity\ScoreViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\rcv_slider\Form\ScoreForm",
 *       "add" = "Drupal\rcv_slider\Form\ScoreForm",
 *       "edit" = "Drupal\rcv_slider\Form\ScoreForm",
 *       "delete" = "Drupal\rcv_slider\Form\ScoreDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\rcv_slider\ScoreHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\rcv_slider\ScoreAccessControlHandler",
 *   },
 *   base_table = "score",
 *   translatable = FALSE,
 *   admin_permission = "administer score entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "respondent_id" = "respondent_id",
 *     "poll_id" = "poll_id",
 *     "locate" = "locate",
 *     "ip_address" = "ip_address",
 *     "choice_list" = "choice_list",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/score/{score}",
 *     "add-form" = "/admin/structure/score/add",
 *     "edit-form" = "/admin/structure/score/{score}/edit",
 *     "delete-form" = "/admin/structure/score/{score}/delete",
 *     "collection" = "/admin/structure/score",
 *   },
 *   field_ui_base_route = "score.settings"
 * )
 */
class Score extends ContentEntityBase implements ScoreInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Score entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Score is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['respondent_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Respondent ID'))
      ->setDescription(t('The poll respondent.'));

    $fields['poll_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Poll ID'))
      ->setDescription(t('The poll taken.'));

    $fields['ip_address'] = BaseFieldDefinition::create('string')
      ->setLabel(t('IP Address'))
      ->setDescription(t('Respondent IP address.'));

    $fields['locate'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Location'))
      ->setDescription(t('Rough location of IP address.'));

    $fields['choice_list'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Choice List'))
      ->setDescription(t('Ranked list of choices.'))
      ->setSettings([
        'max_length' => 2048,
      ]);

    return $fields;
  }

}
