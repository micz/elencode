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

class Elenconfig {

  private $CI;
  private $cachefile='elenconfig.php';
  private $wp_options_rename=array('blogname'=>'sitename');
  var $Options;
  
  function __construct($params='')
  {
    $this->Options=array();
    $this->CI=&get_instance();
    $this->CI->load->library('el/elencache');
    if(($params!='')&&($params['autoload']==1))$this->_load_options();
  }

  private function _load_options()
  {
    if($this->Options=$this->CI->elencache->load($this->cachefile)){
      return true;
    }

    //From el_config
    if(!$query=$this->CI->db->query('SELECT name,value FROM el_config WHERE autoload=1'))
      return false;
		
	  foreach ($query->result() as $row)
    {
      $this->Options[$row->name]=$row->value;
    }

    $reverse_rename=array_flip($this->wp_options_rename);

    //From wp_options
    if(!$query=$this->CI->db->query('SELECT option_name,option_value FROM wp_options WHERE option_name IN (\''.implode('\',\'',$reverse_rename).'\')'))
      return false;

	  foreach ($query->result() as $row)
    {
      $this->Options[in_array($row->option_name,$reverse_rename)?$this->wp_options_rename[$row->option_name]:$row->option_name]=$row->option_value;
    }
    
    $this->CI->elencache->save($this->cachefile,$this->Options);
    
    return true;
  }
  
  private function _get_option_db($option_name)
  {
    if(!$query=$this->CI->db->query("SELECT value,name FROM el_config WHERE name=? LIMIT 1",array($option_name)))
      return false;
		
	  $row=$query->row();
    $this->Options[$row->name]=$row->value;
    
    return $row->value;
  }
  
  function get_option($option_name)
  {
    if(array_key_exists($option_name,$this->Options)){
      return $this->Options[$option_name];
    }else{
      return $this->_get_option_db($option_name);
    }
  }

  function update_option($option_name,$option_value)
  {
    if(in_array($option_name,$this->wp_options_rename)){
      $reverse_rename=array_flip($this->wp_options_rename);
      $option_name=$reverse_rename[$option_name];
      $qry='UPDATE wp_options SET option_value=? WHERE option_name=? LIMIT 1';
    }else{
      $qry='UPDATE el_config SET value=? WHERE name=? LIMIT 1';
    }

    if($this->CI->db->query($qry,array($option_value,$option_name))){
      $this->clear_cache();
      return true;
    }else{
      return false;
    }
  }

  function clear_cache()
  {
    $this->CI->elencache->clear($this->cachefile);
  }
}
?>