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

	function __construct()
	{
		parent::Controller();
		$this->load->library('el/elenconfig');
    $this->current_user=$this->wpauth->get_user();
    if(!$this->current_user->is_admin()) redirect('','location',301);
    $this->load->library('el/universeconfig');
	}
	
	function index()
	{
    $data['userdata']=$this->current_user;
    $data['test_config']=$this->elenconfig->get_option('test');
    $data['test_uniconf']=$this->universeconfig->Races;
		$this->load->view('admin/main',$data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */