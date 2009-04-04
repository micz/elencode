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

function tb_edit_value(id)
{
  $('#v'+id).hide();
  $('#vm'+id).show();
  $('#btnm'+id).show();
  $('#btne'+id).hide();
}

function tb_cancel_mod(id)
{
  $('#v'+id).show();
  $('#vm'+id).hide();
  $('#btnm'+id).hide();
  $('#btne'+id).show();
}

function tb_confirm_mod(id,ajax_url,data)
{
  $('#vm'+id).hide();
  $('#btnm'+id).hide();
  $('#wm'+id).show();
  $('#w'+id).show();
  $.ajax({url:ajax_url,type:'POST',dataType:'script',data:data+'&htmlid='+id});
}