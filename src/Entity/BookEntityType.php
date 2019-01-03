<?php

namespace Drupal\book\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Book entity type entity.
 *
 * @ConfigEntityType(
 *   id = "book_entity_type",
 *   label = @Translation("Book entity type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\book\BookEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\book\Form\BookEntityTypeForm",
 *       "edit" = "Drupal\book\Form\BookEntityTypeForm",
 *       "delete" = "Drupal\book\Form\BookEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\book\BookEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "book_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "book_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/book_entity_type/{book_entity_type}",
 *     "add-form" = "/admin/structure/book_entity_type/add",
 *     "edit-form" = "/admin/structure/book_entity_type/{book_entity_type}/edit",
 *     "delete-form" = "/admin/structure/book_entity_type/{book_entity_type}/delete",
 *     "collection" = "/admin/structure/book_entity_type"
 *   }
 * )
 */
class BookEntityType extends ConfigEntityBundleBase implements BookEntityTypeInterface {

  /**
   * The Book entity type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Book entity type label.
   *
   * @var string
   */
  protected $label;

}
