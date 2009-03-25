<? if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* Copyright 2009 ElenCode
* This file is part of ElenCode.
* ElenCode is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* ElenCode is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for ElenCode is available at https://github.com/micz/elencode
* Contact Mic at m [at] micz [dot] it
*
*/

class Usermodel extends Model {

    function __construct()
    {
        parent::Model();
    }

    function get_userdata_by($field,$value)
    {
      $qry='SELECT wp_users.*,wp_usermeta.meta_value AS Role FROM wp_users LEFT JOIN wp_usermeta ON wp_usermeta.user_id=wp_users.ID WHERE '.$field."='$value'".' AND wp_usermeta.meta_key=\'wp_user_level\' LIMIT 1';
      //if(!$userdata=$this->db->get_where('wp_users',array($field=>$value),1,0)){
      if(!$userdata=$this->db->query($qry)){
        return new User();
      }else{
        return new User($userdata->row());
      }
    }

    function get_userdata_by_login($username)
    {
      return $this->get_userdata_by('user_login',$username);
    }

    function get_userdata_by_id($userid)
    {
      return $this->get_userdata_by('ID',$userid);
    }
}


class User {

    var $ID;
    var $Username;
    var $Role;

    function __construct($userdata='')
    {
      if($userdata==''){
        $this->ID=0;
        $this->Username='Guest';
        $this->Role=-1;
      }else{
        $this->ID=$userdata->ID;
        $this->Username=$userdata->user_login;
        $this->Role=$userdata->Role;
      }
    }
    
    function is_admin()
    {
      return $this->Role==10;
    }
}

//    Wordpress User Roles from http://codex.wordpress.org/Roles_and_Capabilities
//    * User Level 0 converts to Subscriber Role
//    * User Level 1 converts to Contributor Role
//    * User Level 2 converts to Author Role
//    * User Level 3 converts to Author Role
//    * User Level 4 converts to Author Role
//    * User Level 5 converts to Editor Role
//    * User Level 6 converts to Editor Role
//    * User Level 7 converts to Editor Role
//    * User Level 8 converts to Administrator Role
//    * User Level 9 converts to Administrator Role
//    * User Level 10 converts to Administrator Role 
?>