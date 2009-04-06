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

class Admin extends Controller {

  var $current_user;
  private $data;

	function __construct()
	{
		parent::Controller();
		$this->load->helper('admin_html');
    $this->load->library('el/elenconfig',array('autoload'=>1));
    $this->lang->load('admin',$this->elenconfig->Options['language']);
    $this->current_user=$this->wpauth->get_user();
    if(!$this->current_user->is_admin()) redirect('','location',301);
    $this->data['userdata']=$this->current_user;
    $this->data['sitename']=$this->elenconfig->Options['sitename'];
    $this->data['sectiontitle']='';
	}
	
	function index()
	{
    $this->data['main_content']='admin/'.__FUNCTION__;
		$this->load->view('admin/main',$this->data);
	}

  function config()
	{
    $this->data['main_content']='admin/'.__FUNCTION__;
    $this->data['form_type']=$this->uri->segment(3,'general');
    switch($this->data['form_type']){
      case 'general':
        $this->data['sectiontitle']=' :: '.lang('admin_menu_general_option');
        $this->data['general_data']=$this->elenconfig->Options;
        break;
      case 'universe':
        $this->data['sectiontitle']=' :: '.lang('admin_menu_universe_option');
        $this->load->library('el/universeconfig');
        break;
    }
		$this->load->view('admin/main',$this->data);
	}
}
?>