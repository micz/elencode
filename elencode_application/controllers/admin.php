<?php

class Admin extends Controller {

  var $current_user;

	function __construct()
	{
		parent::Controller();	
    $this->current_user=$this->wpauth->get_user();
    if(!$this->current_user->is_admin()) redirect('','location',301);
	}
	
	function index()
	{
    $data['userdata']=$this->current_user;
		$this->load->view('admin/main',$data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */