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

function options_table_ajax($data,$headings,$ajax_url)
{
  if(!is_array($data))return false;

  $pid=1;
  $tr_class=1;
  $outbuffer='<table class="opt_tb"><tr>';
  foreach($headings as $hh){
    $outbuffer.='<th>'.$hh.'</th>';
  }
  $outbuffer.='</tr>';
  foreach($data as $key=>$value){
    $outbuffer.='<tr id="tr'.$pid.'" class="r'.$tr_class.'">';
    $outbuffer.='<td class="opt_tb_n">'.$key.'</td><td class="opt_tb_v"><span id="v'.$pid.'">'.$value.'</span><span id="vm'.$pid.'" class="invis"><input id="option_value_'.$pid.'" value="'.$value.'" /></span><img id="w'.$pid.'" class="invis" src="'.base_url().'graphic/img/w.gif"/></td><td class="opt_tb_b"><a id="btne'.$pid.'" onclick="javascript:tb_edit_value('.$pid.');">'.lang('common_btn_edit').'</a><span id="btnm'.$pid.'" class="invis"><a onclick="javascript:tb_confirm_mod('.$pid.',\''.$ajax_url.'\',\'action=option_edit&option_name='.$key.'&option_value=\'+$(\'#option_value_'.$pid.'\').val());">'.lang('common_btn_confirm').'</a>&nbsp;<a onclick="javascript:tb_cancel_mod('.$pid.');">'.lang('common_btn_cancel').'</a></span><span class="invis" id="wm'.$pid.'">'.lang('common_saving').'</span></td>';
    $outbuffer.='</tr>';
    $pid++;
    $tr_class=$tr_class==1?2:1;
  }
  $outbuffer.='</table>';
  return $outbuffer;
}

function options_table_ajax_update_val($htmlid,$option_value)
{
  return "$('#wm$htmlid').hide();$('#w$htmlid').hide();$('#v$htmlid').html('$option_value');$('#option_value_$htmlid').val('$option_value');$('#btne$htmlid').show();$('#v$htmlid').show();";
}

function options_table_ajax_update_val_error($htmlid)
{
  return "$('#wm$htmlid').hide();$('#w$htmlid').hide();$('#v$htmlid').html('<span class=\"error_msg\">".lang('common_error')."</span>');$('#v$htmlid').show();";
}

/*
 * This function will render an array of objects.
 * It lets the user edit the existing objects and add a new one.
 * The objects must have:
 *  - ID property to identify them (ie on a database)
 *  - add method
 *  - update method
 */
function table_ajax_from_objs_array($objs_array,$ajax_url)
{
  if(!is_array($objs_array))return false;
  $first_obj=current($objs_array);
  if(!is_object($first_obj))return false;

  $pid=1;
  $tr_class=1;
  $outbuffer='<table class="obj_tb"><tr>';
  foreach($first_obj as $key=>$value){
    $outbuffer.='<th>'.$key.'</th>';
  }
  $outbuffer.='</tr>';
  foreach($objs_array as $obj){
    if(!is_object($obj))break;
    $outbuffer.='<tr id="tr'.$pid.'" class="r'.$tr_class.'">';
    $iitem=0;
    foreach($obj as $var_name => $var_value){
      if($iitem==0){  //The first object property is ALWAYS the ID and it's our unique key
        $outbuffer.='<td class="obj_tb_e"><span>'.$var_value.'</span></td>';
      }else{
        $outbuffer.='<td class="obj_tb_e"><span id="v'.$pid.'_'.$iitem.'">'.$var_value.'</span>'.($iitem==1?'<img id="w'.$pid.'" class="invis" src="'.base_url().'graphic/img/w.gif"/>':'').'<span id="vm'.$pid.'_'.$iitem.'" class="invis"><input id="item_value_'.$pid.'_'.$iitem.'" value="'.$var_value.'" /></span></td>';
      }
      $iitem++;
    }
    $outbuffer.='<td class="opt_tb_b"><a id="btne'.$pid.'" onclick="javascript:tbobj_edit_value('.$pid.');">'.lang('common_btn_edit').'</a><span id="btnm'.$pid.'" class="invis"><a onclick="javascript:tbobj_confirm_mod('.$pid.',\''.$ajax_url.'\',\'action=object_edit&obj_type='.get_class($first_obj).'&ID='.$obj->ID.'&obj_values=\'+tbobj_get_mod_data('.$pid.'));">'.lang('common_btn_confirm').'</a>&nbsp;<a onclick="javascript:tbobj_cancel_mod('.$pid.');">'.lang('common_btn_cancel').'</a></span><span class="invis" id="wm'.$pid.'">'.lang('common_saving').'</span></td>';
    $outbuffer.='</tr>';
    $pid++;
    $tr_class=$tr_class==1?2:1;
  }
  $outbuffer.='</table>';
  return $outbuffer;
}

function table_ajax_from_objs_array_update_val($htmlid,$obj_values)
{
  $data_array=unserialize($obj_values);
  $ii=1;
  $js_str='';
  foreach ($data_array as $val){
    $val=addslashes($val);
    $js_str.="$('#v$htmlid"."_$ii').html('$val');";
    $ii++;
  }
  return $js_str."tbobj_cancel_mod($htmlid);$('#w$htmlid').hide();$('#wm$htmlid').hide();";
  //return "$('#wm$htmlid').hide();$('#w$htmlid').hide();$('#v$htmlid').html('$option_value');$('#option_value_$htmlid').val('$option_value');$('#btne$htmlid').show();$('#v$htmlid').show();";
}

function table_ajax_from_objs_array_update_val_error($htmlid)
{
  return "tbobj_cancel_mod($htmlid);$('#btne$htmlid').hide();$('#w$htmlid').hide();$('#wm$htmlid').html('<span class=\"error_msg\">".lang('common_error')."</span>');";
}
?>