<?php
/**
 * auth/keeyaiwp.class.php
 *
 * Wordpress integration - simply uses the wordpress login form and logout functions
 * Doesn't mess around with cookies or any other nonsense and requires no changes
 * to the wordpress install or DB
 *
 *
 * @author    Collin "Keeyai" Green <keeyai@keeyai.com>
 * @url: 			http://keeyai.com/projects-and-releases/dokuwiki-tools/dokuwiki-and-wordpress-integration/
 * @date: 		2008-09-27
 * @version:	1.0
 */
 
 // define WP --- change this to fit your installation. Make path relative to wiki directory
$wordpresspath = '../';	// dont forget the trailing slash

// look for wp-load for wordpress 2.6+
if( file_exists( DOKU_INC . $wordpresspath .'wp-load.php'))
	require_once(DOKU_INC . $wordpresspath .'wp-load.php');

// try wp-config for earlier versions
else if( file_exists(DOKU_INC . $wordpresspath .'wp-config.php'))
	require_once(DOKU_INC . $wordpresspath .'wp-config.php');

else
	nice_die('Could not find wordpress installation. Authetication System Failed');


class auth_keeyaiwp extends auth_basic {

  var $success = true;


  var $cando = array (
    'addUser'     => false, // can Users be created?
    'delUser'     => false, // can Users be deleted?
    'modLogin'    => false, // can login names be changed?
    'modPass'     => false, // can passwords be changed?
    'modName'     => false, // can real names be changed?
    'modMail'     => false, // can emails be changed?
    'modGroups'   => false, // can groups be changed?
    'getUsers'    => false, // can a (filtered) list of users be retrieved?
    'getUserCount'=> false, // can the number of users be retrieved?
    'getGroups'   => true, // can a list of available groups be retrieved?
    'external'    => true, // does the module do external auth checking?
    'logoff'      => true, // has the module some special logoff method?
  );

  function auth_keeyaiwp() 
	{
  	$this->cando['external'] = true;
	  $this->cando['logoff'] = true;
	  $this->cando['getGroups'] = true;
  
  	$this->success = True;
  } // end function auth_keeyaiwp() {

  function logOff(){
	  wp_clear_auth_cookie();
		do_action('wp_logout');
	
  //	wp_logout();
  }
  
  
  // helper function for saving WP info into wiki session
  function saveUserInfo($wpuser)
  {
  	global $USERINFO;
  	global $conf;
  	
  	// make logininfo globally available
    $_SERVER['REMOTE_USER'] = $wpuser->user_login;
    
    $wpuser = wp_get_current_user();
    $USERINFO['name'] = $wpuser->user_login;
    $USERINFO['mail'] = $wpuser->email;
    
		// set groups --- uses the wordpress group names
		// if you have custom WP roles, you'll probably need to change this by hand :(
    // I've never needed custom roles, so if you DO, please shoot me an email with why and how
    // you are using them and I might be able to come up with a good interface for using them
    
    $groups = array($conf['defaultgroup']);	 // everyone is in the default group
    foreach($wpuser->roles as $groupname)
    	$groups[] = $groupname;
    	
		$USERINFO['grps'] = $groups;


    // set session
    $_SESSION[DOKU_COOKIE]['auth']['user'] = $wpuser->user_login;
    $_SESSION[DOKU_COOKIE]['auth']['pass'] = $wpuser->user_password;
    $_SESSION[DOKU_COOKIE]['auth']['buid'] = auth_browseruid();
    $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
    $_SESSION[DOKU_COOKIE]['auth']['time'] = time();
  	
  } // end function saveUserInfo($wpuser)
  
  function trustExternal($user,$pass,$sticky=false){
  

    if(!empty($user)){
	    //usual login
	    $wpuser = wp_signon( array('user_login'=>$user, 'user_password'=>$pass), False);
	    
	    if (! is_wp_error($wpuser) ){
	    
	    	// save info in session
	      $this->saveUserInfo($wpuser);
	    
	      return true;
	    }else{	      
				//invalid credentials - log off
	      if(!$silent) msg($lang['badlogin'],-1);
	      auth_logoff();
	      return false;
	    }
	  }else
		{
	    
	    // regular session auth check
	    if(is_user_logged_in())
	    {
	    	$wpuser = wp_get_current_user();
	    	
	    	// save info in session
	    	$this->saveUserInfo($wpuser);
	    	
	      return True;
	    }
	    
	    return False;
	    
	  }
	  //just to be sure
	  auth_logoff();
	  return false;
  
  } // end function trustExternal($user,$pass,$sticky=false)
  
	function retrieveGroups($start=0, $limit=0)
	{
		// again, will need to change this with custom groups
		return array('administrator', 'editor', 'author', 'contributor', 'subscriber');
	}
}
