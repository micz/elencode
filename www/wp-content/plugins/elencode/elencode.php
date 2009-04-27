<?php
/*
Plugin Name: Elencode
Plugin URI: http://code.google.com/p/elencode
Description: Adds nedeed <a href="http://code.google.com/p/elencode" target="_blank">Elencode</a> functionalities to Wordpress.
Version: 1.0.0
Author: Mic
Author URI: http://micz.it/
*/

/*
* Copyright 2009 Elencode
* This file is part of Elencode.
* Elencode is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Elencode is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Elencode; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Elencode is available at https://github.com/micz/elencode
* Contact the Dev Team at dev [at] elenbar [dot] it
*
*/

/*function elencode_check_username($valid,$username)
{
  global $errors;
  $errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
  return $valid;
}

add_filter('validate_username','elencode_check_username',10,2);
*/

//Check if the username exists in the characters table
function elencode_check_username($user_login,$user_email,$errors){
  global $wpdb;
  $present_errors=$errors->get_error_codes();
  if(!in_array('username_exists',$present_errors)){
    if($wpdb->get_row($wpdb->prepare("SELECT id FROM el_characters WHERE name = '%s'",$user_login))){
      $errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
    }
  }
}

add_action('register_post','elencode_check_username',10,3);
?>