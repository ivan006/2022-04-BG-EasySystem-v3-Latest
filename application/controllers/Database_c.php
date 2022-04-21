<?php
class Database_c extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		// $this->load->model('trip');
		// $this->load->library('../modules/trips/controllers/table_page_lib');
		$this->load->library('table_page_lib');
		$this->load->library('erd_lib');


		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');

	}

	public function database($database)
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		$database = urldecode($database);
		$data['tables'] = $this->table_page_lib->database_api($database);




		$data["table_details"]["cols"]["visible"] = array("name"=>array());
		$data["overview"]["table_id"] = "";
		$data["data_endpoint"] = "database_api/d/".$database;
		$data['title'] = "Database";
		$this->load->view('table_header_v', array(
			"data"=>$data,
			// "database"=>$database,
			"type"=>"g_database_core_abilities",
			"active_groups_dropdown"=>$this->table_page_lib->active_groups_dropdown(),
		));
		$this->load->view('table_block_readonly_v', array("data"=>$data,"link_prefix"=>"/table/d/$database/t/"));
		$this->load->view('table_footer_v');

	}

	public function database_api($database)
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		$database = urldecode($database);

		$data = $this->table_page_lib->database_api($database);
		header('Content-Type: application/json');
		echo json_encode($data, JSON_PRETTY_PRINT);

	}

	public function databases()
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		$data['tables'] = $this->table_page_lib->databases_api();




		$data["table_details"]["cols"]["visible"] = array("name"=>array());
		$data["overview"]["table_id"] = "";
		$data["data_endpoint"] = "databases_api";
		$data['title'] = "Databases";
		$this->load->view('table_header_v', array(
			"data"=>$data,
			// "database"=>$database,
			"type"=>"g_database_core_abilities",
			"active_groups_dropdown"=>$this->table_page_lib->active_groups_dropdown(),
		));
		$this->load->view('table_block_readonly_v', array("data"=>$data,"link_prefix"=>"/database/d/"));
		$this->load->view('table_footer_v');

	}

	public function databases_api()
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}


		$data = $this->table_page_lib->databases_api();
		header('Content-Type: application/json');
		echo json_encode($data, JSON_PRETTY_PRINT);

	}

}
