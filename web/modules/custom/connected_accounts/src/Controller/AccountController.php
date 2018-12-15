<?php

namespace Drupal\connected_accounts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\connected_accounts\AccountsKeyLoader;
use Abraham\TwitterOAuth\TwitterOAuth;

class AccountController extends ControllerBase {

  protected function loggedIn($platform) {
    $is = true;
    switch($platform) {
      case 'twitter':
        $is = false;
        break;
      case 'instagram':
        break;
      case 'stripe':
        break;
    }
    return $is;
  }

  public function twitter() {
    //Grab services
    $keyrepo = \Drupal::service('key.repository');
    $config = \Drupal::service('config.factory')->getEditable('connected_accounts.twitter');

    //Get tokens from key loader
    $loader = new AccountsKeyLoader();
    $tokens = $loader->getTwitterKey();

    if (empty($tokens)) {
      return "<p>Unable to load Twitter</p>";
    }

    $init = new TwitterOAuth($tokens['token'],$tokens['token_secret']);

/** TODO: Handle logout */
    if (isset($_GET['logout'])) {
      // remove tokens/data from storage
      $config->set('twitter_request_oauth_token',null);
      $config->set('twitter_request_oauth_token_secret',null);
      $config->set('twitter_access_oauth_token',null);
      $config->set('twitter_access_oauth_token_secret',null);
      $config->set('twitter_data',null);
      $config->set('twitter_name',null);
      $config->save();
      unset($_GET['logout']);
      // redirect to same page to remove url paramters
      $redirect = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . '/admin/config/connected_accounts/settings';
      header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    //Check for logged in
    if(null === $config->get('twitter_data') && !isset($_GET['oauth_token'])) {
      $callback_url = 'https://canopy-client-log.parallelpublicworks.com/twitter-redirect?orig_host=' . \Drupal::request()->getHttpHost();
      try {
        $request_token = $init->oauth("oauth/request_token", ["oauth_callback" => $callback_url]);
        if ($request_token) {
          $config->set('twitter_request_oauth_token',$request_token['oauth_token']);
          $config->set('twitter_request_oauth_token_secret',$request_token['oauth_token_secret']);
          $config->save();
          $login_url = $init->url("oauth/authorize", ["oauth_token" => $request_token['oauth_token']]);
        }
      } catch(\Exception $e) {
        drupal_set_message('Error retrieving request token, likely unauthorized callback URL','error');
        return '<p>Error retrieving request token</p>';
      }
    }

    //Check for callback and retrieve access token
    if (isset($_GET['oauth_token'])) {
      $connection = new TwitterOAuth($tokens['token'],
                                     $tokens['token_secret'],
				     $config->get('twitter_request_oauth_token'),
                                     $config->get('twitter_request_oauth_token_secret'));
      $access_token = $connection->oauth("oauth/access_token",["oauth_verifier" => $_REQUEST['oauth_verifier']]);
      if ($access_token) {
        // create another connection object with access token
        $config->set('twitter_access_oauth_token',$access_token['oauth_token']);
        $config->set('twitter_access_oauth_token_secret',$access_token['oauth_token_secret']);
        $config->save();
        $connection = new TwitterOAuth($tokens['token'], 
                                       $tokens['token_secret'], 
                                       $access_token['oauth_token'], 
                                       $access_token['oauth_token_secret']);
        // set the parameters array with attributes include_entities false
        $params = ['include_entities'=>'false'];
        // get the data
        $data = $connection->get('account/verify_credentials',$params);
        if ($data) {
          $config->set('twitter_name', $data->screen_name);
          $config->save();
          $redirect = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . \Drupal::request()->getRequestUri();
          header('Status: 200'); // The twitteroauth library will complain with the redirects from forge so we set this explicitly
          header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }
      }
    }

    if(isset($login_url) && null === $config->get('twitter_name')){
      // Need to decide what to return here for login info
      return "<a href='$login_url'><button>Login with twitter </button></a>";
      
    } else {
      $name = $config->get('twitter_name');
      return "<p class='twitter-username'>Logged in as: {$name}</p><a href='?logout=true'><button>Logout</button></a>";
    } 
  }

  public function defaultTwitterFeed($username="parallelpw") {
    $loader = new AccountsKeyLoader();
    $tokens = $loader->getTwitterKey();
    if (empty($tokens)) {
      // Can not load default twitter feed
      return [];
    }
    $init = new TwitterOAuth($tokens['token'],$tokens['token_secret']);
    $accessToken = $init->oauth2('oauth2/token', array('grant_type' => 'client_credentials'));
    $connection = new TwitterOAuth($tokens['token'],
                                   $tokens['token_secret'],
                                   null,
                                   $accessToken->access_token);
    $result = $connection->get('statuses/user_timeline', array('screen_name' => $username,
                                                               'count' => '3'));
    return $result;
  }

  public function twitterFeed() {
    $loader = new AccountsKeyLoader();
    $tokens = $loader->getTwitterKey();
    $config = \Drupal::service('config.factory')->getEditable('connected_accounts.twitter');
    $access = $config->get('twitter_access_oauth_token');
    $access_secret = $config->get('twitter_access_oauth_token_secret');
    if ($access === null || $access_secret === null) {
      return $this->defaultTwitterFeed();
    } 
    // create another connection object with access token
    $connection = new TwitterOAuth($tokens['token'],
                                   $tokens['token_secret'],
                                   $access,
                                   $access_secret);
    // set the parameters array with attributes include_entities false
    $params =[
      'screen_name' => $config->get('twitter_name'),
      'count' => '3'
    ];
    // get the data
    $data = $connection->get('statuses/user_timeline',$params);
    return $data;
  }

  public function facebook() {
    //Grab services
    $keyrepo = \Drupal::service('key.repository');
    $config = \Drupal::service('config.factory')->getEditable('connected_accounts.facebook');

    //Get tokens from key loader
    $loader = new AccountsKeyLoader();
    $tokens = $loader->getFacebookKey();

    if (empty($tokens)) {
      return "<p>Unable to load Facebook</p>";
    }

    
    
/** TODO: Handle logout */
    if (isset($_GET['logout'])) {
      // remove tokens/data from storage
      $config->set('facebook_request_oauth_token',null);
      $config->set('facebook_request_oauth_token_secret',null);
      $config->set('facebook_access_oauth_token',null);
      $config->set('facebook_access_oauth_token_secret',null);
      $config->set('facebook_data',null);
      $config->set('facebook_name',null);
      $config->save();
      unset($_GET['logout']);
      // redirect to same page to remove url paramters
      $redirect = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . '/admin/config/connected_accounts/settings';
      header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    //Check for logged in
    if(null === $config->get('twitter_data') && !isset($_GET['oauth_token'])) {
      $callback_url = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . '/admin/config/connected_accounts/settings';
      try {
        $request_token = $init->oauth("oauth/request_token", ["oauth_callback" => $callback_url]);
        if ($request_token) {
          $config->set('facebook_request_oauth_token',$request_token['oauth_token']);
          $config->set('facebook_request_oauth_token_secret',$request_token['oauth_token_secret']);
          $config->save();
          $login_url = $init->url("oauth/authorize", ["oauth_token" => $request_token['oauth_token']]);
        }
      } catch(\Exception $e) {
        drupal_set_message('Error retrieving request token, likely unauthorized callback URL','error');
        return '<p>Error retrieving request token</p>';
      }
    }

    //Check for callback and retrieve access token
    if (isset($_GET['oauth_token'])) {
      $connection = new TwitterOAuth($tokens['token'],
                                     $tokens['token_secret'],
				     $config->get('twitter_request_oauth_token'),
                                     $config->get('twitter_request_oauth_token_secret'));
      $access_token = $connection->oauth("oauth/access_token",["oauth_verifier" => $_REQUEST['oauth_verifier']]);
      if ($access_token) {
        // create another connection object with access token
        $config->set('twitter_access_oauth_token',$access_token['oauth_token']);
        $config->set('twitter_access_oauth_token_secret',$access_token['oauth_token_secret']);
        $config->save();
        $connection = new TwitterOAuth($tokens['token'], 
                                       $tokens['token_secret'], 
                                       $access_token['oauth_token'], 
                                       $access_token['oauth_token_secret']);
        // set the parameters array with attributes include_entities false
        $params = ['include_entities'=>'false'];
        // get the data
        $data = $connection->get('account/verify_credentials',$params);
        if ($data) {
          $config->set('twitter_name', $data->screen_name);
          $config->save();
          $redirect = \Drupal::request()->getScheme() . '://' . \Drupal::request()->getHttpHost() . \Drupal::request()->getRequestUri();
          header('Status: 200'); // The twitteroauth library will complain with the redirects from forge so we set this explicitly
          header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }
      }
    }

    if(isset($login_url) && null === $config->get('twitter_name')){
      // Need to decide what to return here for login info
      return "<a href='$login_url'><button>Login with twitter </button></a>";
      
    } else {
      $name = $config->get('twitter_name');
      return "<p class='twitter-username'>Logged in as: {$name}</p><a href='?logout=true'><button>Logout</button></a>";
    } 
  }
}
