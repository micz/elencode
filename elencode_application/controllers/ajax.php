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

define('DOING_AJAX',1);

class Ajax extends Controller {

  var $current_user;

	function __construct()
	{
		parent::Controller();
    $this->current_user=$this->wpauth->get_user();
    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
    $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
    $this->output->set_header("Pragma: no-cache");
    $this->load->library('el/elenconfig',array('autoload'=>1));
	}

	function index()
	{
    $this->output->set_output('');
	}

  function admin()
	{
    $this->lang->load('admin',$this->elenconfig->Options['language']);

    if(!$this->current_user->is_admin()){
      $this->output->set_status_header('401');
      $this->output->set_output('');
    }

    $this->load->library('el/elenconfig');
    $this->load->helper('admin_html');

    $out_buffer='';
    $htmlid=$this->input->post('htmlid');

    switch($this->input->post('action')){
      case 'option_edit':
        $option_value=$this->input->post('option_value');
        if($this->elenconfig->update_option($this->input->post('option_name'),$option_value)){
          $out_buffer.=options_table_ajax_update_val($htmlid,$option_value);
        }else{
          $out_buffer.=options_table_ajax_update_val_error($htmlid);
        }
        break;
      case 'object_edit':
        $obj_id=$this->input->post('ID');
        $obj_values=$this->input->post('obj_values');
        $obj_type=$this->input->post('obj_type');
        // Security check -> no serialized objects in $obj_value
        if(serialized_has_objects($obj_values)){
          $out_buffer.=table_ajax_from_objs_array_update_val_error($htmlid);
          break;
        }
        $this->load->model('el/'.strtolower($obj_type).'model');
        $item=call_user_func($obj_type.'::get_from_serialized_data',$obj_id,$obj_values);
        if($this->{strtolower($obj_type).'model'}->{'update'}($item))
        {
          $out_buffer.=table_ajax_from_objs_array_update_val($htmlid,$obj_values);
        }else{
          $out_buffer.=table_ajax_from_objs_array_update_val_error($htmlid);
        }
        break;
    }

    $this->output->set_output($out_buffer);
  }
}
?>