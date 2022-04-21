<?php
class Erd_c extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		// $this->load->library('erd_lib');




		$this->load->database();
		$this->load->library([
			'erd_lib',
			'ion_auth',
			'form_validation',
			'table_page_lib'
		]);
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
	}

	function index($database)
	{

		$database = urldecode($database);
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		// echo "<pre>";
		$data["erd"] = $this->erd_lib->erd($database);
		$data["erd_to_db"] = $this->erd_lib->erd_to_db($database);
		$data["model_two"] = $this->erd_lib->model_two();
		$data["db_to_erd"] = $this->erd_lib->db_to_erd($database);
		$data["diff"] = $this->erd_lib->diff($database);

		foreach (json_decode($data["erd"]) as $key => $value) {
			$crud_cache = json_encode($this->table_page_lib->backend_cache_table($database, $key), JSON_PRETTY_PRINT);

			if (file_exists($this->erd_lib->erd_path($database)."/crud_cache/$key.txt")) {
				unlink($this->erd_lib->erd_path($database)."/crud_cache/$key.txt");
			}
			file_put_contents($this->erd_lib->erd_path($database)."/crud_cache/$key.txt", $crud_cache);
		}

		// $data["abilities_cache"] = json_encode($this->table_page_lib->backend_cache_table("objects"), JSON_PRETTY_PRINT);
		$data["abilities_cache"] = json_encode(array(), JSON_PRETTY_PRINT);

		// header('Content-Type: application/json');
		// echo json_encode($class);
		// exit;

		$this->load->view('erd_v', $data);
	}

}
