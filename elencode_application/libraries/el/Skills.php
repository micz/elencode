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

class Skills {

  var $CI;
  var $Names;
  
  function __construct()
  {
    $this->Names=array();
    $this->CI=&get_instance();
    $this->CI->load->library('el/elencache');
    $this->_load_names();
  }

  private function _load_names()
  {
    if($this->Names=$this->CI->elencache->load('skills.php')){
      return true;
    }else{
      $this->Names=array();
    }

    if(!$query=$this->CI->db->query('SELECT id,name FROM el_skills ORDER BY id ASC'))
      return false;
		
	  foreach ($query->result() as $row)
    {
      $this->Names[$row->id]=$row->name;
    }
    
    $this->CI->elencache->save('skills.php',$this->Names);
    
    return true;
  }
}
?>