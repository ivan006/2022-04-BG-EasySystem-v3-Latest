<?php
class Record_c extends CI_Controller
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
		$this->load->library('ssp');




		// $this->load->library([
		// 	'ion_auth',
		// 	'form_validation',
		// 	'table_page_lib',
		// 	'erd_lib',
		// 	'ssp_class',
		// ]);


		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
	}

	public function index($database, $table, $record_id)
	{
		$database = urldecode($database);
		$table = urldecode($table);
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		// $data = $this->table_page_lib->record_abilities($table, $record_id);
		$data = $this->table_page_lib->frontend_data_for_record_view($database, $table, $record_id);
		// header('Content-Type: application/json');
		// echo json_encode($data, JSON_PRETTY_PRINT);
		// exit;





		$permisssion_options = array(
			// "owner" => array(
			// 	"assumed" => 2,
			// 	"options" => $this->table_page_lib->active_groups_dropdown()
			// ),
			"owner" => $this->table_page_lib->active_groups_dropdown(),
			// "assignbility" => array(
			// 	"assumed" => "",
			// 	"options" => array(
			// 		"Private",
			// 		// "Organisation",
			// 		"Public"
			// 	)
			// ),
			"editability" => array(
				"assumed" => "",
				"options" => array(
					array(
						"label"=>"Private",
						"value"=>"pr",
					),
					// array(
					// 	"label"="Organisation",
					// 	"value"="1",
					// ),
					array(
						"label"=>"Public",
						"value"=>"pu",
					),
				)
			),
			"visibility" => array(
				"assumed" => "",
				"options" => array(
					array(
						"label"=>"Private",
						"value"=>"pr",
					),
					// array(
					// 	"label"="Organisation",
					// 	"value"="1",
					// ),
					array(
						"label"=>"Public",
						"value"=>"pu",
					),
				)
			),
		);
		// echo $record;
		if ($data["table_exists"] == 1) {




			$this->load->view('table_header_v', array(
				"data"=>$data,
				"type"=>"g_record_core_abilities",
				"database"=>$database,
				"active_groups_dropdown"=>$this->table_page_lib->active_groups_dropdown(),
			));
			$this->load->view('table_block_v', array(
				"data"=>$data["g_core_abilities"],
				"permisssion_options"=>$permisssion_options,
				"type"=>"g_record_core_abilities",
				"database"=>$database,
			));


			if (!empty($data["g_parental_abilities"])) {
				// $this->load->view('blank_v', array("data"=>'<div class="row"><div class="col-md-12 mt-5"><h2 class="text-center">items</h2><hr style="background-color: black; color: black; height: 1px;"></div></div>'));
				$this->load->view('blank_v', array("data"=>'<div class="row"><div class="col-md-12 mt-5"><h2 class="text-center">items</h2></div></div>'));
			}
			foreach ($data["g_parental_abilities"] as $key => $value) {
				if (!empty($value)) {
					// code...
					$this->load->view('table_block_v', array(
						"data"=>$value,
						"permisssion_options"=>$permisssion_options,
						"type"=>"g_record_parental_abilities"
					));
				}
			}
			$this->load->view('table_footer_v', array("data"=>$data));

		} else {
			echo "No such record.";
		}


	}




}
