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

class Elencache {

  var $CI;
  var $cache_dir;
  
  function __construct()
  {
    $this->CI=&get_instance();
    $this->cache_dir=BASEPATH.'../cache/';
  }

  function save($filename,$values,$arrayname='array_value',$from_object=0)
  {
      $buffer='<?if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'."\r".'$arrayname='."'$arrayname';\r";
      $filepath=$this->cache_dir.$filename;
      array_walk($values,'filter_cached_array_save',$from_object);
      foreach ($values as $key => $value)
      {
        $buffer.='$'.$arrayname.'[\''.$key.'\']=\''.$value.'\';'."\r";
      }
      
      $buffer.='?>';
      return $this->_write_file($filepath,$buffer);
  }

  function load($filename,$to_object=0)
  {
    if($this->is_cached($filename)){
      require_once($this->cache_dir.$filename);
      array_walk($$arrayname,'filter_cached_array_load',$to_object);
      return $$arrayname;
    }else{
      return false;
    }
  }

  function is_cached($filename)
  {
    return file_exists($this->cache_dir.$filename);
  }

  function _write_file($file_out,$outputcache){
    if(($file_out=='')||($outputcache==''))return false;
    $fp = @fopen($file_out,'w');
    @fwrite($fp,$outputcache);
    @fclose($fp);
    return true;
  }
}

function filter_cached_array_save(&$item,$key,$from_object)
{
  if($from_object)$item=serialize($item);
  $item=addslashes($item);
}

function filter_cached_array_load(&$item,$key,$to_object)
{
  $item=stripslashes($item);
  if($to_object)$item=unserialize($item);
}
?>