<?php

namespace Drupal\connected_accounts;

class AccountsKeyLoader {
  public function getTwitterKey() {
    $keyrepo = \Drupal::service('key.repository');
    $tokens = $keyrepo->getKey('twitter_consumer_api_key')->getKeyValue();
    //$atokens = $keyrepo->getKey('twitter_access_token')->getKeyValue();
    if ($tokens == null) {
      drupal_set_message('Failed to load twitter','error');
      return [];
    }

    //separate out tokens
    $tokens = json_decode($tokens, true);
    $arrkey = array_keys($tokens);
    $token_secret = $arrkey[0];
    $token = $tokens[$token_secret];

    return ['token' => $token, 'token_secret' => $token_secret];
  }

  public function getFacebookKey() {}
}
