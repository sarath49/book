<?php

namespace Drupal\book\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\book\Entity\BookEntityInterface;

/**
 * Class BookEntityController.
 *
 *  Returns responses for Book entity routes.
 */
class BookEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Book entity  revision.
   *
   * @param int $book_entity_revision
   *   The Book entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($book_entity_revision) {
    $book_entity = $this->entityManager()->getStorage('book_entity')->loadRevision($book_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('book_entity');

    return $view_builder->view($book_entity);
  }

  /**
   * Page title callback for a Book entity  revision.
   *
   * @param int $book_entity_revision
   *   The Book entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($book_entity_revision) {
    $book_entity = $this->entityManager()->getStorage('book_entity')->loadRevision($book_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $book_entity->label(), '%date' => format_date($book_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Book entity .
   *
   * @param \Drupal\book\Entity\BookEntityInterface $book_entity
   *   A Book entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BookEntityInterface $book_entity) {
    $account = $this->currentUser();
    $langcode = $book_entity->language()->getId();
    $langname = $book_entity->language()->getName();
    $languages = $book_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $book_entity_storage = $this->entityManager()->getStorage('book_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $book_entity->label()]) : $this->t('Revisions for %title', ['%title' => $book_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all book entity revisions") || $account->hasPermission('administer book entity entities')));
    $delete_permission = (($account->hasPermission("delete all book entity revisions") || $account->hasPermission('administer book entity entities')));

    $rows = [];

    $vids = $book_entity_storage->revisionIds($book_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\book\BookEntityInterface $revision */
      $revision = $book_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $book_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.book_entity.revision', ['book_entity' => $book_entity->id(), 'book_entity_revision' => $vid]));
        }
        else {
          $link = $book_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.book_entity.translation_revert', ['book_entity' => $book_entity->id(), 'book_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.book_entity.revision_revert', ['book_entity' => $book_entity->id(), 'book_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.book_entity.revision_delete', ['book_entity' => $book_entity->id(), 'book_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['book_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
