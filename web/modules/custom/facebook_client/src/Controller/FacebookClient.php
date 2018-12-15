<?php

namespace Drupal\facebook_client\Controller;

use Drupal\Core\Controller\ControllerBase;

class FacebookClient extends ControllerBase {
  protected $client;

  public __construct() {
    $this->client = new \Facebook\Facebook([
      'app_id' => '2747942515430882',
      'app_secret' => 'c02ef7e7392ee564ef84c7f13073d88e',
      'default_graph_version' => 'v2.10',
      //'default_access_token' => '{access-token}', // optional
    ]);
  }

  public getLoginButton() {
    if (!isset($client)) { 
      drupal_set_message("Client not set, class instantiated incorrectly", 'error');
      return "<p class='facebook-error'>Error, no button here</p>";
    }

    $helper = $this->client->getRedirectLoginHelper();
    $permissions = ['email'];
    $callback = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . '/admin/config/connected_accounts/settings';
    $loginUrl = $helper->getLoginUrl($callback, $permissions);
  
    return "<a class='facebook-login-btn' href='$loginURL'>Login with Facebook</a>";
  }

} 
