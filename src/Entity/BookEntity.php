<?php

namespace Drupal\book\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Book entity entity.
 *
 * @ingroup book
 *
 * @ContentEntityType(
 *   id = "book_entity",
 *   label = @Translation("Book entity"),
 *   bundle_label = @Translation("Book entity type"),
 *   handlers = {
 *     "storage" = "Drupal\book\BookEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\book\BookEntityListBuilder",
 *     "views_data" = "Drupal\book\Entity\BookEntityViewsData",
 *     "translation" = "Drupal\book\BookEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\book\Form\BookEntityForm",
 *       "add" = "Drupal\book\Form\BookEntityForm",
 *       "edit" = "Drupal\book\Form\BookEntityForm",
 *       "delete" = "Drupal\book\Form\BookEntityDeleteForm",
 *     },
 *     "access" = "Drupal\book\BookEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\book\BookEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "book_entity",
 *   data_table = "book_entity_field_data",
 *   revision_table = "book_entity_revision",
 *   revision_data_table = "book_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer book entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/book_entity/{book_entity}",
 *     "add-page" = "/admin/structure/book_entity/add",
 *     "add-form" = "/admin/structure/book_entity/add/{book_entity_type}",
 *     "edit-form" = "/admin/structure/book_entity/{book_entity}/edit",
 *     "delete-form" = "/admin/structure/book_entity/{book_entity}/delete",
 *     "version-history" = "/admin/structure/book_entity/{book_entity}/revisions",
 *     "revision" = "/admin/structure/book_entity/{book_entity}/revisions/{book_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/book_entity/{book_entity}/revisions/{book_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/book_entity/{book_entity}/revisions/{book_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/book_entity/{book_entity}/revisions/{book_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/book_entity",
 *   },
 *   bundle_entity_type = "book_entity_type",
 *   field_ui_base_route = "entity.book_entity_type.edit_form"
 * )
 */
class BookEntity extends RevisionableContentEntityBase implements BookEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the book_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

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
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Book entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Book entity entity.'))
      ->setRevisionable(TRUE)
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

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Book entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
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

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

      $fields['isbn'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ISBN'))
      ->setDescription(t('ISBN of the Book'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'settings' => array(
          'display_label' => TRUE,
        ),
      ))
     ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);    
     
    $fields['author'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Author'))
      ->setDescription(t('Author(s) of the book'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'settings' => array(
          'display_label' => TRUE,
        ),
      ))
     ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);
 
 
    $fields['price'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Price'))
      ->setDescription(t('Price of the Book'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'settings' => array(
          'display_label' => TRUE,
        ),
      ))
     ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);

    return $fields;
  }

}
