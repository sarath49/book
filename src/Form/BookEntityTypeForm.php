<?php

namespace Drupal\book\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BookEntityTypeForm.
 */
class BookEntityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $book_entity_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $book_entity_type->label(),
      '#description' => $this->t("Label for the Book entity type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $book_entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\book\Entity\BookEntityType::load',
      ],
      '#disabled' => !$book_entity_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $book_entity_type = $this->entity;
    $status = $book_entity_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Book entity type.', [
          '%label' => $book_entity_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Book entity type.', [
          '%label' => $book_entity_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($book_entity_type->toUrl('collection'));
  }

}
