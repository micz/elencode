<?php
/*
Plugin Name: SI CAPTCHA
Plugin URI: http://www.642weather.com/weather/scripts-wordpress-captcha.php
Description: A CAPTCHA to protect comment posts and or registrations in WordPress
Version: 1.5.2
Author: Mike Challis
Author URI: http://www.642weather.com/weather/scripts.php
*/

/*  Copyright (C) 2008 Mike Challis  (http://www.642weather.com/weather/contact_us.php)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!class_exists('siCaptcha')) {
  session_cache_limiter ('private, must-revalidate');
  if( !isset( $_SESSION ) ) {
    session_start();
  }
 class siCaptcha {


function add_tabs() {
    //add_options_page('Captcha Options', 'Captcha', 9, __FILE__,array(&$this,'options_page'));
    add_submenu_page('plugins.php', __('SI Captcha Options', 'si-captcha'), __('SI Captcha Options', 'si-captcha'), 'manage_options', __FILE__,array(&$this,'options_page'));
}

function get_settings($value) {
// gets the settings, providing defaults if need be
if (!get_option($value)) {
        $defaults = array(
         'si_captcha_perm' => 'true',
         'si_captcha_perm_level' => 'read',
         'si_captcha_comment' => 'true',
         'si_captcha_register' => 'true',
         'si_captcha_rearrange' => 'false');
        update_option($value,$defaults[$value]);
        return $defaults[$value];
} else {
        return get_option($value);
}
} //end get_settings

function options_page() {
  global $si_captcha_nonce;
  if ($_POST['submit']) {
    if ( function_exists('current_user_can') && !current_user_can('manage_options') )
                        die(__('You do not have permissions for managing this option', 'si-captcha'));

    $possible_options = array_keys($_POST);
    //if the options are part of an array
        foreach($possible_options as $option) {
                update_option($option,trim($_POST[$option]));
        }

    if ( isset( $_POST['si_captcha_perm'] ) )
         update_option( 'si_captcha_perm', 'true' );
        else
         update_option( 'si_captcha_perm', 'false' );

    if ( isset( $_POST['si_captcha_comment'] ) )
         update_option( 'si_captcha_comment', 'true' );
        else
         update_option( 'si_captcha_comment', 'false' );

    if ( isset( $_POST['si_captcha_register'] ) )
         update_option( 'si_captcha_register', 'true' );
        else
         update_option( 'si_captcha_register', 'false' );

    if ( isset( $_POST['si_captcha_rearrange'] ) )
         update_option( 'si_captcha_rearrange', 'true' );
        else
         update_option( 'si_captcha_rearrange', 'false' );

  }
?>
<?php if ( !empty($_POST ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'si-captcha') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('SI Captcha Options', 'si-captcha') ?></h2>

<p>
<?php _e('Your theme must have a', 'si-captcha') ?> &lt;?php do_action('comment_form', $post->ID); ?&gt; <?php _e('tag inside your comments.php form. Most themes do.', 'si-captcha'); echo ' '; ?>
<?php _e('The best place to locate the tag is before the comment textarea, you may want to move it if it is below the comment textarea, or the captcha image and captcha code entry might display after the submit button.', 'si-captcha') ?>
</p>

<form name="formoptions" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>&amp;updated=true">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="form_type" value="upload_options" />
    <?php si_captcha_nonce_field($si_captcha_nonce) ?>
        <fieldset class="options">

        <table width="100%" cellspacing="2" cellpadding="5" class="form-table">

        <tr>
            <th scope="row"><label for="si_captcha_register"><?php _e('CAPTCHA on Register Form:', 'si-captcha') ?></label></th>
        <td>
    <input name="si_captcha_register" id="si_captcha_register" type="checkbox"
    <?php if ( $this->get_settings('si_captcha_register') == 'true' ) echo ' checked="checked" '; ?> />
    <?php _e('Enable CAPTCHA on the register form.', 'si-captcha') ?><br />
    </td>
        </tr>

        <tr>
            <th scope="row"><label for="si_captcha_comment"><?php _e('CAPTCHA on Comment Form:', 'si-captcha') ?></label></th>
        <td>
    <input name="si_captcha_comment" id="si_captcha_comment" type="checkbox" <?php if ( $this->get_settings('si_captcha_comment') == 'true' ) echo ' checked="checked" '; ?> />
    <?php _e('Enable CAPTCHA on the comment form.', 'si-captcha') ?><br />

        <input name="si_captcha_perm" id="si_captcha_perm" type="checkbox" <?php if( $this->get_settings('si_captcha_perm') == 'true' ) echo 'checked="checked"'; ?> />
        <label name="si_captcha_perm" for="si_captcha_perm"><?php _e('Hide CAPTCHA for', 'si-captcha') ?>
        <strong><?php _e('registered', 'si-captcha') ?></strong>
         <?php _e('users who can:', 'si-captcha') ?></label>
        <?php $this->si_captcha_perm_dropdown('si_captcha_perm_level', $this->get_settings('si_captcha_perm_level'));  ?>
    </td>
        </tr>

    <tr>
        <th scope="row"><label for="si_captcha_rearrange"><?php _e('Comment Form Rearrange:', 'si-captcha') ?></label></th>
        <td>
    <input name="si_captcha_rearrange" id="si_captcha_rearrange" type="checkbox"
    <?php if ( $this->get_settings('si_captcha_rearrange') == 'true' ) echo ' checked="checked" '; ?> />
    <?php _e('Change the display order of the catpcha input field on the comment form. (see note below).', 'si-captcha') ?><br />
    </td>
        </tr>

        </table>
        </fieldset>

<p><strong><?php _e('Problem:', 'si-captcha') ?></strong>
<?php _e('Sometimes the captcha image and captcha input field are displayed AFTER the submit button on the comment form.', 'si-captcha') ?><br />
<strong><?php _e('Fix:', 'si-captcha') ?></strong>
<?php _e('Edit your current theme comments.php file and locate this line:', 'si-captcha') ?><br />
&lt;?php do_action('comment_form', $post->ID); ?&gt;<br />
<?php _e('This tag is exactly where the captcha image and captcha code entry will display on the form, so move the line to BEFORE the comment textarea, uncheck the option box above, and the problem should be fixed.', 'si-captcha') ?><br />
<?php _e('Alernately you can just check the box above and javascript will attempt to rearrange it for you, but editing the comments.php, moving the tag, and unchecking this box is the best solution.', 'si-captcha') ?><br />
<?php _e('Why is it better to uncheck this and move the tag? because the XHTML will no longer validate on the comment page if it is checked.', 'si-captcha') ?>
</p>
        <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Update Options', 'si-captcha') ?> &raquo;" />
        </p>
</form>
</div>
<?php
}// end options_page

function si_captcha_perm_dropdown($select_name, $checked_value='') {
        // choices: Display text => permission_level
        $choices = array (
                 __('All registered users', 'si-captcha') => 'read',
                 __('Edit posts', 'si-captcha') => 'edit_posts',
                 __('Publish Posts', 'si-captcha') => 'publish_posts',
                 __('Moderate Comments', 'si-captcha') => 'moderate_comments',
                 __('Administer site', 'si-captcha') => 'level_10'
                 );
        // print the <select> and loop through <options>
        echo '<select name="' . $select_name . '" id="' . $select_name . '">' . "\n";
        foreach ($choices as $text => $capability) :
                if ($capability == $checked_value) $checked = ' selected="selected" ';
                echo "\t". '<option value="' . $capability . '"' . $checked . ">$text</option> \n";
                $checked = '';
        endforeach;
        echo "\t</select>\n";
 }

function captchaCheckRequires() {

  global $captcha_path;

  $ok = 'ok';
  // Test for some required things, print error message if not OK.
  if ( !extension_loaded('gd') || !function_exists('gd_info') ) {
       echo '<p style="color:maroon">'.__('ERROR: si-captcha.php plugin says GD image support not detected in PHP!', 'si-captcha').'</p>';
       echo '<p>'.__('Contact your web host and ask them why GD image support is not enabled for PHP.', 'si-captcha').'</p>';
      $ok = 'no';
  }
  if ( !function_exists('imagepng') ) {
       echo '<p style="color:maroon">'.__('ERROR: si-captcha.php plugin says imagepng function not detected in PHP!', 'si-captcha').'</p>';
       echo '<p>'.__('Contact your web host and ask them why imagepng function is not enabled for PHP.', 'si-captcha').'</p>';
      $ok = 'no';
  }
  if ( !file_exists("$captcha_path/securimage.php") ) {
       echo '<p style="color:maroon">'.__('ERROR: si-captcha.php plugin says captcha_library not found.', 'si-captcha').'</p>';
       $ok = 'no';
  }
  if ($ok == 'no')  return false;
  return true;
}

// this function adds the captcha to the comment form
function addCaptchaToCommentForm() {
    global $user_ID, $captcha_url;

    // skip the captcha if user is loggged in and the settings allow
    if (isset($user_ID) && intval($user_ID) > 0 && $this->get_settings('si_captcha_perm') == 'true') {
       // skip the CAPTCHA display if the minimum capability is met
       if ( current_user_can( $this->get_settings('si_captcha_perm_level') ) ) {
               // skip capthca
               return true;
        }
    }

    $captcha_rearrange = $this->get_settings('si_captcha_rearrange');

// the captch html
echo '
<div style="display:block;" id="captchaImgDiv">
';

// Test for some required things, print error message right here if not OK.
if ($this->captchaCheckRequires()) {

echo '
<div style="width: 250px; height: 55px; padding-top: 10px;">
         <img id="siimage" style="padding-right: 5px; border-style: none; float:left;"
         src="'.$captcha_url.'/securimage_show.php?sid='.md5(uniqid(time())).'"
         alt="'.__('CAPTCHA Image', 'si-captcha').'" title="'.__('CAPTCHA Image', 'si-captcha').'" />
           <a href="'.$captcha_url.'/securimage_play.php" title="'.__('Audible Version of CAPTCHA', 'si-captcha').'">
         <img src="'.$captcha_url.'/images/audio_icon.gif" alt="'.__('Audio Version', 'si-captcha').'"
          style="border-style: none; vertical-align:top; border-style: none;" onclick="this.blur()" /></a><br />
           <a href="#" title="'.__('Refresh Image', 'si-captcha').'" style="border-style: none"
         onclick="document.getElementById(\'siimage\').src = \''.$captcha_url.'/securimage_show.php?sid=\' + Math.random(); return false">
         <img src="'.$captcha_url.'/images/refresh.gif" alt="'.__('Reload Image', 'si-captcha').'"
         style="border-style: none; vertical-align:bottom;" onclick="this.blur()" /></a>
</div>
<div id="captchaInputDiv" style="display:block;" >
<input type="text" name="captcha_code" id="captcha_code" tabindex="4" aria-required=\'true\' style="width:65px;" />
 <label for="captcha_code"><small>'.__('CAPTCHA Code (required)', 'si-captcha').'</small></label>
</div>
</div>
';


// rearrange submit button display order
if ($captcha_rearrange == 'true') {
     print  <<<EOT
      <script type='text/javascript'>
          var sUrlInput = document.getElementById("url");
                  var oParent = sUrlInput.parentNode;
          var sSubstitue = document.getElementById("captchaImgDiv");
                  oParent.appendChild(sSubstitue, sUrlInput);
      </script>
            <noscript>
          <style type='text/css'>#submit {display:none;}</style><br />
EOT;
  echo '           <input name="submit" type="submit" id="submit-alt" tabindex="6" value="'.__('Submit Comment', 'si-captcha').'" />
          </noscript>
  ';

}
}else{
 echo '</div>';
}
        return true;
}

// this function adds the captcha to the comment form
function addCaptchaToRegisterForm() {
   global $captcha_url;

   if ($this->get_settings('si_captcha_register') != 'true') {
        return true; // captcha setting is disabled for registration
   }

// Test for some required things, print error message right here if not OK.
if ($this->captchaCheckRequires()) {

// the captch html
echo '
<div style="width: 250px;  height: 55px">
         <img id="siimage" style="padding-right: 5px; border-style: none; float:left;"
         src="'.$captcha_url.'/securimage_show.php?sid='.md5(uniqid(time())).'"
         alt="'.__('CAPTCHA Image', 'si-captcha').'" title="'.__('CAPTCHA Image', 'si-captcha').'" />
           <a href="'.$captcha_url.'/securimage_play.php" title="'.__('Audible Version of CAPTCHA', 'si-captcha').'">
         <img src="'.$captcha_url.'/images/audio_icon.gif" alt="'.__('Audio Version', 'si-captcha').'"
          style="border-style: none; vertical-align:top; border-style: none;" onclick="this.blur()" /></a><br />
           <a href="#" title="'.__('Refresh Image', 'si-captcha').'" style="border-style: none"
         onclick="document.getElementById(\'siimage\').src = \''.$captcha_url.'/securimage_show.php?sid=\' + Math.random(); return false">
         <img src="'.$captcha_url.'/images/refresh.gif" alt="'.__('Reload Image', 'si-captcha').'"
         style="border-style: none; vertical-align:bottom;" onclick="this.blur()" /></a>
</div>
<p>
<input type="text" name="captcha_code" id="captcha_code" class="input" tabindex="30" aria-required=\'true\' style="width:65px;" />
 <label for="captcha_code">'.__('CAPTCHA Code (required)', 'si-captcha').'</label>
</p>
<br />
';
}

        return true;
}

// this function checks the captcha posted with registration on vers 2.5+
function checkCaptchaRegisterPostNew($errors) {
   global $captcha_path;

   if (empty($_POST['captcha_code']) || $_POST['captcha_code'] == '') {
                $errors->add('captcha_blank', '<strong>'.__('ERROR', 'si-captcha').'</strong>: '.__('Please complete the CAPTCHA.'));
                return $errors;
   } else {
        $captcha_code = trim(strip_tags($_POST['captcha_code']));
   }

   include "$captcha_path/securimage.php";
   $img = new Securimage();
   $valid = $img->check("$captcha_code");
   // Check, that the right CAPTCHA password has been entered, display an error message otherwise.
   if($valid == true) {
       // ok can continue

   } else {
    $errors->add('captcha_wrong', '<strong>'.__('ERROR', 'si-captcha').'</strong>: '.__('That CAPTCHA was incorrect.'));
   }
   return($errors);
}

// this function checks the captcha posted with registration pre vers 2.5
function checkCaptchaRegisterPost() {
   global $errors, $captcha_path;

   if (empty($_POST['captcha_code']) || $_POST['captcha_code'] == '') {
                $errors['captcha_blank'] = '<strong>'.__('ERROR', 'si-captcha').'</strong>: '.__('Please complete the CAPTCHA.');
                return $errors;
   } else {
       $captcha_code = trim(strip_tags($_POST['captcha_code']));

      include_once "$captcha_path/securimage.php";
      $img = new Securimage();
      $valid = $img->check("$captcha_code");
      // Check, that the right CAPTCHA password has been entered, display an error message otherwise.
      if($valid == true) {
          // ok can continue

      } else {
            $errors['captcha_wrong'] = '<strong>'.__('ERROR', 'si-captcha').'</strong>: '.__('That CAPTCHA was incorrect.');
      }
   }

}


// this function checks the captcha posted with the comment
function checkCaptchaCommentPost($comment) {
    global $user_ID, $captcha_path;

    // added for compatibility with WP Wall plugin
    // this does NOT add CAPTCHA to WP Wall plugin,
    // it just prevents the "Error: You did not enter a Captcha phrase." when submitting a WP Wall comment
    if ( function_exists('WPWall_Widget') && isset($_POST['wpwall_comment']) ) {
        // skip capthca
        return $comment;
    }

    // skip the captcha if user is loggged in and the settings allow
    if (isset($user_ID) && intval($user_ID) > 0 && $this->get_settings('si_captcha_perm') == 'true') {
       // skip the CAPTCHA display if the minimum capability is met
       if ( current_user_can( $this->get_settings('si_captcha_perm_level') ) ) {
               // skip capthca
               return $comment;
        }
    }

    // Skip captcha for trackback or pingback
    if ( $comment['comment_type'] != '' && $comment['comment_type'] != 'comment' ) {
               // skip capthca
               return $comment;
    }

    if (empty($_POST['captcha_code']) || $_POST['captcha_code'] == '') {
        wp_die( __('Error: You did not enter a Captcha phrase. Press your browsers back button and try again.', 'si-captcha'));
    }
    $captcha_code = trim(strip_tags($_POST['captcha_code']));

   include_once "$captcha_path/securimage.php";
   $img = new Securimage();
   $valid = $img->check("$captcha_code");
   // Check, that the right CAPTCHA password has been entered, display an error message otherwise.
   if($valid == true) {
       // ok can continue
       return($comment);
   } else {
       wp_die( __('Error: You entered in the wrong Captcha phrase. Press your browsers back button and try again.', 'si-captcha'));
   }

}

function unset_si_captcha_options () {
  delete_option('si_captcha_perm');
  delete_option('si_captcha_perm_level');
  delete_option('si_captcha_comment');
  delete_option('si_captcha_register');
  delete_option('si_captcha_rearrange');
}

function init() {
   if (function_exists('load_plugin_textdomain')) {
      #load_plugin_textdomain('si-captcha', 'wp-content/plugins/si-captcha-for-wordpress');
      load_plugin_textdomain('si-captcha', false, dirname(plugin_basename(__FILE__)) );
   }
}

} // end of class
} // end of if class

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


if (class_exists("siCaptcha")) {
 $si_image_captcha = new siCaptcha();
}

if (isset($si_image_captcha)) {

  $captcha_url = WP_PLUGIN_URL . '/si-captcha-for-wordpress/captcha-secureimage';
  $captcha_path = WP_PLUGIN_DIR . '/si-captcha-for-wordpress/captcha-secureimage';

  if ( !function_exists("wp_nonce_field") ) {
        function si_captcha_nonce_field($action = -1) { return; }
        $si_captcha_nonce = -1;
  } else {
        function si_captcha_nonce_field($action = -1) { return wp_nonce_field($action); }
        $si_captcha_nonce = 'si-captcha-update-key';
  }


  //Actions
  add_action('init', array(&$si_image_captcha, 'init'));

  add_action('admin_menu', array(&$si_image_captcha,'add_tabs'),1);


  if ($si_image_captcha->get_settings('si_captcha_comment') == 'true') {
     // set the minimum capability needed to skip the captcha if there is one
     add_action('comment_form', array(&$si_image_captcha, 'addCaptchaToCommentForm'), 1);
     add_filter('preprocess_comment', array(&$si_image_captcha, 'checkCaptchaCommentPost'), 1);
  }


  if ($si_image_captcha->get_settings('si_captcha_register') == 'true') {
    add_action('register_form', array(&$si_image_captcha, 'addCaptchaToRegisterForm'), 1);

    if (version_compare(get_bloginfo('version'), '2.5' ) >= 0)
       add_filter('registration_errors', array(&$si_image_captcha, 'checkCaptchaRegisterPostNew'), 1);
    else
       add_filter('registration_errors', array(&$si_image_captcha, 'checkCaptchaRegisterPost'), 1);
  }

  // uncomment if you want the settings deleted when this plugin is deactivated
  //register_deactivation_hook(__FILE__, array(&$si_image_captcha, 'unset_si_captcha_options'), 1);
}

?>