<?php


namespace Drupal\rsvplist;


use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class EnablerService {

  /**
   * EnablerService constructor.
   */
  public function __construct() {
  }

  /**
   * Sets a individual node to be RSVP enabled.
   *
   * @param \Drupal\node\Entity\Node $node
   */
  public function setEnabled(Node $node) {
    if (!$this->isEnabled($node)) {
      $insert = Database::getConnection()->insert('rsvplist_enabled');
      $insert->fields(['nid'], [$node->id()]);
      $insert->execute();
    }
  }

  /**
   * @param \Drupal\node\Entity\Node $node
   *
   * @return bool
   */
  public function isEnabled(Node $node) {
    if ($node->isNew()) {
      return FALSE;
    }
    $select = Database::getConnection()->select('rsvplist_enabled', 're');
    $select->fields('re', ['nid']);
    $select->condition('nid', $node->id());
    $results = $select->execute();
    return !empty($results->fetchCol());
  }

  /**
   * @param \Drupal\node\Entity\Node $node
   */
  public function delEnabled(Node $node) {
    $delete = Database::getConnection()->delete('rsvplist_enabled');
    $delete->condition('nid', $node->id());
    $delete->execute();
  }

}
