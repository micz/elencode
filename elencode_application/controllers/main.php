<?php

class Main extends Controller {

	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
    $data['userdata']=$this->wpauth->get_user();
		$this->load->view('main',$data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */