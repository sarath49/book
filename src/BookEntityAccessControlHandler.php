<?php

namespace Drupal\book;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Book entity entity.
 *
 * @see \Drupal\book\Entity\BookEntity.
 */
class BookEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\book\Entity\BookEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished book entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published book entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit book entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete book entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add book entity entities');
  }

}
