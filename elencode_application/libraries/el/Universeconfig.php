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

  private $CI;
  var $Skills;
  var $Abilities;
  var $Classes;
  var $Races;
  var $Options;
  
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
    if($this->Options=$this->CI->elencache->load('universe_opt.php')){
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

    $this->CI->elencache->save('universe_opt.php',$this->Options,'unicache');
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
}
?>