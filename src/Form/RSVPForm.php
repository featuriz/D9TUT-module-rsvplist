<?php


namespace Drupal\rsvplist\Form;


use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

class RSVPForm extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = 0;
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
    }
    $form['email'] = [
      '#title' => t('Email Address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t('We\'ll send updated to the email address your provide.'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if ($value == !\Drupal::service('email.validator')->isValid($value)) {
      $form_state->setErrorByName('email', t('The email address %mail is not valid.', ['%mail' => $value]));
      return;
    }
    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    // Check if email already is set for this node
    $select = Database::getConnection()->select('rsvplist', 'r');
    $select->fields('r', ['nid']);
    $select->condition('nid', $node->id());
    $select->condition('mail', $value);
    $results = $select->execute();
    if (!empty($results->fetchCol())) {
      // We found a row with this nid and email.
      $form_state->setErrorByName('email', t('The address %mail is already subscribed to this list.', ['%mail' => $value]));
    }

  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //    $this->messenger()->addMessage(t('The form is working.'));
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    \Drupal::database()->insert('rsvplist')
      ->fields([
        'mail' => $form_state->getValue('email'),
        'nid' => $form_state->getValue('nid'),
        'uid' => $user->id(),
        'created' => time(),
      ])
      ->execute();
    $this->messenger()
      ->addMessage(t('Thankyou for your RSVP, you are on the list for the event.'));
  }

}
