<?php

namespace Drupal\book;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class BookEntityStorage extends SqlContentEntityStorage implements BookEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BookEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {book_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {book_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BookEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {book_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('book_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
