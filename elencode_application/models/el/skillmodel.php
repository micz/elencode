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

class Skillmodel extends Model {

    private $cachefile='skills.php';

    function __construct()
    {
        parent::Model();
        $this->load->library('el/elencache');
    }

    function get_all()
    {
      if($outbuffer=$this->elencache->load($this->cachefile,1)){
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

      $this->elencache->save($this->cachefile,$outbuffer,'skills_values',1);

      return $outbuffer;
    }

    function clear_cache()
    {
      $this->elencache->clear($this->cachefile);
    }

    function update(Skill $item)
    {
      $data_array=array();
      $update_str='';
      foreach($item as $var_name => $var_value){
        if($var_name!='ID'){
          array_push($data_array,$var_value);
          $update_str.=$var_name.'=?,';
        }
      }
      $update_str=trim($update_str,',');
      array_push($data_array,$item->ID);
      if($this->db->query('UPDATE el_skills SET '.$update_str.' WHERE ID=?',$data_array)){
        $this->clear_cache();
        return true;
      }else{
        return false;
      }
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

    static function get_from_serialized_data($ID,$serdata)
    {
      $outobj=new Skill();
      $outobj->ID=$ID;
      $arrdata=unserialize($serdata);
      $outobj->name=$arrdata[0];
      $outobj->ability_id=intval($arrdata[1]);
      return $outobj;
    }
}
?>