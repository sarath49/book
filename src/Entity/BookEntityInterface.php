<?php

namespace Drupal\book\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Book entity entities.
 *
 * @ingroup book
 */
interface BookEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Book entity name.
   *
   * @return string
   *   Name of the Book entity.
   */
  public function getName();

  /**
   * Sets the Book entity name.
   *
   * @param string $name
   *   The Book entity name.
   *
   * @return \Drupal\book\Entity\BookEntityInterface
   *   The called Book entity entity.
   */
  public function setName($name);

  /**
   * Gets the Book entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Book entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Book entity creation timestamp.
   *
   * @param int $timestamp
   *   The Book entity creation timestamp.
   *
   * @return \Drupal\book\Entity\BookEntityInterface
   *   The called Book entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Book entity published status indicator.
   *
   * Unpublished Book entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Book entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Book entity.
   *
   * @param bool $published
   *   TRUE to set this Book entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\book\Entity\BookEntityInterface
   *   The called Book entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Book entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Book entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\book\Entity\BookEntityInterface
   *   The called Book entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Book entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Book entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\book\Entity\BookEntityInterface
   *   The called Book entity entity.
   */
  public function setRevisionUserId($uid);

}
