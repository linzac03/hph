<?php

namespace Drupal\connected_accounts\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\connected_accounts\Controller\AccountController;

class ConnectedAccountsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return [
      'connected_accounts.settings',
    ];
  }

  public function getFormId() {
    return 'connected_accounts_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('connected_accounts.settings');
    $controller = new AccountController;
    $form['twitter'] = [
      '#type' => 'fieldset',
      '#title' => 'Connect Twitter Account',
      'button' => [
        '#markup' => $controller->twitter(),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }
//Not needed?
/*
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('connected_accounts.settings')
      ->set('twitter.username', $form_state->getValue('twitter.username'))
      ->set('twitter.password', $form_state->getValue('twitter.password'))
      ->save();
  }
*/

}
