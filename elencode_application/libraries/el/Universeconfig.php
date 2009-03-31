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

class Universeconfig {

  var $CI;
  var $Skills;
  var $Abilities;
  var $Classes;
  var $Races;
  
  function __construct()
  {
    $this->Names=array();
    $this->CI=&get_instance();
    $this->CI->load->library('el/elencache');
    $this->CI->load->model('el/racemodel');
    $this->CI->load->model('el/eclassmodel');
    $this->CI->load->model('el/skillmodel');
    $this->_load_all();
  }

  private function _load_all()
  {
    $this->_load_skills();
    $this->_load_abilities();
    $this->_load_classes();
    $this->_load_races();
  }

  private function _load_skills()
  {
    if($this->Skills=$this->CI->elencache->load('skills.php',1)){
      return true;
    }else{
      $this->Skills=array();
    }

    if(!$query=$this->CI->db->query('SELECT * FROM el_skills ORDER BY id ASC'))
      return false;
		
	  foreach ($query->result() as $row)
    {
      $this->Skills[$row->id]=new Skill($row);
    }
    
    $this->CI->elencache->save('skills.php',$this->Skills,'skills_values',1);
    
    return true;
  }

  private function _load_abilities()
  {
    if($this->Abilities=$this->CI->elencache->load('abilities.php')){
      return true;
    }else{
      $this->Abilities=array();
    }

    if(!$query=$this->CI->db->query('SELECT * FROM el_abilities ORDER BY id ASC'))
      return false;

	  foreach ($query->result() as $row)
    {
      $this->Abilities[$row->id]=$row->name;
    }

    $this->CI->elencache->save('abilities.php',$this->Abilities,'abilities_values');

    return true;
  }

  private function _load_classes()
  {
    if($this->Classes=$this->CI->elencache->load('classes.php',1)){
      return true;
    }else{
      $this->Classes=array();
    }

    if(!$query=$this->CI->db->query('SELECT * FROM el_classes ORDER BY id ASC'))
      return false;

	  foreach ($query->result() as $row)
    {
      $this->Classes[$row->id]=new EClass($row);
    }

    $this->CI->elencache->save('classes.php',$this->Classes,'classes_values',1);

    return true;
  }

  private function _load_races()
  {
    if($this->Races=$this->CI->elencache->load('races.php',1)){
      return true;
    }else{
      $this->Races=array();
    }

    if(!$query=$this->CI->db->query('SELECT * FROM el_races ORDER BY id ASC'))
      return false;

	  foreach ($query->result() as $row)
    {
      $this->Races[$row->id]=new Race($row);
    }

    $this->CI->elencache->save('races.php',$this->Races,'races_values',1);

    return true;
  }
}
?>