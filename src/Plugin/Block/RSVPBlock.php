<?php


namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides an 'RSVP' List Block
 * @Block (
 *   id = "rsvp_block",
 *   admin_label=@Translation("RSVP Block")
 *   )
 */
class RSVPBlock extends BlockBase {

  /**
   * @inheritDoc
   */
  public function build() {
    //    return ['#markup' => $this->t('My RSVP List Block'),];
    return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  public function blockAccess(AccountInterface $account) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = 'a';
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
    }
    /** @var \Drupal\rsvplist\EnablerService $enabler */
    $enabler = \Drupal::service('rsvplist.enabler');
    if (is_numeric($nid)) {
      if ($enabler->isEnabled($node)) {
        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
      }
    }
    return AccessResult::forbidden();
  }

}
