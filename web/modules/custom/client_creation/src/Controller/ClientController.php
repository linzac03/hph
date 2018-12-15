<?php

namespace Drupal\client_creation\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ClientController.
 */
class ClientController extends ControllerBase {

  public function login() {
    $form = \Drupal::formBuilder()->getForm('Drupal\client_creation\Form\ClientLoginForm');
    
    $build = [
      '#theme' => 'client_login',
      '#form' => $form
    ];
    return $build;
  }

}
