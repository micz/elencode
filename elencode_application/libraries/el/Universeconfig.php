<? if (!defined('BASEPATH')) exit('No direct script access allowed');
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

class Universeconfig {

  private $CI;
  var $Skills;
  var $Abilities;
  var $Classes;
  var $Races;
  var $Options;
  private $races_classes_array;
  private $options_cachefile='universe_opt.php';
  
  function __construct()
  {
    $this->Names=array();
    $this->CI=&get_instance();
    $this->CI->load->library('el/elencache');
    $this->CI->load->model('el/racemodel');
    $this->CI->load->model('el/eclassmodel');
    $this->CI->load->model('el/skillmodel');
    $this->CI->load->model('el/abilitymodel');
    $this->_load_all();
    $races_classes_array='';
  }

  private function _load_all()
  {
    $this->_load_skills();
    $this->_load_abilities();
    $this->_load_classes();
    $this->_load_races();
    $this->_load_options();
  }

  private function _load_options()
  {
    if($this->Options=$this->CI->elencache->load($this->options_cachefile)){
      return true;
    }else{
      $this->Options=array();
    }

    if(!$query=$this->CI->db->query('SELECT name,value FROM el_config WHERE name IN (\'skills_dice\',\'abilities_step\',\'race_class\',\'class_skill\',\'skill_cost\',\'ability_first_points\')'))
		return false;

	  foreach ($query->result() as $row)
    {
      $this->Options[$row->name]=$row->value;
    }

    $this->CI->elencache->save($this->options_cachefile,$this->Options,'unicache');
    return true;
  }

  private function _load_skills()
  {
    $this->Skills=$this->CI->skillmodel->get_all();
  }

  private function _load_abilities()
  {
    $this->Abilities=$this->CI->abilitymodel->get_all();
  }

  private function _load_classes()
  {
    $this->Classes=$this->CI->eclassmodel->get_all();
  }

  private function _load_races()
  {
    $this->Races=$this->CI->racemodel->get_all();
  }

  function get_race_class($race_id,$class_id)
  {
    if(!is_array($this->races_classes_array))$this->races_classes_array=unserialize($this->Options['race_class']);

    if(!key_exists($race_id,$this->races_classes_array)){
      $this->races_classes_array[$race_id]=array();
    }
    if(!key_exists($class_id,$this->races_classes_array[$race_id])){
      $this->races_classes_array[$race_id][$class_id]=0;
    }

    return $this->races_classes_array[$race_id][$class_id];
  }

  function save_option($option_name,$option_value)
  {
    $qry='UPDATE el_config SET value=? WHERE name=? LIMIT 1';
    
    if($this->CI->db->query($qry,array($option_value,$option_name))){
      $this->clear_option_cache();
      return true;
    }else{
      return false;
    }
  }

  function clear_option_cache()
  {
    $this->CI->elencache->clear($this->options_cachefile);
  }
}
?>