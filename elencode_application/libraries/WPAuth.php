<? if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* Copyright 2009 Elencode
* This file is part of Elencode.
* Elencode is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Elencode is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Elencode is available at https://github.com/micz/elencode
* Contact Mic at m [at] micz [dot] it
*
*/

class WPAuth {

  var $CI;
  var $WPSecrets;
  
  function __construct()
  {
    $this->CI=&get_instance();
    $this->CI->config->load('wp_config');
    $this->_load_wp_secrets();
  }

  function get_user()
  {
    return $this->_validate_auth_cookie('','logged_in');
  }

  private function _validate_auth_cookie($cookie = '', $scheme = '')
  {
    if(!$cookie_elements=$this->_parse_auth_cookie($cookie, $scheme)){
      return new User();
    }

    extract($cookie_elements, EXTR_OVERWRITE);

    $expired = $expiration;

    // Allow a grace period for POST and AJAX requests
    if ( defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD'] )
      $expired += 3600;

    // Quick check to see if an honest cookie has expired
    if ( $expired < time() ) {
      return false;
    }

    $key = $this->_hash($username . '|' . $expiration, $scheme);
    $hash = hash_hmac('md5', $username . '|' . $expiration, $key);

    if ( $hmac != $hash ) {
      return new User();
    }

    return $this->CI->Usermodel->get_userdata_by_login($username);
  }
  
  
  private function _parse_auth_cookie($cookie='',$scheme='')
  {
    if ( empty($cookie) ) {
      switch ($scheme){
        case 'auth':
          $cookie_name = $this->WPSecrets['AUTH_COOKIE'];
          break;
        case 'secure_auth':
          $cookie_name = $this->WPSecrets['SECURE_AUTH_COOKIE'];
          break;
        case 'logged_in':
          $cookie_name = $this->WPSecrets['LOGGED_IN_COOKIE'];
          break;
        default:
          if($this->_is_ssl()){
            $cookie_name = $this->WPSecrets['SECURE_AUTH_COOKIE'];
            $scheme = 'secure_auth';
          } else {
            $cookie_name = $this->WPSecrets['AUTH_COOKIE'];
            $scheme = 'auth';
          }
        }

      if ( empty($_COOKIE[$cookie_name]) )
        return false;
      $cookie = $_COOKIE[$cookie_name];
    }

    $cookie_elements = explode('|', $cookie);
    if ( count($cookie_elements) != 3 )
      return false;

    list($username, $expiration, $hmac) = $cookie_elements;

    return compact('username', 'expiration', 'hmac', 'scheme');
  }
  
  
  private function _hash($data, $scheme = 'auth')
  {
    $salt = $this->_salt($scheme);
    return hash_hmac('md5', $data, $salt);
  }
  
  
  private function _salt($scheme = 'auth')
  {
    $secret_key = '';
    if ( ('' != $this->CI->config->item('SECRET_KEY')) && ( $this->CI->config->item('wp_default_secret_key') != $this->CI->config->item('SECRET_KEY') ))
      $secret_key = $this->CI->config->item('SECRET_KEY');

    if ( 'auth' == $scheme ) {
      if ( ('' != $this->CI->config->item('AUTH_KEY')) && ( $this->CI->config->item('wp_default_secret_key') != $this->CI->config->item('AUTH_KEY') ))
        $secret_key = $this->CI->config->item('AUTH_KEY');

        $salt = $this->WPSecrets['auth_salt'];
    } elseif ( 'secure_auth' == $scheme ) {
      if ( ('' != $this->CI->config->item('SECURE_AUTH_KEY')) && ( $this->CI->config->item('wp_default_secret_key') != $this->CI->config->item('SECURE_AUTH_KEY') ))
        $secret_key = $this->CI->config->item('SECURE_AUTH_KEY');
        $salt = $this->WPSecrets['secure_auth_salt'];
    } elseif ( 'logged_in' == $scheme ) {
      if ( ('' != $this->CI->config->item('LOGGED_IN_KEY')) && ( $this->CI->config->item('wp_default_secret_key') != $this->CI->config->item('LOGGED_IN_KEY') ))
        $secret_key = $this->CI->config->item('LOGGED_IN_KEY');
        $salt = $this->WPSecrets['logged_in_salt'];
    } elseif ( 'nonce' == $scheme ) {
      if ( ('' != $this->CI->config->item('NONCE_KEY')) && ( $this->CI->config->item('wp_default_secret_key') != NONCE_KEY) )
        $secret_key = $this->CI->config->item('NONCE_KEY');
        $salt = $this->WPSecrets['nonce_salt'];
    } else {
      // ensure each auth scheme has its own unique salt
      $salt = hash_hmac('md5', $scheme, $secret_key);
    }

    return $secret_key.$salt;
 }
 
  private function _is_ssl()
  {
    if ( isset($_SERVER['HTTPS']) ) {
      if ( 'on' == strtolower($_SERVER['HTTPS']) )
        return true;
      if ( '1' == $_SERVER['HTTPS'] )
        return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
      return true;
    }
    return false;
  }
  
  private function _load_wp_secrets()
  {
    if(!$query=$this->CI->db->query('SELECT option_name,option_value FROM wp_options WHERE option_name IN (\'siteurl\',\'auth_salt\',\'logged_in_salt\',\'secure_auth_salt\',\'nonce_salt\',\'secret\')'))
		return false;
		
		$wps=array();
	
	  foreach ($query->result() as $row)
    {
      $wps[$row->option_name]=$row->option_value;
    }
    
    $wps['COOKIEHASH']=md5($wps['siteurl']);
    $wps['USER_COOKIE']='wordpressuser_' . $wps['COOKIEHASH'];
    $wps['PASS_COOKIE']= 'wordpresspass_' . $wps['COOKIEHASH'];
    $wps['AUTH_COOKIE']= 'wordpress_' . $wps['COOKIEHASH'];
    $wps['SECURE_AUTH_COOKIE']= 'wordpress_sec_' . $wps['COOKIEHASH'];
    $wps['LOGGED_IN_COOKIE']= 'wordpress_logged_in_' . $wps['COOKIEHASH'];
    
    $this->WPSecrets=$wps;
    
    return true;
  }
}
?>