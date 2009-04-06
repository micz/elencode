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

function tbobj_edit_value(id)
{
  var tot=$('#tr'+id+' > td').length;
  for(i=1;i<tot;i++)
  {
    $('#v'+id+'_'+i).hide();
    $('#vm'+id+'_'+i).show();
  }
  $('#btnm'+id).show();
  $('#btne'+id).hide();
}

function tbobj_cancel_mod(id)
{
  var tot=$('#tr'+id+' > td').length;
  for(i=1;i<tot;i++)
  {
    $('#v'+id+'_'+i).show();
    $('#vm'+id+'_'+i).hide();
  }
  $('#btnm'+id).hide();
  $('#btne'+id).show();
}

function tbobj_confirm_mod(id,ajax_url,data)
{
  var tot=$('#tr'+id+' > td').length;
  for(i=1;i<tot;i++)
  {
    $('#vm'+id+'_'+i).hide();
  }
  $('#btnm'+id).hide();
  $('#wm'+id).show();
  $('#w'+id).show();
  $.ajax({url:ajax_url,type:'POST',dataType:'script',data:data+'&htmlid='+id});
}

function tbobj_get_mod_data(id)
{
  var tot=$('#tr'+id+' > td').length;
  out=new Array();
  for(i=1;i<tot;i++)
  {
    out.push($('#item_value_'+id+'_'+i).val());
  }
  return js_serialize(out);
}

function js_serialize(mixed_value)
{
// Returns a string representation of variable (which can later be unserialized)
//
// version: 812.3015
// discuss at: http://phpjs.org/functions/serialize
// +   original by: Arpad Ray (mailto:arpad@php.net)
// +   improved by: Dino
// +   bugfixed by: Andrej Pavlovic
// +   bugfixed by: Garagoth
// %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
// %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
// *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
// *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
// *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
// *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
var _getType = function( inp ) {
var type = typeof inp, match;
var key;
if (type == 'object' && !inp) {
return 'null';
}
if (type == "object") {
if (!inp.constructor) {
return 'object';
}
var cons = inp.constructor.toString();
if (match = cons.match(/(\w+)\(/)) {
cons = match[1].toLowerCase();
}
var types = ["boolean", "number", "string", "array"];
for (key in types) {
if (cons == types[key]) {
type = types[key];
break;
}
}
}
return type;
};
var type = _getType(mixed_value);
var val, ktype = '';
switch (type) {
case "function":
val = "";
break;
case "undefined":
val = "N";
break;
case "boolean":
val = "b:" + (mixed_value ? "1" : "0");
break;
case "number":
val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
break;
case "string":
val = "s:" + mixed_value.length + ":\"" + mixed_value + "\"";
break;
case "array":
case "object":
val = "a";
/*
if (type == "object") {
var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
if (objname == undefined) {
return;
}
objname[1] = serialize(objname[1]);
val = "O" + objname[1].substring(1, objname[1].length - 1);
}
*/
var count = 0;
var vals = "";
var okey;
var key;
for (key in mixed_value) {
ktype = _getType(mixed_value[key]);
if (ktype == "function") {
continue;
}
okey = (key.match(/^[0-9]+$/) ? parseInt(key) : key);
vals += js_serialize(okey) +
js_serialize(mixed_value[key]);
count++;
}
val += ":" + count + ":{" + vals + "}";
break;
}
if (type != "object" && type != "array") val += ";";
return val;
}