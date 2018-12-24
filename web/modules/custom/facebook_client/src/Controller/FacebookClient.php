<?php

namespace Drupal\facebook_client\Controller;

use Drupal\Core\Controller\ControllerBase;

class FacebookClient extends ControllerBase {
  protected $client;

  public function __construct() {
    $this->client = new \Facebook\Facebook([
      'app_id' => '2747942515430882',
      'app_secret' => 'c02ef7e7392ee564ef84c7f13073d88e',
      'default_graph_version' => 'v2.10',
      //'default_access_token' => '{access-token}', // optional
    ]);
  }

  public function getLoginButton() {
    if (!isset($this->client)) { 
      drupal_set_message("Client not set, class instantiated incorrectly", 'error');
      return "<p class='facebook-error'>Error, no button here</p>";
    }

    $helper = $this->client->getRedirectLoginHelper();
    $permissions = ['email'];
    $callback = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . '/admin/config/connected_accounts/settings';
    $loginUrl = $helper->getLoginUrl($callback, $permissions);
  
    return "<a class='facebook-login-btn' href='$loginUrl'>Login with Facebook</a>";
  }

  public function getAccessToken() {
    $helper = $this->client->getRedirectLoginHelper();

    try {
      $access_token = $helper->getAccessToken();
    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
      \Drupal::logger('facebook_client')->error('Graph API returned error: ' . $e->getMessage());
      return null;
    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
      \Drupal::logger('facebook_client')->error('Facebook SDK returned an error: ' . $e->getMessage());
      return null;
    }

    if (!isset($access_token)) {
      if ($helper->getError()) {
        \Drupal::logger('facebook_client')->error("Error: {$helper->getError()}");
        \Drupal::logger('facebook_client')->error("Error Code: {$helper->getErrorCode()}");
        \Drupal::logger('facebook_client')->error("Error Description: {$helper->getErrorDescription()}");
        return null;
      } else {
        \Drupal::logger('facebook_client')->error("Bad request: Failed to retrieve access token");
        return null;
      }
    }

    $oauth_client = $this->client->getOAuth2Client();

    // Get access token metadata
    $token_metadata = $oauth_client->debugToken($access_token);

    // Validate token
    $token_metadata->validateAppId();
    $token_metadata->validateExpiration();

    if (!$access_token->isLongLived()) {
      try {
        $access_token = $oauth_client->getLongLivedAccessToken($access_token);
      } catch (\Facebook\Exceptions\FacebookSDKException $e) {
        \Drupal::logger('facebook_client')->error('Error retrieving long-lived access token: ' . $e->getMessage());
        return null;
      }
    }

    return $access_token;
  }

  public function getInstagramFeed() {
    // Grab all media from account
    $business_id = $this->client->get('/page?fields=instagram_business_profile');
    $posts = $this->client->get('/media?fields=id,media_url');
  }

} 
