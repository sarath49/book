<?php

/**
 * @file
 * Contains book_entity.page.inc.
 *
 * Page callback for Book entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Book entity templates.
 *
 * Default template: book_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_book_entity(array &$variables) {
  // Fetch BookEntity Entity Object.
  $book_entity = $variables['elements']['#book_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
