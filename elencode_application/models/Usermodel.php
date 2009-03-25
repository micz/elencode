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
    
    function get_userdata_by_login($username)
    {
      if(!$userdata=$this->db->get_where('wp_users',array('user_login'=>$username),1,0)){
        return new User();
      }else{
        return new User($userdata->row());
      }
    }
}


class User {

    var $ID;
    var $Username;

    function __construct($userdata='')
    {
      if($userdata==''){
        $this->ID=0;
        $this->Username='Guest';
      }else{
        $this->ID=$userdata->ID;
        $this->Username=$userdata->user_login;
      }
    }    
}
?>