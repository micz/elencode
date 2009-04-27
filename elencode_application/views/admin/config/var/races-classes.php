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

$outbuffer='<table id="rctable"><tbody><tr><th>&nbsp;</th>';

$colspan_l=1;
foreach($universe->Races as $race){
  $outbuffer.='<th>'.$race->male_name.'</th>';
  $colspan_l++;
}
$outbuffer.='</tr>';

foreach($universe->Classes as $eclass){
  $outbuffer.='<tr><th>'.$eclass->male_name.'</th>';
  foreach($universe->Races as $race){
    $outbuffer.='<td><input onchange="javascript:$(\'#savebtn\').attr(\'disabled\',\'\');" type="checkbox" id="r'.$race->ID.'c'.$eclass->ID.'" value="1"'.($universe->get_race_class($race->ID,$eclass->ID)==0?'':' checked="checked"').'/></td>';
  }
  $outbuffer.='</tr>';
}

$outbuffer.='<tr><td colspan="'.$colspan_l.'" align="right"><input id="savebtn" type="button" value="'.lang('common_save').'" disabled="disabled" onclick="javascript:rcsave(\''.base_url().'ajax/admin/\');"><br/><span class="invis" id="svmsg">'.lang('common_saving').'</span></td></tr></tbody></table>';
echo $outbuffer;
?>