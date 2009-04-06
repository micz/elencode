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

class Skillmodel extends Model {

    function __construct()
    {
        parent::Model();
        $this->load->library('el/elencache');
    }

    function get_all()
    {
      if($outbuffer=$this->elencache->load('skills.php',1)){
        return $outbuffer;
      }else{
        $outbuffer=array();
      }

      if(!$query=$this->db->query('SELECT * FROM el_skills ORDER BY id ASC'))
        return false;

      foreach ($query->result() as $row)
      {
        $outbuffer[$row->id]=new Skill($row);
      }

      $this->elencache->save('skills.php',$outbuffer,'skills_values',1);

      return $outbuffer;
    }
}

class Skill {

    var $ID;
    var $name;
    var $ability_id;

    function __construct($rowdata='')
    {
      if($rowdata==''){
        $this->ID=-1;
        $this->name='';
        $this->ability_id=-1;
      }else{
        $this->ID=$rowdata->id;
        $this->name=$rowdata->name;
        $this->ability_id=$rowdata->ability_id;
      }
    }

}
?>