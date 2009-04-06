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
    $outbuffer.='<td class="opt_tb_n">'.$key.'</td><td class="opt_tb_v"><span id="v'.$pid.'">'.$value.'</span><span id="vm'.$pid.'" class="invis"><input id="option_value_'.$pid.'" value="'.$value.'" /></span><img id="w'.$pid.'" class="invis" src="'.base_url().'graphic/img/w.gif"/></td><td class="opt_tb_b"><a id="btne'.$pid.'" onclick="javascript:tb_edit_value('.$pid.');">'.lang('admin_btn_edit').'</a><span id="btnm'.$pid.'" class="invis"><a onclick="javascript:tb_confirm_mod('.$pid.',\''.$ajax_url.'\',\'action=option_edit&option_name='.$key.'&option_value=\'+$(\'#option_value_'.$pid.'\').val());">'.lang('admin_btn_confirm').'</a>&nbsp;<a onclick="javascript:tb_cancel_mod('.$pid.');">'.lang('admin_btn_cancel').'</a></span><span class="invis" id="wm'.$pid.'">'.lang('admin_saving').'</span></td>';
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
  return "$('#wm$htmlid').hide();$('#w$htmlid').hide();$('#v$htmlid').html('<span class=\"error_msg\">".lang('admin_error')."</span>');$('#v$htmlid').show();";
}
?>