<?php

namespace Drupal\book;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\book\Entity\BookEntityInterface;

/**
 * Defines the storage handler class for Book entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Book entity entities.
 *
 * @ingroup book
 */
interface BookEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Book entity revision IDs for a specific Book entity.
   *
   * @param \Drupal\book\Entity\BookEntityInterface $entity
   *   The Book entity entity.
   *
   * @return int[]
   *   Book entity revision IDs (in ascending order).
   */
  public function revisionIds(BookEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Book entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Book entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\book\Entity\BookEntityInterface $entity
   *   The Book entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BookEntityInterface $entity);

  /**
   * Unsets the language for all Book entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
