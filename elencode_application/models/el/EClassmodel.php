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

class EClassmodel extends Model {

    function __construct()
    {
        parent::Model();
        $this->load->library('el/elencache');
    }

    function get_all()
    {
      if($outbuffer=$this->elencache->load('classes.php',1)){
        return $outbuffer;
      }else{
        $outbuffer=array();
      }

      if(!$query=$this->db->query('SELECT * FROM el_classes ORDER BY id ASC'))
        return false;

      foreach ($query->result() as $row)
      {
        $outbuffer[$row->id]=new EClass($row);
      }

      $this->elencache->save('classes.php',$outbuffer,'classes_values',1);

      return $outbuffer;
    }
}

class EClass {

    var $ID;
    var $male_name;
    var $male_name_art;
    var $female_name;
    var $female_name_art;

    function __construct($rowdata='')
    {
      if($rowdata==''){
        $this->ID=-1;
        $this->male_name='';
        $this->male_name_art='';
        $this->female_name='';
        $this->female_name_art='';
      }else{
        $this->ID=$rowdata->id;
        $this->male_name=$rowdata->male_name;
        $this->male_name_art=$rowdata->male_name_art;
        $this->female_name=$rowdata->female_name;
        $this->female_name_art=$rowdata->female_name_art;
      }
    }

}
?>