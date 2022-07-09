<?php
class Table_page_lib
{
	private $CI;

	function __construct()
	{
		// parent::__construct();

		// $this->load->helper(array('form', 'url'));
		// $this->load->library('form_validation');
		// $this->load->library('erd_lib');

		$this->CI =& get_instance();
		//
		$this->CI->load->helper(array('form', 'url'));
		$this->CI->load->library(
			'form_validation',
			'erd_lib',
			'input',
			'ion_auth',
			'session',
			'ssp'
		);


	}

	// public function fetch_without_inheritance($table)
	// {
	//
	// 	$table = urldecode($table);
	// 	$this->CI->load->database();
	//
	//
	// 	// if ($this->CI->input->is_ajax_request()) {
	// 	// // if ($posts = $this->CI->db->get($table)->result()) {
	// 	// // 	$data = array('responce' => 'success', 'posts' => $posts);
	// 	// // }else{
	// 	// // 	$data = array('responce' => 'error', 'message' => 'Failed to fetch data');
	// 	// // }
	// 	// echo $table;
	//
	//
	// 	$this->CI->db->_protect_identifiers=false;
	// 	$posts = $this->CI->db->get("`".$table."`")->result();
	// 	$data = array('responce' => 'success', 'posts' => $posts);
	// 	$this->CI->db->_protect_identifiers=true;
	// 	return $data;
	//
	// 	// } else {
	// 	// 	return "No direct script access allowed";
	// 	// }
	//
	//
	//
	// }

	public function edit($database, $table)
	{

		$this->CI->load->database();

		$table = urldecode($table);

		// if ($this->CI->input->is_ajax_request()) {
		$edit_id = $this->CI->input->post('edit_id');

		// edit me start 1
		// $this->CI->db->select("*");
		// $this->CI->db->from($table);
		// $this->CI->db->where("id", $edit_id);
		// $query = $this->CI->db->get();

		// edit me start 2

		if (1==1) {
			$erd_two_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
			$erd_two = file_get_contents($erd_two_path);
			$erd_two = json_decode($erd_two, true);

			$fields = $erd_two[$table]["fields"];

			$this->CI->db->_protect_identifiers=false;


			$QueryA = "";
			$QueryA = $QueryA."SELECT
			`record_table_and_id`,
			`timestamp`,
			`owner`,
			`editability`,
			`visibility`,";

			$iteration = 0;
			foreach ($fields as $field_key => $field_value) {
				if ($iteration > 0) {
					$QueryA = $QueryA.",";
				}
				$QueryA = $QueryA." `$table`.`$field_key`";
				$iteration = $iteration+1;
			}

			$QueryA = $QueryA." FROM `_activity_log` as `_activity_log`
			RIGHT JOIN (SELECT * FROM `$table` WHERE `id` = '$edit_id')
			AS `$table` ON `_activity_log`.`record_table_and_id` = CONCAT('$table', '/', `$table`.id)";

			$query = $this->CI->db->query($QueryA);

			$this->CI->db->_protect_identifiers=true;
		}

		// edit me end
		$post = null;
		if (count($query->result()) > 0) {
			$variables = (array) $query->row();
			// unset();
			// var_dump($variables);
			unset($variables["record_table_and_id"]);
			unset($variables["owner"]);
			unset($variables["editability"]);
			unset($variables["visibility"]);
			unset($variables["timestamp"]);
			$post["variables"] = $variables;
			$post["permissions"] = array(
				"permissions_owner" => $query->row()->owner,
				"permissions_editability" => $query->row()->editability,
				"permissions_visibility" => $query->row()->visibility,
			);
		}
		if ($post) {
			$data = array('responce' => 'success', 'post' => $post);
		} else {
			$data = array('responce' => 'error', 'message' => 'failed to fetch record');
		}
		return $data;
		// } else {
		// 	return "No direct script access allowed";
		// }
	}

	public function table_rows($database, $table)
	{

		// $this->CI->load->database();
		//
		//
		// $row_query = array(
		// "SHOW COLUMNS FROM `$table`",
		// );
		// $row_query = implode(" ", $row_query);
		// $rows = $this->CI->db->query($row_query)->result_array();
		// $rows = array_column($rows, 'Field');

		if (!file_exists($this->CI->erd_lib->erd_path($database)."/crud_cache/$table.txt")) {
			return array();
		}
		$data = file_get_contents($this->CI->erd_lib->erd_path($database)."/crud_cache/$table.txt");
		$data = json_decode($data, true);

		$result = array();
		foreach ($data["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
			$result[$key] = array();
		}
		// header('Content-Type: application/json');
		// echo json_encode($result, JSON_PRETTY_PRINT);
		// exit;



		return $result;
	}

	// public function fetch_for_record($table, $haystack, $needle, $child_of)
	public function fetch($database, $table, $page_type, $where, $groups)
	{
		$active_groups_dropdown = $this->active_groups_dropdown();
		$active_groups_dropdown = $active_groups_dropdown["assumed"];
		if ($active_groups_dropdown == "") {
			$active_groups_dropdown = 2;
		}



		$table = urldecode($table);

		$this->CI->load->database();

		// $posts = $this->CI->db->where($where["haystack"], $where["needle"])->get($table)->result_array();

		$erd_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
		$erd = file_get_contents($erd_path);
		$erd = json_decode($erd, true);

		if ($page_type == "record") {
			$where["haystack_type"] = urldecode($where["haystack_type"]);
			if ($where["haystack_type"] == "foreign_key") {
				$foreign_key = $where["haystack"];
			} else {
				$foreign_key = null;
			}
			$cols_visible = $this->backend_cache_columns(
				$table,
				$erd,
				$foreign_key
			);
			// $cols_visible = $this->backend_cache_columns($table, $erd, "");
		}
		elseif ($page_type == "table") {
			$cols_visible = $this->backend_cache_columns($table, $erd, null);
		}

		// header('Content-Type: application/json');
		// echo json_encode($cols_visible, JSON_PRETTY_PRINT);
		// exit;






		$this->CI->db->_protect_identifiers=false;
		$query = $this->CI->db;


		if ("old"=="old") {
			// code...

			foreach ($cols_visible["nonlinking_cols"] as $key => $value) {
				$query = $query->select("`".$table."`".'.'."`".$key."`");
				// if ($key == "id") {
				// 	// code...
				// 	$parent_link_part_1 = '<a href="/record/t/'.$table.'/r/';
				// 	$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
				// 	$query = $query->select("CONCAT('$parent_link_part_1', "."`".$table."`".".id, '$parent_link_part_2') as `id`");
				// }

			}
			foreach ($cols_visible["linking_cols"] as $key => $value) {
				// if ($key !== $table) {
				foreach ($value["cols"] as $key_2 => $value_2) {
					if ($key_2 == "id") {
						$parent_link_part_1 = '<a href="/record/t/'.$key.'/r/';
						$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
						$query = $query->select("CONCAT('$parent_link_part_1', "."`joining_table_".$key."`".".id".", '$parent_link_part_2') as `$key - $key_2`");
					} else {
						$query = $query->select("`joining_table_".$key."`"."."."`".$key_2."`"." as `$key - $key_2`");
					}


				}
				if (isset($value["is_self_joined"])) {
					// $g_select["visible"] = array_merge(
					// 	$g_select["visible"],
					// 	array("$key - breadcrumbs" => "1")
					// );

					$query = $query->select("`joining_table_".$key."_breadcrumbs`.path"." as `$key - breadcrumbs`");

					// $concat_part_1 = "`joining_table_".$key."_breadcrumbs`.path";
					// $concat_part_2 = "`$table`.`id`";
					// $query = $query->select("CONCAT($concat_part_1, "."'-'".", $concat_part_2) as `$key - breadcrumbs`");
					// // $query = $query->select("CONCAT('0-', $concat_part_1, '-', $concat_part_2) as `$key - breadcrumbs`");
				}
				// }
			}
			$query = $query->from("`".$table."`");

			foreach ($cols_visible["linking_cols"] as $key => $value) {
				// echo "xyz";
				// if ($key !== $table) {
				$query = $query->join("`".$key."` as `joining_table_".$key."`", "`".$table."`".'.'."`".$value["linking_key"]."`".' = '."`joining_table_".$key."`".'.id', 'left');
				// }

				if (isset($value["is_self_joined"])) {
					$linking_key = $value["linking_key"];
					// $sql="
					// ";
					$sql = array(
						"WITH RECURSIVE q AS (",
						// "SELECT  id,`$linking_key`, CONCAT('0-', id) as path",
						"SELECT  id,`$linking_key`, CONCAT('', id) as path",
						// "SELECT  id as id,`$linking_key`, id as path",
						"FROM    `$key`",
						"WHERE   `$linking_key` = 0",
						"UNION ALL",
						"SELECT  m.id,m.`$linking_key`, CONCAT(q.path, '-', m.id) as path",
						"FROM    `$key` m",
						"JOIN    q",
						"ON      m.`$linking_key` = q.id)",
						"SELECT  *",
						// "SELECT  id,`$linking_key`, CONCAT('0-',path, '-', id) as path",
						"FROM    q",

					);
					$sql = implode("\n\r", $sql);
					// $query = $query->join("(".$sql.") as `joining_table_".$key."_breadcrumbs`", "`".$table."`".'.'."`$linking_key`".' = '."`joining_table_".$key."_breadcrumbs`".'.id', 'left');
					$query = $query->join("(".$sql.") as `joining_table_".$key."_breadcrumbs`", "`".$table."`".'.'."`id`".' = '."`joining_table_".$key."_breadcrumbs`".'.id', 'left');
				}
			}
			$query = $query->join("`_activity_log`", "`_activity_log`.`record_table_and_id` = CONCAT('$table', '/', `$table`.id)", 'left');

			// echo json_encode($groups);
			// exit;
			$query = $query->group_start();
			// $query = $query->or_where("`_activity_log`.`owner` =", "0");

			$query = $query->where("`_activity_log`.`editability` =", "'pu'");
			$query = $query->or_where("`_activity_log`.`visibility` =", "'pu'");
			$query = $query->or_where("`_activity_log`.`editability` IS", "NULL");
			$query = $query->or_where("`_activity_log`.`visibility` IS", "NULL");
			$query = $query->or_where("`_activity_log`.`owner` =", "2");




			if (!empty($groups)) {
				// code...
				$query = $query->or_where_in("`_activity_log`.`owner`", $groups);
			}

			$query = $query->where("`_activity_log`.`owner` =", $active_groups_dropdown);

			$query = $query->group_end();
			// $query = $query->or_where_in("`_activity_log`.`editability`", array("'Public'", "''"));
			// $query = $query->or_where_in("`_activity_log`.`visibility`", array("'Public'", "''"));

			if ($page_type == "record") {
				$where["haystack"] = urldecode($where["haystack"]);

				// echo "`".$table."`"."."."`".$where["haystack"]."` =". '"'.$where["needle"].'"';
				// exit;
				$query = $query->where("`".$table."`"."."."`".$where["haystack"]."` =", '"'.$where["needle"].'"');
			}
			elseif ($page_type == "table") {
			}



			// $sql = $query->_compile_select();
			// echo $sql;
			// exit;
			$query = $query->get();

		}



		$posts = $query;
		$posts = $posts->result_array();

		// print_r($this->CI->db->last_query());
		// exit;


		$this->CI->db->_protect_identifiers=true;






		$data = array('responce' => 'success', 'posts' => $posts);

		// header('Content-Type: application/json');
		// echo $this->CI->db->last_query();
		// exit;
		return $data;
	}

	public function fetch_ssp($database, $table, $page_type, $where, $groups)
	{
		if ("old"=="!old") {

			$active_groups_dropdown = $this->active_groups_dropdown();
			$active_groups_dropdown = $active_groups_dropdown["assumed"];
			if ($active_groups_dropdown == "") {
				$active_groups_dropdown = 2;
			}



			$table = urldecode($table);

			$this->CI->load->database();

			// $posts = $this->CI->db->where($where["haystack"], $where["needle"])->get($table)->result_array();




			$this->CI->db->_protect_identifiers=false;
			$query = $this->CI->db;


			if ("old"=="old") {
				// code...
				if ("old"=="!old") {
					// TODO: use frontend_data_for_table_view or frontend_data_for_record_view not backend_cache_columns

					if (1==1) {
						$erd_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
						$erd = file_get_contents($erd_path);
						$erd = json_decode($erd, true);

						if ($page_type == "record") {
							$where["haystack_type"] = urldecode($where["haystack_type"]);
							if ($where["haystack_type"] == "foreign_key") {
								$foreign_key = $where["haystack"];
							} else {
								$foreign_key = null;
							}
							$cols_visible = $this->backend_cache_columns(
								$table,
								$erd,
								$foreign_key
							);
							// $cols_visible = $this->backend_cache_columns($table, $erd, "");
						}
						elseif ($page_type == "table") {
							$cols_visible = $this->backend_cache_columns($table, $erd, null);
						}

					}

					if (1==1) {

						foreach ($cols_visible["linking_cols"] as $key => $value) {
							// if ($key !== $table) {
							foreach ($value["cols"] as $key_2 => $value_2) {
								if ($key_2 == "id") {
									$parent_link_part_1 = '<a href="/record/t/'.$key.'/r/';
									$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
									$query = $query->select("CONCAT('$parent_link_part_1', "."`joining_table_".$key."`".".id".", '$parent_link_part_2') as `$key - $key_2`");
								} else {
									$query = $query->select("`joining_table_".$key."`"."."."`".$key_2."`"." as `$key - $key_2`");
								}


							}
							if (isset($value["is_self_joined"])) {
								// $g_select["visible"] = array_merge(
								// 	$g_select["visible"],
								// 	array("$key - breadcrumbs" => "1")
								// );

								$query = $query->select("`joining_table_".$key."_breadcrumbs`.path"." as `$key - breadcrumbs`");

								// $concat_part_1 = "`joining_table_".$key."_breadcrumbs`.path";
								// $concat_part_2 = "`$table`.`id`";
								// $query = $query->select("CONCAT($concat_part_1, "."'-'".", $concat_part_2) as `$key - breadcrumbs`");
								// // $query = $query->select("CONCAT('0-', $concat_part_1, '-', $concat_part_2) as `$key - breadcrumbs`");
							}
							// }
						}
						foreach ($cols_visible["nonlinking_cols"] as $key => $value) {
							$query = $query->select("`".$table."`".'.'."`".$key."`");
							// if ($key == "id") {
							// 	// code...
							// 	$parent_link_part_1 = '<a href="/record/t/'.$table.'/r/';
							// 	$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
							// 	$query = $query->select("CONCAT('$parent_link_part_1', "."`".$table."`".".id, '$parent_link_part_2') as `id`");
							// }

						}
						$query = $query->from("`".$table."`");

						foreach ($cols_visible["linking_cols"] as $key => $value) {
							// echo "xyz";
							// if ($key !== $table) {
							$query = $query->join("`".$key."` as `joining_table_".$key."`", "`".$table."`".'.'."`".$value["linking_key"]."`".' = '."`joining_table_".$key."`".'.id', 'left');
							// }

							if (isset($value["is_self_joined"])) {
								$linking_key = $value["linking_key"];
								// $sql="
								// ";
								$sql = array(
									"WITH RECURSIVE q AS ",
									"(",
									// "SELECT  id,`$linking_key`, CONCAT('0-', id) as path",
									"SELECT  id,`$linking_key`, CONCAT('', id) as path",
									// "SELECT  id as id,`$linking_key`, id as path",
									"FROM    `$key`",
									"WHERE   `$linking_key` = 0",
									"UNION ALL",
									"SELECT  m.id,m.`$linking_key`, CONCAT(q.path, '-', m.id) as path",
									"FROM    `$key` m",
									"JOIN    q",
									"ON      m.`$linking_key` = q.id",
									")",
									"SELECT  *",
									// "SELECT  id,`$linking_key`, CONCAT('0-',path, '-', id) as path",
									"FROM    q",

								);
								$sql = implode("\n\r", $sql);
								// $query = $query->join("(".$sql.") as `joining_table_".$key."_breadcrumbs`", "`".$table."`".'.'."`$linking_key`".' = '."`joining_table_".$key."_breadcrumbs`".'.id', 'left');
								$query = $query->join("(".$sql.") as `joining_table_".$key."_breadcrumbs`", "`".$table."`".'.'."`id`".' = '."`joining_table_".$key."_breadcrumbs`".'.id', 'left');
							}
						}
					}


				}
				else {

					$cols_visible = $this->frontend_data_for_table_view($database, $table);


					if (1==1) {


						foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
							if (isset($value["rels"])) {
								$value_rels_table = $value["rels"]["table"];
								// if ($value_rels_table !== $table) {
								foreach ($value["rels"]["rows"] as $key_2 => $value_2) {
									if ($key_2 == "id") {
										$parent_link_part_1 = '<a href="/record/t/'.$value_rels_table.'/r/';
										$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
										$query = $query->select("CONCAT('$parent_link_part_1', "."`joining_table_".$value_rels_table."`".".id".", '$parent_link_part_2') as `$value_rels_table - $key_2`");
									} elseif (isset($value_2["assumed"])) {

									} else {
										$query = $query->select("`joining_table_".$value_rels_table."`"."."."`".$key_2."`"." as `$value_rels_table - $key_2`");
									}


								}
								if (isset($value["rels"]["is_self_joined"])) {
									// $g_select["visible"] = array_merge(
									// 	$g_select["visible"],
									// 	array("$value_rels_table - breadcrumbs" => "1")
									// );

									$query = $query->select("`joining_table_".$value_rels_table."_breadcrumbs`.path"." as `$value_rels_table - breadcrumbs`");

									// $concat_part_1 = "`joining_table_".$value_rels_table."_breadcrumbs`.path";
									// $concat_part_2 = "`$table`.`id`";
									// $query = $query->select("CONCAT($concat_part_1, "."'-'".", $concat_part_2) as `$value_rels_table - breadcrumbs`");
									// // $query = $query->select("CONCAT('0-', $concat_part_1, '-', $concat_part_2) as `$value_rels_table - breadcrumbs`");
								}
								// }
							} else {
								$query = $query->select("`".$table."`".'.'."`".$key."`");
								// if ($key == "id") {
								// 	// code...
								// 	$parent_link_part_1 = '<a href="/record/t/'.$table.'/r/';
								// 	$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
								// 	$query = $query->select("CONCAT('$parent_link_part_1', "."`".$table."`".".id, '$parent_link_part_2') as `id`");
								// }

							}
						}
						$query = $query->from("`".$table."`");


						// foreach ($cols_visible["linking_cols"] as $key => $value) {
						// }
						foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
							if (isset($value["rels"])) {

								$value_rels_table = $value["rels"]["table"];
								// echo "xyz";
								// if ($value_rels_table !== $table) {
								$query = $query->join("`".$value_rels_table."` as `joining_table_".$value_rels_table."`", "`".$table."`".'.'."`".$key."`".' = '."`joining_table_".$value_rels_table."`".'.id', 'left');
								// }

								if (isset($value["rels"]["is_self_joined"])) {
									$linking_key = $key;
									// $sql="
									// ";
									$sql = array(
									"WITH RECURSIVE q AS ",
									"(",
									// "SELECT  id,`$linking_key`, CONCAT('0-', id) as path",
									"SELECT  id,`$linking_key`, CONCAT('', id) as path",
									// "SELECT  id as id,`$linking_key`, id as path",
									"FROM    `$value_rels_table`",
									"WHERE   `$linking_key` = 0",
									"UNION ALL",
									"SELECT  m.id,m.`$linking_key`, CONCAT(q.path, '-', m.id) as path",
									"FROM    `$value_rels_table` m",
									"JOIN    q",
									"ON      m.`$linking_key` = q.id",
									")",
									"SELECT  *",
									// "SELECT  id,`$linking_key`, CONCAT('0-',path, '-', id) as path",
									"FROM    q",

									);
									$sql = implode("\n\r", $sql);
									// $query = $query->join("(".$sql.") as `joining_table_".$value_rels_table."_breadcrumbs`", "`".$table."`".'.'."`$linking_key`".' = '."`joining_table_".$value_rels_table."_breadcrumbs`".'.id', 'left');
									$query = $query->join("(".$sql.") as `joining_table_".$value_rels_table."_breadcrumbs`", "`".$table."`".'.'."`id`".' = '."`joining_table_".$value_rels_table."_breadcrumbs`".'.id', 'left');
								}
							}
						}
					}

				}


				if (1==1) {

					// Table's primary key
					$primaryKey = 'id';

					// Array of database columns which should be read and sent back to DataTables.
					// The `db` parameter represents the column name in the database, while the `dt`
					// parameter represents the DataTables column identifier. In this case simple
					// indexes
					if ("old"=="!old") {

						$columns = array(
							array( 'db' => 'first_name', 'dt' => 0 ),
							array( 'db' => 'last_name',  'dt' => 1 ),
							array( 'db' => 'position',   'dt' => 2 ),
							array( 'db' => 'office',     'dt' => 3 ),
							array(
								'db'        => 'start_date',
								'dt'        => 4,
								'formatter' => function( $d, $row ) {
									return date( 'jS M y', strtotime($d));
									}
							),
							array(
								'db'        => 'salary',
								'dt'        => 5,
								'formatter' => function( $d, $row ) {
									return '$'.number_format($d);
								}
							)
						);
					}
					else {

						$columns = array();
						$i = 0;
						foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
							if (!isset($value["rels"])) {
								$columns[] = array(
									"db" => $key,
									"dt" => $i,
								);
							}
							else {
								$value_rels_table = $value["rels"]["table"];
								// if ($value_rels_table !== $table) {
								foreach ($value["rels"]["rows"] as $key_2 => $value_2) {

								}

							}

							$i = $i+1;
						}
					}

					// SQL server connection information
					$sql_details = array(
						'user' => '',
						'pass' => '',
						'db'   => '',
						'host' => ''
					);

					$ssp_simple = $this->ssp_simple( $_GET, $sql_details, $table, $primaryKey, $columns );
					// foreach ($ssp_simple as $key => $value) {
					// 	echo "<h1>".$key."</h1>";
					// 	echo "<pre>".$value."</pre>";
					// }
					// exit;
					// // echo "<pre>";
					// // echo json_encode($ssp_simple, JSON_PRETTY_PRINT);
					// // exit;
				}

				// header('Content-Type: application/json');
				// echo json_encode($cols_visible, JSON_PRETTY_PRINT);
				// exit;


				$query = $query->join("`_activity_log`", "`_activity_log`.`record_table_and_id` = CONCAT('$table', '/', `$table`.id)", 'left');

				// echo json_encode($groups);
				// exit;
				$query = $query->group_start();
				// $query = $query->or_where("`_activity_log`.`owner` =", "0");

				$query = $query->where("`_activity_log`.`editability` =", "'pu'");
				$query = $query->or_where("`_activity_log`.`visibility` =", "'pu'");
				$query = $query->or_where("`_activity_log`.`editability` IS", "NULL");
				$query = $query->or_where("`_activity_log`.`visibility` IS", "NULL");
				$query = $query->or_where("`_activity_log`.`owner` =", "2");




				if (!empty($groups)) {
					// code...
					$query = $query->or_where_in("`_activity_log`.`owner`", $groups);
				}

				$query = $query->where("`_activity_log`.`owner` =", $active_groups_dropdown);

				$query = $query->group_end();
				// $query = $query->or_where_in("`_activity_log`.`editability`", array("'Public'", "''"));
				// $query = $query->or_where_in("`_activity_log`.`visibility`", array("'Public'", "''"));

				if ($page_type == "record") {
					$where["haystack"] = urldecode($where["haystack"]);

					// echo "`".$table."`"."."."`".$where["haystack"]."` =". '"'.$where["needle"].'"';
					// exit;
					$query = $query->where("`".$table."`"."."."`".$where["haystack"]."` =", '"'.$where["needle"].'"');
				}
				elseif ($page_type == "table") {
				}

				if ("new"=="new") {
					if (1==1) {
						// http://indigo.bluegemify.co.za/api/table2/d/Integration%20Management%20System/t/ES%20Integration%20engines/fetch_for_record/h_type/primary_key/h/id/n/1
						// ?draw=1&
						//
						// columns[0][data]=id&
						// columns[0][name]=&
						// columns[0][searchable]=true&
						// columns[0][orderable]=true&
						// columns[0][search][value]=&
						// columns[0][search][regex]=false&
						//
						// columns[1][data]=name&
						// columns[1][name]=&
						// columns[1][searchable]=true&
						// columns[1][orderable]=true&
						// columns[1][search][value]=&
						// columns[1][search][regex]=false&
						//
						// columns[2][data]=2&
						// columns[2][name]=&
						// columns[2][searchable]=true&
						// columns[2][orderable]=true&
						// columns[2][search][value]=&
						// columns[2][search][regex]=false&
						//
						// order[0][column]=0&
						// order[0][dir]=asc&
						// start=0&
						// length=10&
						// search[value]=&
						// search[regex]=false&
						// _=1645784879628

						// ssp_limit($request)
					}

					$request = $_GET;

					$columns = array();
					$i = 0;

					if ("old"=="!old") {

						foreach ($cols_visible["nonlinking_cols"] as $key => $value) {
							$columns[] = array(
								'db' => $key,
								'dt' => $i
							);
							$i = $i+1;
						}
					}
					else {
						// code...
						foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
							$columns[] = array(
								'db' => $key,
								'dt' => $i
							);
							$i = $i+1;
						}
					}

					$bindings = array();
					// echo "<pre>";
					// echo json_encode($columns, JSON_PRETTY_PRINT);
					// exit;
					// {
					// 	"id": {
					// 		"Type": "bigint(20) unsigned",
					// 		"Null": "NO",
					// 		"Key": "PRI",
					// 		"Extra": "auto_increment",
					// 		"important_field": ""
					// 	},
					// 	"name": {
					// 		"Type": "text"
					// 	}
					// }

					$dt_where = $this->filter( $request, $columns, $bindings );

					// $query = $query->where($dt_where);

					if ($_GET["search"]["value"] !== "") {
						foreach ($cols_visible["nonlinking_cols"] as $key => $value) {
							$query = $query->where("`".$table."`"."."."`".$key."` LIKE", '"%'.$_GET["search"]["value"].'%"');
						}
					}

					// $columnIdx = intval($request['order'][$i]['column']);
					// $requestColumn = $request['columns'][$columnIdx];
					// // $columnIdx = array_search( $requestColumn['data'], $dtColumns );

					$order_col_num = $request['order'][0]["column"];
					$order_col_name = $request["columns"][$order_col_num]["data"];
					$order_dir = $request['order'][0]["dir"];

					$query = $query->order_by("`".$order_col_name."`", $order_dir);
					$query = $query->limit(intval($request['length']), intval($request['start']));


					// $query = $query->where($dt_where);


				}

				// $sql = $query->_compile_select();
				// echo $sql;
				// exit;
				$query = $query->get();

			}



			$posts = $query;
			$num_rows = $query->num_rows();
			$posts = $posts->result_array();

			// print_r($this->CI->db->last_query());
			// exit;


			$this->CI->db->_protect_identifiers=true;




			$this->CI->db->_protect_identifiers=false;
			$recordsTotal = $this->CI->db->select('COUNT(`id`)')->from("`$table`")->get()->result_array()[0]["COUNT(`id`)"];
			$this->CI->db->_protect_identifiers=true;

			$data = array(
				"draw" => isset ( $request['draw'] ) ? intval( $request['draw'] ) : 0,
				'recordsTotal' => intval( $recordsTotal ),
				'recordsFiltered' => intval( $recordsTotal ),
				// 'recordsTotal' => intval( $recordsTotal ),
				// 'recordsFiltered' => $num_rows,
				'data' => $posts
			);

			// header('Content-Type: application/json');
			// echo $this->CI->db->last_query();
			// exit;
			return $data;
		}
		else {

			if ("old"=="!old") {

				$bindings = array();
				$db = $this->CI->ssp->db( $conn );

				// Build the SQL query string from the request
				$limit = $this->CI->ssp->limit( $request, $columns );
				$order = $this->CI->ssp->order( $request, $columns );
				$where_sql = $this->CI->ssp->filter( $request, $columns, $bindings );

				// Main query to actually get the data
				$data = $this->CI->ssp->sql_exec( $db, $bindings,
					"SELECT `".implode("`, `", $this->CI->ssp->pluck($columns, 'db'))."`
					FROM `$table`
					$where_sql
					$order
					$limit"
				);

				// Data set length after filtering
				$resFilterLength = $this->CI->ssp->sql_exec( $db, $bindings,
					"SELECT COUNT(`{$primaryKey}`)
					FROM   `$table`
					$where_sql"
				);
				$recordsFiltered = $resFilterLength[0][0];

				// Total data set length
				$resTotalLength = $this->CI->ssp->sql_exec( $db,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`"
				);
				$recordsTotal = $resTotalLength[0][0];

				/*
				* Output
				*/
				return array(
					"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
					"recordsTotal"    => intval( $recordsTotal ),
					"recordsFiltered" => intval( $recordsFiltered ),
					"data"            => $this->CI->ssp->data_output( $columns, $data )
				);
			}
			else {

				$table = urldecode($table);

				$request = $_GET;

				if (1==1) {

					$cols_visible = $this->frontend_data_for_table_view($database, $table);

				}
				// echo "<pre>";
				// echo json_encode($cols_visible, JSON_PRETTY_PRINT);
				// exit;

				if (1==1) {
					// Table's primary key
					$primaryKey = 'id';

					// Array of database columns which should be read and sent back to DataTables.
					// The `db` parameter represents the column name in the database, while the `dt`
					// parameter represents the DataTables column identifier. In this case simple
					// indexes
					if ("old"=="!old") {

						$columns = array(
							array( 'db' => 'first_name', 'dt' => 0 ),
							array( 'db' => 'last_name',  'dt' => 1 ),
							array( 'db' => 'position',   'dt' => 2 ),
							array( 'db' => 'office',     'dt' => 3 ),
							array(
								'db'        => 'start_date',
								'dt'        => 4,
								'formatter' => function( $d, $row ) {
									return date( 'jS M y', strtotime($d));
									}
							),
							array(
								'db'        => 'salary',
								'dt'        => 5,
								'formatter' => function( $d, $row ) {
									return '$'.number_format($d);
								}
							)
						);
					}
					else {

						$columns = array();
						$i = 0;
						foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
							if (!isset($value["rels"])) {
								$columns[] = array(
									"db" => $key,
									"dt" => $key,
								);
							}
							else {
								$value_rels_table = $value["rels"]["table"];
								// if ($value_rels_table !== $table) {
								foreach ($value["rels"]["rows"] as $key_2 => $value_2) {

								}

							}

							$i = $i+1;
						}
					}

					// SQL server connection information
					$sql_details = array(
						'user' => '',
						'pass' => '',
						'db'   => '',
						'host' => ''
					);

					// $ssp_simple = $this->ssp_simple( $_GET, $sql_details, $table, $primaryKey, $columns );
					// foreach ($ssp_simple as $key => $value) {
					// 	echo "<h1>".$key."</h1>";
					// 	echo "<pre>".$value."</pre>";
					// }
					// exit;
					// // // echo "<pre>";
					// // // echo json_encode($ssp_simple, JSON_PRETTY_PRINT);
					// // // exit;
				}

				$bindings = array();
				// $db = $this->CI->ssp->db( $conn );

				// Build the SQL query string from the request
				$limit = $this->CI->ssp->limit( $request, $columns );
				$order = $this->CI->ssp->order( $request, $columns );
				// $where_sql = $this->CI->ssp->filter( $request, $columns, $bindings );
				$where_sql = $this->filter($request, $columns);



				$fetch_ssp_helper = $this->fetch_ssp_helper($cols_visible, $database, $table, $page_type, $where, $groups);

				if ("old"=="old!") {
					// Main query to actually get the data
					$data = $this->CI->ssp->sql_exec( $db, $bindings,

					);
				}
				else {
					$SQL_data = "SELECT `".implode("`, `table_alias`.`", $this->CI->ssp->pluck($columns, 'db'))."`
					FROM ($fetch_ssp_helper) as table_alias
					$where_sql
					$order
					$limit";
				}

				if ("old"=="!old") {
					// Data set length after filtering
					$resFilterLength = $this->CI->ssp->sql_exec( $db, $bindings,
						"SELECT COUNT(`{$primaryKey}`)
						FROM   `$table`
						$where_sql"
					);
					$recordsFiltered = $resFilterLength[0][0];
				}
				else {
					$SQL_resFilterLength = "SELECT COUNT(`table_alias`.`{$primaryKey}`)
					FROM ($fetch_ssp_helper) as table_alias
					$where_sql";
				}

				if ("old"=="!old") {
					// Total data set length
					$resTotalLength = $this->CI->ssp->sql_exec( $db,
					"SELECT COUNT(`{$primaryKey}`)
					FROM   `$table`"
					);
					$recordsTotal = $resTotalLength[0][0];
				}
				else {
					$SQL_resTotalLength = "SELECT COUNT(`table_alias`.`{$primaryKey}`)
					FROM ($fetch_ssp_helper) as table_alias";
				}

				if ("old"=="!old") {
					/*
					* Output
					*/
					return array(
						"draw"            => isset ( $request['draw'] ) ?
						intval( $request['draw'] ) :
						0,
						"recordsTotal"    => intval( $recordsTotal ),
						"recordsFiltered" => intval( $recordsFiltered ),
						"data"            => $this->CI->ssp->data_output( $columns, $data )
					);
				}
				else {
					// return $result;
					// $sql="Select * from my_table where 1";
					// $data = $this->db->query($SQL_data)->result_array();
					// $resFilterLength = $this->db->query($SQL_resFilterLength)->result_array();
					// $resTotalLength = $this->db->query($SQL_resTotalLength)->result_array();
					// // return $query;

					if (1==1) {
						// $posts = $query;
						// $num_rows = $query->num_rows();
						// $posts = $posts->result_array();
						//
						// // print_r($this->CI->db->last_query());
						// // exit;
						// //
						// //
						// // $this->CI->db->_protect_identifiers=true;




						$this->CI->db->_protect_identifiers=false;

						$data = $this->CI->db->query($SQL_data)->result_array();
						$resFilterLength = $this->CI->db->query($SQL_resFilterLength)->result_array();
						$resTotalLength = $this->CI->db->query($SQL_resTotalLength)->result_array();

						$this->CI->db->_protect_identifiers=true;



						// header('Content-Type: application/json');
						// echo json_encode($resFilterLength[0], JSON_PRETTY_PRINT);
						// exit;

						// $recordsFiltered = $resFilterLength[0][0];
						$recordsFiltered = array_pop($resFilterLength[0]);
						// $recordsTotal = $resTotalLength[0][0];
						$recordsTotal = array_pop($resTotalLength[0]);
						// $recordsTotal = $resTotalLength;


						// $data = array(
						// 	"draw" => isset ( $request['draw'] ) ? intval( $request['draw'] ) : 0,
						// 	'recordsTotal' => intval( $recordsTotal ),
						// 	'recordsFiltered' => intval( $recordsTotal ),
						// 	// 'recordsTotal' => intval( $recordsTotal ),
						// 	// 'recordsFiltered' => $num_rows,
						// 	'data' => $posts
						// );


						$result = array(
							"draw"            => isset ( $request['draw'] ) ?
								intval( $request['draw'] ) :
								0,
							"recordsTotal"    => intval( $recordsTotal ),
							"recordsFiltered" => intval( $recordsFiltered ),
							"data"            => $this->CI->ssp->data_output( $columns, $data )
						);


						header('Content-Type: application/json');
						echo json_encode($result, JSON_PRETTY_PRINT);
						exit;

						// header('Content-Type: application/json');
						// echo $this->CI->db->last_query();
						// exit;
					}

				}
			}
		}
	}

	// public function fetch_join_where($table_1, $table_2, $haystack,$needle)
	// {
	//
	// 	$this->CI->load->database();
	//
	//
	// 	// $posts = $this->CI->db->select('*')->where($haystack, $needle)->from($table_1)->join($table_2, "$table_1.$table_2_key = $table_2.id")->get()->result_array();
	// 	$table_2_singular = $this->CI->erd_lib->grammar_singular($table_2);
	// 	$table_2_singular = $table_2_singular."_id";
	// 	// $table_1_singular = $this->CI->erd_lib->grammar_singular($table_1);
	// 	// $haystack = $table_1_singular.".".$haystack;
	//
	//
	// 	$posts = $this->CI->db->select('*')->where($haystack, $needle)->from($table_2)->join($table_1, "$table_1.$table_2_singular = $table_2.id", "right")->get()->result_array();
	//
	// 	$data = array('responce' => 'success', 'posts' => $posts);
	// 	return $data;
	//
	// }

	public function fetch_where($table, $haystack, $needle)
	{

		$this->CI->load->database();


		$posts = $this->CI->db->select('*')->where($haystack, $needle)->from($table)->get()->result_array();

		$data = array('responce' => 'success', 'posts' => $posts);
		return $data;

	}

	public function database_api($database)
	{
		$erd_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
		$rows = file_get_contents($erd_path);
		$rows = json_decode($rows, true);


		// $this->CI->load->database();
		//
		// $row_query = array(
		//   "SHOW TABLES",
		// );
		// $row_query = implode(" ", $row_query);
		// $rows = $this->CI->db->query($row_query)->result_array();
		// $rows = array_column($rows, 'Tables_in_'.$this->CI->db->database);
		// foreach ($rows as $key => $value) {
		// 	$rows_formatted[]["name"] = $value;
		// }
		foreach ($rows as $key => $value) {
			$rows_formatted[]["name"] = $key;
		}


		$data = array('responce' => 'success', 'posts' => $rows_formatted);
		return $data;
	}

	public function databases_api()
	{

		$dir = APPPATH."erd/";
		$rows = scandir($dir);
		// foreach ($rows as $key => $link) {
		// }
		// $erd_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
		// $rows = file_get_contents($erd_path);
		// $rows = json_decode($rows, true);


		// $this->CI->load->database();
		//
		// $row_query = array(
		//   "SHOW TABLES",
		// );
		// $row_query = implode(" ", $row_query);
		// $rows = $this->CI->db->query($row_query)->result_array();
		// $rows = array_column($rows, 'Tables_in_'.$this->CI->db->database);
		// foreach ($rows as $key => $value) {
		// 	$rows_formatted[]["name"] = $value;
		// }
		foreach ($rows as $key => $value) {

				if(is_dir($dir.$value) && !in_array($value, array(".",".."))){

					$rows_formatted[]["name"] = $value;
				}
		}


		$data = array('responce' => 'success', 'posts' => $rows_formatted);
		return $data;
	}

	public function backend_cache_columns($table, $erd, $foreign_key)
	{
		$parents = array();

		foreach ($erd as $key => $value) {
			if (isset($value["items"])) {
				foreach ($value["items"] as $key_2 => $value_2) {
					if ($key_2 == $table) {

						if ($value_2 !== $foreign_key) { // dont inherit values for current parent
							// echo $key_2;
							$parents[$key]["cols"] = $value["fields"];
							$parents[$key]["linking_key"] = $value_2;
							// if (isset($value["items"][$key])) {
							// 	$parents[$key]["is_self_joined"] = 1;
							// }
						}
						if (isset($value["items"][$key])) {
							$parents[$key]["cols"] = $value["fields"];
							$parents[$key]["linking_key"] = $value_2;

							$parents[$key]["is_self_joined"] = 1;

						}
					}
				}
			}
		}

		$self = $erd[$table]["fields"];

		foreach ($parents as $key => $value) {
			foreach ($erd[$key]["items"] as $key_2 => $value_2) {
				unset($self[$value_2]);
			}
		}

		if (isset($self[$foreign_key])) {
			unset($self[$foreign_key]);
		}

		$cols_visible = array(
			"nonlinking_cols" => $self,
			"linking_cols" => $parents
		);

		return $cols_visible;



	}

	public function makeSafeForCSS($string)
	{
		//Lower case everything
		$string = strtolower($string);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		// $string = preg_replace("/[\s_]/", "-", $string);
		$string = preg_replace("/[\s_]/", "_", $string);
		return $string;
	}

	public function backend_cache_items($rec_part, $erd, $table, $foreign_k)
	{


		if ($rec_part=="overview") {

			$g_identity["g_ability_name"] = $table;
			$g_identity["g_ability_html_id"] = preg_replace('/\W+/','',strtolower(strip_tags($g_identity["g_ability_name"])));
			$g_identity["g_from"] = $table;

			$haystack = "id";
			$g_identity["g_where_haystack"] = $haystack;
			// $g_identity["g_where_haystack_type"] = "primary_key";
			$g_identity["g_where_haystack_type"] = "foreign_key";

			$data_endpoint = "fetch_for_record/h_type/primary_key/h/$haystack/n/";
			$g_identity["data_endpoint"] = $data_endpoint;

		}
		elseif ($rec_part=="details") {

			$g_identity["g_ability_name"] = $table." (as ".$foreign_k.")"; // changes
			$g_identity["g_ability_html_id"] = preg_replace('/\W+/','',strtolower(strip_tags($g_identity["g_ability_name"])));
			$g_identity["g_from"] = $table; // dynamic

			$haystack = $foreign_k; //changes
			$g_identity["g_where_haystack"] = $foreign_k;
			$g_identity["g_where_haystack_type"] = "foreign_key";

			$data_endpoint = "fetch_for_record/h_type/foreign_key/h/$haystack/n/";
			$g_identity["data_endpoint"] = $data_endpoint;
		}
		elseif ($rec_part=="table") {

			$g_identity["g_ability_name"] = $table; // changes
			$g_identity["g_ability_html_id"] = preg_replace('/\W+/','',strtolower(strip_tags($g_identity["g_ability_name"])));
			$g_identity["g_from"] = $table; // dynamic

			$data_endpoint = "fetch";
			$g_identity["data_endpoint"] = $data_endpoint;
		}

		$editable = $erd[$table]["fields"];
		foreach ($editable as $key => $value) {
			$g_select["editable"][$key]["col_deets"] = $value;
			if ($key == $foreign_k) {
				$g_select["editable"][$key]["assumable"] = "";
			}
		}




		$result["g_identity"] = $g_identity;
		if ($rec_part=="overview") {
			$cols_visible = $this->backend_cache_columns($g_identity["g_from"], $erd, "");}
		elseif ($rec_part=="details") {
			$cols_visible = $this->backend_cache_columns($g_identity["g_from"], $erd, $foreign_k);
		}

		if ($rec_part=="overview") {
		// if (1==1) {

			$g_select["visible"] = array();

			$g_select["visible"] = $cols_visible["nonlinking_cols"];

			$cols_wth_props = array();

			foreach ($cols_visible["linking_cols"] as $key => $value) {

				// header('Content-Type: application/json');
				// echo json_encode($value, JSON_PRETTY_PRINT);
				// exit;
				foreach ($value["cols"] as $key_2 => $value_2) {
					if (isset($value_2["important_field"])) {
						// code...
						$cols_wth_props["$key - $key_2"] = $value_2;
					}
				}
				$g_select["visible"] = array_merge(
					$g_select["visible"],
					$cols_wth_props
				);

				if (isset($g_select["editable"][$value["linking_key"]])) {
					if (isset($value["is_self_joined"]) && $key == $table) {
						$g_select["visible"] = array("$key - breadcrumbs" => "") + $g_select["visible"];
					}
				}
			}

			foreach ($cols_visible["linking_cols"] as $key => $value) {
				if (isset($g_select["editable"][$value["linking_key"]])) {
					// code...
					$cols_visible_lookup_helper = $this->backend_cache_columns($key, $erd, "");
					$cols_visible_lookup = $cols_visible_lookup_helper["nonlinking_cols"];
					$cols_visible_lookup_part_2 = array();
					foreach ($cols_visible_lookup_helper["linking_cols"] as $key_lookup => $value_lookup) {
						foreach ($value_lookup["cols"] as $key_lookup_2 => $value_lookup_2) {
							$cols_visible_lookup_part_2["$key_lookup - $key_lookup_2"] = $value_lookup_2;
							$cols_visible_lookup_part_2["$key_lookup - $key_lookup_2"]["assumed"] = "";
						}
						$cols_visible_lookup = array_merge(
							$cols_visible_lookup,
							$cols_visible_lookup_part_2
						);

						if (isset($value_lookup["is_self_joined"])) {
							if ("old"=="!old") {
								$cols_visible_lookup = array("$key_lookup - breadcrumbs" => "") + $cols_visible_lookup;

							} else {

								$breadcrumbs = array();
								$breadcrumbs["$key_lookup - breadcrumbs"]["assumed"] = "";

								$cols_visible_lookup = $breadcrumbs + $cols_visible_lookup;
							}

							$g_select["editable"][$value["linking_key"]]["rels"]["is_self_joined"] = $value_lookup["is_self_joined"];
						}

					}

					$g_select["editable"][$value["linking_key"]]["rels"]["table"] = $key;
					$g_select["editable"][$value["linking_key"]]["rels"]["rows"] = $cols_visible_lookup;

				}
			}
			$result["g_select"] = $g_select;

		}

		return $result;
	}

	public function insert($database, $table)
	{
		$table = urldecode($table);

		$this->CI->load->database();

		// if ($this->CI->input->is_ajax_request()) {
		// $this->form_validation->set_rules('name', 'Name', 'required');
		// $this->form_validation->set_rules('event_children', 'Event_children');

		// if ($this->form_validation->run() == FALSE) {
		//   $data = array('responce' => 'error', 'message' => validation_errors());
		// } else {

		$post = $this->CI->input->post();


		// unset($post["variables"][0]);
		if ("old" == "old2") {
			$ajax_data = array();
			foreach ($post["variables"] as $key => $value) {

				$ajax_data["`".urldecode($key)."`"] = '"'.$value.'"';
			}
			// zzzzzzzzzzzzzz

		} else {
			// code...
			$ajax_data = array();
			$rows = $this->table_rows($database, $table);
			foreach ($rows as $key => $value) {
				if ($key !== "id") {
					// $ajax_data["`".urldecode($key)."`"] = "\"".$this->CI->input->post('edit_'.$this->makeSafeForCSS($key))."\"";
					$ajax_data["`".urldecode($key)."`"] = '"'.$post["variables"][$this->makeSafeForCSS($key)].'"';

				}
			}
		}
		// header('Content-Type: application/json');
		// echo json_encode($ajax_data, JSON_PRETTY_PRINT);
		// exit;





		$this->CI->db->_protect_identifiers=false;

		$query_result = $this->CI->db->insert("`$table`", $ajax_data);
		$this->CI->db->_protect_identifiers=true;

		if ($query_result) {

			if (1==1) {

				$insert_id = $this->CI->db->insert_id();
				// $insert_id = 10000;
				$table_and_id = array(
					"table" => $table,
					"id" => $insert_id
				);



				$this->CI->input->post('edit_permissions_owner');
				$this->CI->input->post('edit_permissions_owner');

				$permissions = array();
				$permissions["owner"] = $post["permissions"]["edit_permissions_owner"];
				if ($post["permissions"]["edit_permissions_editability"] == "") {
					$permissions["editability"] = "pr";
				} else {
					$permissions["editability"] = $post["permissions"]["edit_permissions_editability"];
				}
				if ($post["permissions"]["edit_permissions_visibility"] == "") {
					$permissions["visibility"] = "pr";
				} else {
					$permissions["visibility"] = $post["permissions"]["edit_permissions_visibility"];
				}


				$this->log_activity($table_and_id, $permissions, "insert");
			}

			$data = array('responce' => 'success', 'message' => 'Record added Successfully');
		} else {
			$data = array('responce' => 'error', 'message' => 'Failed to add record');
		}
		// }

		return $data;
		// } else {
		// 	return "No direct script access allowed";
		// }
	}

	public function delete($table)
	{

		$this->CI->load->database();

		$this->CI->db->_protect_identifiers=false;
		// if ($this->CI->input->is_ajax_request()) {
		$del_id = $this->CI->input->post('del_id');

		if ($this->CI->db->delete("`$table`", array('id' => $del_id))) {
			if (1==1) {


				$table_and_id = array(
					"table" => $table,
					"id" => $del_id
				);



				$this->CI->input->post('edit_permissions_owner');
				$this->CI->input->post('edit_permissions_owner');



				$permissions = array();
				// $permissions["owner"] = $post["permissions"]["edit_permissions_owner"];
				// if ($post["permissions"]["edit_permissions_editability"] == "") {
				// 	$permissions["editability"] = "pr";
				// } else {
				// 	$permissions["editability"] = $post["permissions"]["edit_permissions_editability"];
				// }
				// if ($post["permissions"]["edit_permissions_visibility"] == "") {
				// 	$permissions["visibility"] = "pr";
				// } else {
				// 	$permissions["visibility"] = $post["permissions"]["edit_permissions_visibility"];
				// }

				$this->log_activity($table_and_id, $permissions, "delete");
			}


			$data = array('responce' => 'success');
		} else {
			$data = array('responce' => 'error');
		}
		return $data;
		// } else {
		// 	return "No direct script access allowed";
		// }
	}

	public function update($database, $table)
	{
		// header('Content-Type: application/json');
		// echo json_encode($post = $this->CI->input->post(), JSON_PRETTY_PRINT);
		// exit;

		$table = urldecode($table);
		$this->CI->load->database();

		// if ($this->CI->input->is_ajax_request()) {
		//   $this->form_validation->set_rules('edit_name', 'Name', 'required');
		//   $this->form_validation->set_rules('edit_event_children', 'Event_children');
		//   if ($this->form_validation->run() == FALSE) {
		//     $ajax_data = array('responce' => 'error', 'message' => validation_errors());
		//   } else {


		$post = $this->CI->input->post();

		$ajax_data['id'] = $post["variables"]["edit_record_id"];

		$rows = $this->table_rows($database, $table);
		foreach ($rows as $key => $value) {
			if ($key !== "id") {
				// $ajax_data["`".urldecode($key)."`"] = "\"".$this->CI->input->post('edit_'.$this->makeSafeForCSS($key))."\"";
				$ajax_data["`".urldecode($key)."`"] = '"'.$post["variables"]['edit_'.$this->makeSafeForCSS($key)].'"';

			}
		}

		// zzzzzzzzzzzzzz
		// header('Content-Type: application/json');
		// echo json_encode($ajax_data, JSON_PRETTY_PRINT);
		// exit;
		$this->CI->db->_protect_identifiers=false;

		$query_result = $this->CI->db->update("`$table`", $ajax_data, array('id' => $ajax_data['id']));

		$this->CI->db->_protect_identifiers=true;

		if ($query_result) {

			if (1==1) {

				$table_and_id = array(
					"table" => $table,
					"id" => $ajax_data['id']
				);



				$this->CI->input->post('edit_permissions_owner');
				$this->CI->input->post('edit_permissions_owner');


				$permissions = array();
				$permissions["owner"] = $post["permissions"]["edit_permissions_owner"];
				if ($post["permissions"]["edit_permissions_editability"] == "") {
					$permissions["editability"] = "pr";
				} else {
					$permissions["editability"] = $post["permissions"]["edit_permissions_editability"];
				}
				if ($post["permissions"]["edit_permissions_visibility"] == "") {
					$permissions["visibility"] = "pr";
				} else {
					$permissions["visibility"] = $post["permissions"]["edit_permissions_visibility"];
				}

				$this->log_activity($table_and_id, $permissions, "update");
			}


			$data = array('responce' => 'success', 'message' => 'Record update Successfully');
			// $data = $this->CI->db->last_query();



		} else {
			$data = array('responce' => 'error', 'message' => 'Failed to update record');
		}
		return $data;


		//   }
		//   return $data;
		// } else {
		// 	return "No direct script access allowed";
		// }
	}

	public function log_activity($table_and_id, $permissions, $last_activity_type)
	{

		$this->CI->db->_protect_identifiers=false;
		// $table = "_groups";
		// $haystack = "user_id";
		// $needle = $this->CI->ion_auth->get_user_id();
		// $user_group_links = $this->fetch_where($table, $haystack, $needle)["posts"];
		// $user_group = $user_group_links[0]["group_id"];

		$activity_log = array(
			"record_table_and_id" => $table_and_id["table"]."/".$table_and_id["id"],
			// "record_table" => $table_and_id["table"],
			// "record_id" => $table_and_id["id"],
			// "actvity_type" => "",
			"timestamp" => date("Y-m-d H:i:s"),
			"last_activity_type" => $last_activity_type,
			// "owner" => $permissions["owner"],
			// "editability" => $permissions["editability"],
			// "visibility" => $permissions["visibility"]
		);


		if (!empty($permissions)) {
			// code...
			$activity_log["owner"] = $permissions["owner"];
			$activity_log["editability"] = $permissions["editability"];
			$activity_log["visibility"] = $permissions["visibility"];

		}

		if ($table_and_id["table"] == "groups") {
			$activity_log["owner"] = $table_and_id["id"];
		}

		// header('Content-Type: application/json');
		// echo json_encode($activity_log, JSON_PRETTY_PRINT);
		// exit;


		// $query_result = $this->CI->db->replace('_activity_log', $activity_log);

		$activity_log_2 = array();
		foreach ($activity_log as $key => $value) {
			if ($key !== "id") {
				$activity_log_2["`".$key."`"] = '"'.$value.'"';

			}
		}

		// echo json_encode($permissions, JSON_PRETTY_PRINT);
		// exit;
		$_activity_log = $this->CI->db->select('*')->where('`record_table_and_id`', '"'.$table_and_id["table"]."/".$table_and_id["id"].'"')->from('_activity_log')->get()->result_array();
		if (empty($_activity_log)) {
			$query_result = $this->CI->db->insert(
				'_activity_log',
				$activity_log_2,
			);

		} else {
			$query_result = $this->CI->db->update(
				'_activity_log',
				$activity_log_2,
				array('`record_table_and_id`' => '"'.$table_and_id["table"]."/".$table_and_id["id"].'"')
			);
		}

		$this->CI->db->_protect_identifiers=true;
	}

	public function user_groups()
	{


		$this->CI->load->database();


		// $this->db->from('table')
		// ->join('SELECT id from table2 where something=%s) as T2'),'table.id=T2.id', 'LEFT',NULL)
		// ->get()->row();
		//
		// $result = $query_run->get('user_messages');
		// //echo $this->db->last_query();
		// return $result->row();

		$sql="WITH RECURSIVE q AS
		(
			SELECT  id,name,description,group_id,CONCAT(id) as path
			FROM    groups
			WHERE   group_id = 0
			UNION ALL
			SELECT  m.id,m.name,m.description,m.group_id,CONCAT(q.path,'-',m.id) as path
			FROM    groups m
			JOIN    q
			ON      m.group_id = q.id
			)
			SELECT  *
			FROM    q
		";

		$query_result = $this->CI->db->query($sql)->result_array();

		// chaneg to $config['tables']['users_groups'] ?
		$table = "users_groups";
		$haystack = "user_id";
		$needle = $this->CI->ion_auth->get_user_id();
		$user_group_links = $this->fetch_where($table, $haystack, $needle)["posts"];

		$user_group_ids = array_column($user_group_links, "group_id");

		$result = array();
		foreach ($query_result as $key => $value) {
			$matches = array_intersect($user_group_ids,explode("-",$value["path"]));
			if (!empty($matches)) {
				$result[$key] = $value;
			}
		}
		return $result;

	}

	public function backend_cache_table($database, $table)
	{
		$table = urldecode($table);
		$g_identity_singular = $this->CI->erd_lib->grammar_singular($table);

		$data = array();
		$data["table_name"] = $table;
		$data["table_name_singular"] = $g_identity_singular;

		$tables_in_db = $this->CI->erd_lib->tables_in_db();

		if (isset($tables_in_db[$table])) {
			$data["table_exists"] = 1;
		} else {

			$data["table_exists"] = 0;
		}

		$erd_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
		$erd = file_get_contents($erd_path);
		$erd = json_decode($erd, true);

		if (isset($erd[$table]["record_links"])) {
			$data["record_links"] = $erd[$table]["record_links"];
		}
		if (isset($erd[$table]["table_links"])) {
			$data["table_links"] = $erd[$table]["table_links"];
		}

		$g_core_abilities = $this->backend_cache_items("overview", $erd, $table, null, "");

		$g_parental_abilities = array();
		if (isset($erd[$table]["items"])) {
			$items = $erd[$table]["items"];
			foreach ($items as $key => $value) {
				$g_parental_abilities[$key] = $this->backend_cache_items("details", $erd, $key, $value, $table);

			}
		}

		$data["g_core_abilities"] = $g_core_abilities;
		$data["g_parental_abilities"] = $g_parental_abilities;

		return $data;
	}

	public function frontend_data_for_record_view($database, $table, $record_id)
	{

		if (file_exists($this->CI->erd_lib->erd_path($database)."/crud_cache/$table.txt")) {
			$data = file_get_contents($this->CI->erd_lib->erd_path($database)."/crud_cache/$table.txt");
			$data = json_decode($data, true);
			$data["record_id"] = $record_id;
			$data["title"] = $data["table_name_singular"]." ".$record_id;
			$data["g_core_abilities"]["g_identity"]["g_where_needle"] = $record_id;
			$new_endpoint = $data["g_core_abilities"]["g_identity"]["data_endpoint"].$record_id;
			$data["g_core_abilities"]["g_identity"]["data_endpoint"] = $new_endpoint;
			$data["g_core_abilities"]["g_identity"]["g_ability_name"] = "overview";
			$data["g_core_abilities"]["g_identity"]["g_ability_html_id"] = "overview";

			foreach ($data["g_parental_abilities"] as $key => $value) {
				$iteration_result = $data["g_parental_abilities"][$key];

				$iteration_result["g_identity"]["g_where_needle"] = $record_id;

				$new_endpoint = $iteration_result["g_identity"]["data_endpoint"].$record_id;
				$iteration_result["g_identity"]["data_endpoint"] = $new_endpoint;

				$relation = file_get_contents($this->CI->erd_lib->erd_path($database)."/crud_cache/$key.txt");
				$relation = json_decode($relation, true);
				$relation = $relation["g_core_abilities"]["g_select"];

				$g_where_haystack = $value["g_identity"]["g_where_haystack"];
				unset($relation["editable"][$g_where_haystack]["rels"]);
				// foreach ($relation["editable"] as $relation_key => $relation_value) {
				// 	unset($relation["editable"])
				// }


				// $table_name = $data["table_name"];
				$self_editable_cols = $data["g_core_abilities"]["g_select"]["editable"];
				foreach ($self_editable_cols as $self_editable_col_key => $self_editable_col_value) {
					if (isset($relation["visible"]["$table - $self_editable_col_key"])) {

						unset($relation["visible"]["$table - $self_editable_col_key"]);
					}
				}
				// header('Content-Type: application/json');
				// echo json_encode($data["g_core_abilities"]["g_select"], JSON_PRETTY_PRINT);
				// exit;
				$iteration_result["g_select"] = $relation;

				$g_where_haystack = $iteration_result["g_identity"]["g_where_haystack"];

				$iteration_result["g_select"]["editable"][$g_where_haystack]["assumable"] = $record_id;
				$iteration_result["g_select"]["editable"][$g_where_haystack]["assumable"] = $record_id;


				$data["g_parental_abilities"][$key] = $iteration_result;
			}

			return $data;
		} else {
			return array();
			// code...
		}


	}

	public function frontend_data_for_table_view($database, $table)
	{
		if (file_exists($this->CI->erd_lib->erd_path($database)."/crud_cache/$table.txt")) {
			$data = file_get_contents($this->CI->erd_lib->erd_path($database)."/crud_cache/$table.txt");
			$data = json_decode($data, true);
			$data["title"] = $data["table_name"];
			$data["g_core_abilities"]["g_identity"]["data_endpoint"] = "fetch";

			unset($data["g_parental_abilities"]);

			return $data;
		} else {
			return array();
			// code...
		}


	}

	// public function user_groups_for_dropdown()
	public function active_groups_dropdown()
	{
		$public_group = array(
			"id"=> "0",
			"name"=> "All public content",
			"description"=> "All public content",
			"group_id"=> "0",
			"path"=> "0"
		);
		$user_groups = array($public_group);

		if (!$this->CI->ion_auth->logged_in())
		{
		} else {
			$private_groups = $this->user_groups();
			$user_groups = array_merge($user_groups,$private_groups);
		}


		$keys = array_column($user_groups, 'path');
		$user_groups=array_combine($keys,$user_groups);
		ksort($user_groups);
		// header('Content-Type: application/json');
		// echo json_encode($query_result, JSON_PRETTY_PRINT);
		// exit;



		$result = array();
		foreach ($user_groups as $key => $value) {
			$result[$value["id"]] = array(
				"id"=>$value["id"],
				"name"=>$value["name"],
				"indent"=>str_repeat("-", count(explode("-",$value["path"]))-1),
				"path"=>$key,
			);
		}

		// header('Content-Type: application/json');
		// echo json_encode($result, JSON_PRETTY_PRINT);
		// exit;


		$cname = "active_group";
		$cookie_value = $this->getCookie($cname);
		if (isset($cookie_value[0])) {
			$assumed = $cookie_value[0];
		} else {
			$assumed = "";
		}

		$result = array(
			"assumed" => $assumed,
			"options" => $result,
		);

		return $result;

	}

	// public function setCookie($cname, $cvalue, $exdays) {
	// 	$validity_days = $exdays;
	// 	$time_in_seconds = $validity_days * 60 * 60 * 24;
	//
	// 	if (!empty($cvalue)) {
	// 		$cvalue = http_build_query($cvalue);
	// 	} else {
	// 		$cvalue = "";
	// 	}
	// 	// $cvalue = rawurlencode($cvalue);
	// 	return setrawcookie($cname, $cvalue, time()+$time_in_seconds, "/");
	// }

	public function getCookie($cname) {
		$cookie_name = $cname;
		if (isset($_COOKIE[$cookie_name])) {
			parse_str($_COOKIE[$cookie_name], $cookie_value);
			return $cookie_value;
		} else {
			return false;
		}
	}

	// public function filter ( $request, $columns, &$bindings )
	// {
	//  $globalSearch = array();
	//  $columnSearch = array();
	//  $dtColumns = $this->pluck( $columns, 'dt' );
	//
	//  if ( isset($request['search']) && $request['search']['value'] != '' ) {
	// 	 $str = $request['search']['value'];
	//
	// 	 for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
	// 		 $requestColumn = $request['columns'][$i];
	// 		 $columnIdx = array_search( $requestColumn['data'], $dtColumns );
	// 		 $column = $columns[ $columnIdx ];
	//
	// 		 if ( $requestColumn['searchable'] == 'true' ) {
	// 			 if(!empty($column['db'])){
	// 				 $binding = $this->bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
	// 				 $globalSearch[] = "`".$column['db']."` LIKE ".$binding;
	// 			 }
	// 		 }
	// 	 }
	//  }
	//
	//  // Individual column filtering
	//  if ( isset( $request['columns'] ) ) {
	// 	 for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
	// 		 $requestColumn = $request['columns'][$i];
	// 		 $columnIdx = array_search( $requestColumn['data'], $dtColumns );
	// 		 $column = $columns[ $columnIdx ];
	//
	// 		 $str = $requestColumn['search']['value'];
	//
	// 		 if ( $requestColumn['searchable'] == 'true' &&
	// 			$str != '' ) {
	// 			 if(!empty($column['db'])){
	// 				 $binding = $this->bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
	// 				 $columnSearch[] = "`".$column['db']."` LIKE ".$binding;
	// 			 }
	// 		 }
	// 	 }
	//  }
	//
	//  // Combine the filters into a single string
	//  $where_sql = '';
	//
	//  if ( count( $globalSearch ) ) {
	// 	 $where_sql = '('.implode(' OR ', $globalSearch).')';
	//  }
	//
	//  if ( count( $columnSearch ) ) {
	// 	 $where_sql = $where_sql === '' ?
	// 		 implode(' AND ', $columnSearch) :
	// 		 $where_sql .' AND '. implode(' AND ', $columnSearch);
	//  }
	//
	//  // if ( $where_sql !== '' ) {
	// 	//  $where_sql = 'WHERE '.$where_sql;
	//  // }
	//
	//  return $where_sql;
	// }

	public function bind ( &$a, $val, $type )
	{
	 $key = ':binding_'.count( $a );

	 $a[] = array(
		 'key' => $key,
		 'val' => $val,
		 'type' => $type
	 );

	 return $key;
	}

	public function pluck ( $a, $prop )
	{
	 $out = array();

	 for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
		 if(empty($a[$i][$prop])){
			 continue;
		 }
		 //removing the $out array index confuses the filter method in doing proper binding,
		 //adding it ensures that the array data are mapped correctly
		 $out[$i] = $a[$i][$prop];
	 }

	 return $out;
	}

	public function ssp_simple($request, $conn, $table, $primaryKey, $columns )
	{

		if ("old"=="!old") {

			$bindings = array();
			$db = $this->CI->ssp->db( $conn );

			// Build the SQL query string from the request
			$limit = $this->CI->ssp->limit( $request, $columns );
			$order = $this->CI->ssp->order( $request, $columns );
			$where_sql = $this->CI->ssp->filter( $request, $columns, $bindings );

			// Main query to actually get the data
			$data = $this->CI->ssp->sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", $this->CI->ssp->pluck($columns, 'db'))."`
				FROM `$table`
				$where_sql
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = $this->CI->ssp->sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where_sql"
			);
			$recordsFiltered = $resFilterLength[0][0];

			// Total data set length
			$resTotalLength = $this->CI->ssp->sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			FROM   `$table`"
			);
			$recordsTotal = $resTotalLength[0][0];

			/*
			* Output
			*/
			return array(
				"draw"            => isset ( $request['draw'] ) ?
				intval( $request['draw'] ) :
				0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => $this->CI->ssp->data_output( $columns, $data )
			);
		}
		else {

			$bindings = array();
			// $db = $this->CI->ssp->db( $conn );

			// Build the SQL query string from the request
			$limit = $this->CI->ssp->limit( $request, $columns );
			$order = $this->CI->ssp->order( $request, $columns );
			$where_sql = $this->CI->ssp->filter( $request, $columns, $bindings );

			if ("old"=="old!") {
				// Main query to actually get the data
				$data = $this->CI->ssp->sql_exec( $db, $bindings,

				);
			}
			else {
				$result["data"] = "SELECT `".implode("`, `", $this->CI->ssp->pluck($columns, 'db'))."`
				FROM `$table`
				$where_sql
				$order
				$limit";
			}

			if ("old"=="!old") {
				// Data set length after filtering
				$resFilterLength = $this->CI->ssp->sql_exec( $db, $bindings,
					"SELECT COUNT(`{$primaryKey}`)
					FROM   `$table`
					$where_sql"
				);
				$recordsFiltered = $resFilterLength[0][0];
			}
			else {
				$result["resFilterLength"] = "SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where_sql";
			}

			if ("old"=="!old") {
				// Total data set length
				$resTotalLength = $this->CI->ssp->sql_exec( $db,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`"
				);
				$recordsTotal = $resTotalLength[0][0];
			}
			else {
				$result["resTotalLength"] = "SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`";
			}

			if ("old"=="!old") {
				/*
				* Output
				*/
				return array(
					"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
					"recordsTotal"    => intval( $recordsTotal ),
					"recordsFiltered" => intval( $recordsFiltered ),
					"data"            => $this->CI->ssp->data_output( $columns, $data )
				);
			}
			else {
				return $result;
			}
		}

	}

	public function fetch_ssp_helper($cols_visible, $database, $table, $page_type, $where, $groups)
	{

		$active_groups_dropdown = $this->active_groups_dropdown();
		$active_groups_dropdown = $active_groups_dropdown["assumed"];
		if ($active_groups_dropdown == "") {
			$active_groups_dropdown = 2;
		}



		$table = urldecode($table);

		$this->CI->load->database();

		// $posts = $this->CI->db->where($where["haystack"], $where["needle"])->get($table)->result_array();




		$this->CI->db->_protect_identifiers=false;
		$query = $this->CI->db;


		if ("old"=="old") {
			// code...
			if ("old"=="!old") {
				// TODO: use frontend_data_for_table_view or frontend_data_for_record_view not backend_cache_columns

				if (1==1) {
					$erd_path = $this->CI->erd_lib->erd_path($database).'/erd.json';
					$erd = file_get_contents($erd_path);
					$erd = json_decode($erd, true);

					if ($page_type == "record") {
						$where["haystack_type"] = urldecode($where["haystack_type"]);
						if ($where["haystack_type"] == "foreign_key") {
							$foreign_key = $where["haystack"];
						} else {
							$foreign_key = null;
						}
						$cols_visible = $this->backend_cache_columns(
							$table,
							$erd,
							$foreign_key
						);
						// $cols_visible = $this->backend_cache_columns($table, $erd, "");
					}
					elseif ($page_type == "table") {
						$cols_visible = $this->backend_cache_columns($table, $erd, null);
					}

				}

				if (1==1) {

					foreach ($cols_visible["linking_cols"] as $key => $value) {
						// if ($key !== $table) {
						foreach ($value["cols"] as $key_2 => $value_2) {
							if ($key_2 == "id") {
								$parent_link_part_1 = '<a href="/record/t/'.$key.'/r/';
								$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
								$query = $query->select("CONCAT('$parent_link_part_1', "."`joining_table_".$key."`".".id".", '$parent_link_part_2') as `$key - $key_2`");
							} else {
								$query = $query->select("`joining_table_".$key."`"."."."`".$key_2."`"." as `$key - $key_2`");
							}


						}
						if (isset($value["is_self_joined"])) {
							// $g_select["visible"] = array_merge(
							// 	$g_select["visible"],
							// 	array("$key - breadcrumbs" => "1")
							// );

							$query = $query->select("`joining_table_".$key."_breadcrumbs`.path"." as `$key - breadcrumbs`");

							// $concat_part_1 = "`joining_table_".$key."_breadcrumbs`.path";
							// $concat_part_2 = "`$table`.`id`";
							// $query = $query->select("CONCAT($concat_part_1, "."'-'".", $concat_part_2) as `$key - breadcrumbs`");
							// // $query = $query->select("CONCAT('0-', $concat_part_1, '-', $concat_part_2) as `$key - breadcrumbs`");
						}
						// }
					}
					foreach ($cols_visible["nonlinking_cols"] as $key => $value) {
						$query = $query->select("`".$table."`".'.'."`".$key."`");
						// if ($key == "id") {
						// 	// code...
						// 	$parent_link_part_1 = '<a href="/record/t/'.$table.'/r/';
						// 	$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
						// 	$query = $query->select("CONCAT('$parent_link_part_1', "."`".$table."`".".id, '$parent_link_part_2') as `id`");
						// }

					}
					$query = $query->from("`".$table."`");

					foreach ($cols_visible["linking_cols"] as $key => $value) {
						// echo "xyz";
						// if ($key !== $table) {
						$query = $query->join("`".$key."` as `joining_table_".$key."`", "`".$table."`".'.'."`".$value["linking_key"]."`".' = '."`joining_table_".$key."`".'.id', 'left');
						// }

						if (isset($value["is_self_joined"])) {
							$linking_key = $value["linking_key"];
							// $sql="
							// ";
							$sql = array(
								"WITH RECURSIVE q AS ",
								"(",
								// "SELECT  id,`$linking_key`, CONCAT('0-', id) as path",
								"SELECT  id,`$linking_key`, CONCAT('', id) as path",
								// "SELECT  id as id,`$linking_key`, id as path",
								"FROM    `$key`",
								"WHERE   `$linking_key` = 0",
								"UNION ALL",
								"SELECT  m.id,m.`$linking_key`, CONCAT(q.path, '-', m.id) as path",
								"FROM    `$key` m",
								"JOIN    q",
								"ON      m.`$linking_key` = q.id",
								")",
								"SELECT  *",
								// "SELECT  id,`$linking_key`, CONCAT('0-',path, '-', id) as path",
								"FROM    q",

							);
							$sql = implode("\n\r", $sql);
							// $query = $query->join("(".$sql.") as `joining_table_".$key."_breadcrumbs`", "`".$table."`".'.'."`$linking_key`".' = '."`joining_table_".$key."_breadcrumbs`".'.id', 'left');
							$query = $query->join("(".$sql.") as `joining_table_".$key."_breadcrumbs`", "`".$table."`".'.'."`id`".' = '."`joining_table_".$key."_breadcrumbs`".'.id', 'left');
						}
					}
				}


			}
			else {

				// $cols_visible = $this->frontend_data_for_table_view($database, $table);


				if (1==1) {


					foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
						if (isset($value["rels"])) {
							$value_rels_table = $value["rels"]["table"];
							// if ($value_rels_table !== $table) {
							foreach ($value["rels"]["rows"] as $key_2 => $value_2) {
								if ($key_2 == "id") {
									$parent_link_part_1 = '<a href="/record/t/'.$value_rels_table.'/r/';
									$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
									$query = $query->select("CONCAT('$parent_link_part_1', "."`joining_table_".$value_rels_table."`".".id".", '$parent_link_part_2') as `$value_rels_table - $key_2`");
								} elseif (isset($value_2["assumed"])) {

								} else {
									$query = $query->select("`joining_table_".$value_rels_table."`"."."."`".$key_2."`"." as `$value_rels_table - $key_2`");
								}


							}
							if (isset($value["rels"]["is_self_joined"])) {
								// $g_select["visible"] = array_merge(
								// 	$g_select["visible"],
								// 	array("$value_rels_table - breadcrumbs" => "1")
								// );

								$query = $query->select("`joining_table_".$value_rels_table."_breadcrumbs`.path"." as `$value_rels_table - breadcrumbs`");

								// $concat_part_1 = "`joining_table_".$value_rels_table."_breadcrumbs`.path";
								// $concat_part_2 = "`$table`.`id`";
								// $query = $query->select("CONCAT($concat_part_1, "."'-'".", $concat_part_2) as `$value_rels_table - breadcrumbs`");
								// // $query = $query->select("CONCAT('0-', $concat_part_1, '-', $concat_part_2) as `$value_rels_table - breadcrumbs`");
							}
							// }
						} else {
							$query = $query->select("`".$table."`".'.'."`".$key."`");
							// if ($key == "id") {
							// 	// code...
							// 	$parent_link_part_1 = '<a href="/record/t/'.$table.'/r/';
							// 	$parent_link_part_2 = '" class="btn btn-sm btn-outline-primary">View</a>';
							// 	$query = $query->select("CONCAT('$parent_link_part_1', "."`".$table."`".".id, '$parent_link_part_2') as `id`");
							// }

						}
					}
					$query = $query->from("`".$table."`");


					// foreach ($cols_visible["linking_cols"] as $key => $value) {
					// }
					foreach ($cols_visible["g_core_abilities"]["g_select"]["editable"] as $key => $value) {
						if (isset($value["rels"])) {

							$value_rels_table = $value["rels"]["table"];
							// echo "xyz";
							// if ($value_rels_table !== $table) {
							$query = $query->join("`".$value_rels_table."` as `joining_table_".$value_rels_table."`", "`".$table."`".'.'."`".$key."`".' = '."`joining_table_".$value_rels_table."`".'.id', 'left');
							// }

							if (isset($value["rels"]["is_self_joined"])) {
								$linking_key = $key;
								// $sql="
								// ";
								$sql = array(
								"WITH RECURSIVE q AS ",
								"(",
								// "SELECT  id,`$linking_key`, CONCAT('0-', id) as path",
								"SELECT  id,`$linking_key`, CONCAT('', id) as path",
								// "SELECT  id as id,`$linking_key`, id as path",
								"FROM    `$value_rels_table`",
								"WHERE   `$linking_key` = 0",
								"UNION ALL",
								"SELECT  m.id,m.`$linking_key`, CONCAT(q.path, '-', m.id) as path",
								"FROM    `$value_rels_table` m",
								"JOIN    q",
								"ON      m.`$linking_key` = q.id",
								")",
								"SELECT  *",
								// "SELECT  id,`$linking_key`, CONCAT('0-',path, '-', id) as path",
								"FROM    q",

								);
								$sql = implode("\n\r", $sql);
								// $query = $query->join("(".$sql.") as `joining_table_".$value_rels_table."_breadcrumbs`", "`".$table."`".'.'."`$linking_key`".' = '."`joining_table_".$value_rels_table."_breadcrumbs`".'.id', 'left');
								$query = $query->join("(".$sql.") as `joining_table_".$value_rels_table."_breadcrumbs`", "`".$table."`".'.'."`id`".' = '."`joining_table_".$value_rels_table."_breadcrumbs`".'.id', 'left');
							}
						}
					}
				}

			}


			// header('Content-Type: application/json');
			// echo json_encode($cols_visible, JSON_PRETTY_PRINT);
			// exit;


			$query = $query->join("`_activity_log`", "`_activity_log`.`record_table_and_id` = CONCAT('$table', '/', `$table`.id)", 'left');

			// echo json_encode($groups);
			// exit;
			$query = $query->group_start();
			// $query = $query->or_where("`_activity_log`.`owner` =", "0");

			$query = $query->where("`_activity_log`.`editability` =", "'pu'");
			$query = $query->or_where("`_activity_log`.`visibility` =", "'pu'");
			$query = $query->or_where("`_activity_log`.`editability` IS", "NULL");
			$query = $query->or_where("`_activity_log`.`visibility` IS", "NULL");
			$query = $query->or_where("`_activity_log`.`owner` =", "2");




			if (!empty($groups)) {
				// code...
				$query = $query->or_where_in("`_activity_log`.`owner`", $groups);
			}

			$query = $query->where("`_activity_log`.`owner` =", $active_groups_dropdown);

			$query = $query->group_end();
			// $query = $query->or_where_in("`_activity_log`.`editability`", array("'Public'", "''"));
			// $query = $query->or_where_in("`_activity_log`.`visibility`", array("'Public'", "''"));

			if ($page_type == "record") {
				$where["haystack"] = urldecode($where["haystack"]);

				// echo "`".$table."`"."."."`".$where["haystack"]."` =". '"'.$where["needle"].'"';
				// exit;
				$query = $query->where("`".$table."`"."."."`".$where["haystack"]."` =", '"'.$where["needle"].'"');
			}
			elseif ($page_type == "table") {
			}



			$sql = $query->_compile_select();
			// echo $sql;
			// exit;

			// $query = $query->get();

		}



		if ("old"=="!old") {
			$posts = $query;
			$num_rows = $query->num_rows();
			$posts = $posts->result_array();

			// print_r($this->CI->db->last_query());
			// exit;


			$this->CI->db->_protect_identifiers=true;




			$this->CI->db->_protect_identifiers=false;
			$recordsTotal = $this->CI->db->select('COUNT(`id`)')->from("`$table`")->get()->result_array()[0]["COUNT(`id`)"];
			$this->CI->db->_protect_identifiers=true;

			$data = array(
				"draw" => isset ( $request['draw'] ) ? intval( $request['draw'] ) : 0,
				'recordsTotal' => intval( $recordsTotal ),
				'recordsFiltered' => intval( $recordsTotal ),
				// 'recordsTotal' => intval( $recordsTotal ),
				// 'recordsFiltered' => $num_rows,
				'data' => $posts
			);

			// header('Content-Type: application/json');
			// echo $this->CI->db->last_query();
			// exit;
		}
		return $sql;
	}


	public function filter($request, $columns)
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = $this->CI->ssp->pluck( $columns, 'dt' );

		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['searchable'] == 'true' ) {
					if(!empty($column['db'])){
						// $binding = $this->CI->ssp->bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
						$binding = "'%".$this->mssql_escape($str)."%'";
						$globalSearch[] = "`".$column['db']."` LIKE ".$binding;
					}
				}
			}
		}

		// Individual column filtering
		if ( isset( $request['columns'] ) ) {
			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				$str = $requestColumn['search']['value'];

				if ( $requestColumn['searchable'] == 'true' &&
				$str != '' ) {
					if(!empty($column['db'])){
						// $binding = $this->CI->ssp->bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
						$binding = "'%".$this->mssql_escape($str)."%'";
						$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
					}
				}
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'WHERE '.$where;
		}

		return $where;
	}


	function mssql_escape($data) {
		return str_replace("'", "''", $data );
	}



}
