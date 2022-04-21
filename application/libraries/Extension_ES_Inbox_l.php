<?php
class Extension_ES_Inbox_l
{
	private $CI;

	function __construct()
	{
		// $this->load->helper(array('form', 'url'));
		// $this->load->library('form_validation');

		$this->CI =& get_instance();
		$this->CI->load->database();
	}

  public function valid_Extension_ES_Integration_engines_json()
  {
    // if ($toggle_interpretation_has_errors !== true) {
    // 	if (isset($_GET["integration_engine"])) {
    //
    // 		$GET_integration_engine = $_GET["integration_engine"].".json";
    // 		$file_application_Extension_ES_Integration_engines = "application/Extension_ES_Integration_engines";
    // 		$scandir_application_Extension_ES_Integration_engines = scandir($file_application_Extension_ES_Integration_engines);
    //
    // 	} else {
    // 		$toggle_interpretation_has_errors = true;
    // 		$status_details = '...'.' ('.'isset($_GET["integration_engine"])'.')';
		// $status_details = array(
		// 	"label" => '',
		// 	"code" => "",
		// 	"sub_status" => array()
		// );
    //
    // 	}
    // }

		$toggle_interpretation_has_errors = false;

    if ($toggle_interpretation_has_errors !== true) {

      $integration_engine = "latest.json";
      $file_application_Extension_ES_Integration_engines = "application/Extension_ES_Integration_engines";
      $scandir_application_Extension_ES_Integration_engines = scandir($file_application_Extension_ES_Integration_engines);

      if (in_array($integration_engine, $scandir_application_Extension_ES_Integration_engines)) {
        $file_integration_engine = "application/Extension_ES_Integration_engines/".$integration_engine;
        // $scandir_integration_engine = scandir($file_integration_engine);
        $file_get_contents_Extension_ES_Integration_engines_json = file_get_contents($file_integration_engine);


      } else {
        $toggle_interpretation_has_errors = true;
        // $status_details = '...'.' ('.'in_array($integration_engine, $scandir_application_Extension_ES_Integration_engines)'.')';
        $status_details = array(
          "label" => '',
          "code" => "in_array(\$integration_engine, \$scandir_application_Extension_ES_Integration_engines)",
          "sub_status" => array()
        );
      }
    }

    if ($toggle_interpretation_has_errors !== true) {
      if ($file_get_contents_Extension_ES_Integration_engines_json !== '') {
        $json_decode_Extension_ES_Integration_engines_json = json_decode($file_get_contents_Extension_ES_Integration_engines_json, true);


      } else {
        $toggle_interpretation_has_errors = true;
        // $status_details = '...'.' ('.'$file_get_contents_Extension_ES_Integration_engines_json !== '''.')';
        $status_details = array(
          "label" => '',
          "code" => "\$file_get_contents_Extension_ES_Integration_engines_json !== ''",
          "sub_status" => array()
        );
      }
    }

    if ($toggle_interpretation_has_errors !== true) {
      if ($json_decode_Extension_ES_Integration_engines_json !== null) {

        $valid_Extension_ES_Integration_engines_json = $json_decode_Extension_ES_Integration_engines_json;

        $toggle_interpretation_has_errors = false;
      } else {
        $toggle_interpretation_has_errors = true;
        $status_details = '...'.' ('.'$json_decode_Extension_ES_Integration_engines_json !== null'.')';
        $status_details = array(
          "label" => '',
          "code" => "\$json_decode_Extension_ES_Integration_engines_json !== null",
          "sub_status" => array()
        );
      }
    }

    if ($toggle_interpretation_has_errors !== true) {
			return array(
				"status"=>true,
				"status_details"=>"",
				"data"=>$valid_Extension_ES_Integration_engines_json
			);
    } else {
			return array(
				"status"=>false,
				"status_details"=>$status_details,
				"data"=>""
			);
    }


  }

	public function pre_json($var)
	{
		echo "<pre>";
		echo json_encode($var, JSON_PRETTY_PRINT);
		echo "</pre>";
	}

	public function pre_echo($var)
	{
		echo "<pre>";
		echo $var;
		echo "</pre>";
	}



  public function find_nested_array($haystack, $field, $needle) {
		$status_details = '';
		$status = true;
    $data = "";

		if ($status !== false) {
			foreach ($haystack as $key => $val) {

				if ($val[$field] === $needle) {
					// return $val;
					$data = $val;
				}
			}

			if (is_array($data)) {

			} else {
				$status = false;
				// $status_details = '...'.' ('.'is_array($data)'.')';
        $status_details = array(
          "label" => '',
          "code" => "is_array(\$data)",
          "sub_status" => array()
        );
			}
		}

		return array(
			"status"=>$status,
			"status_details"=>$status_details,
			"data"=>$data
		);
  }



  public function provider($delivery_destination) {


    $status = true;

    if ($status !== false) {
      if ($this->valid_Extension_ES_Integration_engines_json()['status'] !== false) {

        $valid_Extension_ES_Integration_engines_json = $this->valid_Extension_ES_Integration_engines_json()["data"];

      } else {
        $status = false;
        // $status_details = '...'.' ('.'$this->valid_Extension_ES_Integration_engines_json()["status"] !== false'.')';
        $status_details = array(
          "label" => '',
          "code" => "\$this->valid_Extension_ES_Integration_engines_json()['status'] !== false",
          "sub_status" => array()
        );
      }
    }

		if ($status !== false) {

			$delivery_destination_value_func = $this->find_nested_array($valid_Extension_ES_Integration_engines_json["delivery_destinations"], "id", $delivery_destination);

			if ($delivery_destination_value_func['status'] !== false) {

				$delivery_destination_value = $delivery_destination_value_func["data"];

			} else {
				$status = false;
				// $status_details = '...'.' ('.$delivery_destination_value_func["status_details"].')';
        $status_details = array(
          "label" => '',
          "code" => "\$delivery_destination_value_func['status'] !== false",
          "sub_status" => $delivery_destination_value_func['status_details']
        );
			}
		}

    if ($status !== false) {


      if ($this->auth_creds($delivery_destination_value)['status'] !== false) {

        $auth_creds = $this->auth_creds($delivery_destination_value)["data"];

      } else {
        $status = false;
        // $status_details = '...'.' ('..')';
        $status_details = array(
          "label" => '',
          "code" => "\$this->auth_creds(\$delivery_destination_value)['status'] !== false",
          "sub_status" => $this->auth_creds($delivery_destination_value)["status_details"]
        );
      }
    }



    if ($status !== false) {

      $provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => $auth_creds["clientId"],    // The client ID assigned to you by the provider
        'clientSecret'            => $auth_creds["clientSecret"],   // The client password assigned to you by the provider
        'redirectUri'             => $auth_creds["redirectUri"],
        'urlAuthorize'            => $auth_creds["urlAuthorize"],
        // 'urlAuthorize'            => 'https://accounts.zoho.com/oauth/v2/auth?scope=aaaserver.profile.READ&access_type=offline',
        'urlAccessToken'          => $auth_creds["urlAccessToken"],
        // 'urlResourceOwnerDetails' => 'https://accounts.zoho.com/oauth/user/info'
        'urlResourceOwnerDetails' => $auth_creds["urlResourceOwnerDetails"],
      ]);


    }

    if ($status !== false) {
      return array(
        "status"=>true,
        "status_details"=>"",
        "data"=>$provider
      );
    } else {
      return array(
        "status"=>false,
        "status_details"=>$status_details,
        "data"=>""
      );
    }
  }


  public function auth_creds($value)
  {


		$has_auth_creds_status = true;

    if ($has_auth_creds_status !== false) {
      if (isset($value['authorization']['clientId'])) {
        $result["clientId"] = $value["authorization"]["clientId"];
      } else {
        $has_auth_creds_status = false;
        // $status_details = '...'.' ('.'isset($value["authorization"]["clientId"])'.')';
        $status_details = array(
          "label" => '',
          "code" => "isset(\$value['authorization']['clientId'])",
          "sub_status" => array()
        );
      }
    }
    if ($has_auth_creds_status !== false) {
      if (isset($value['authorization']['clientSecret'])) {
        $result["clientSecret"] = $value["authorization"]["clientSecret"];

      } else {
        $has_auth_creds_status = false;
        // $status_details = '...'.' ('.'isset($value["authorization"]["clientSecret"])'.')';
        $status_details = array(
          "label" => '',
          "code" => "isset(\$value['authorization']['clientSecret'])",
          "sub_status" => array()
        );
      }
    }
    if ($has_auth_creds_status !== false) {
      if (isset($value['authorization']['redirectUri'])) {
        $result["redirectUri"] = $value["authorization"]["redirectUri"];

      } else {
        $has_auth_creds_status = false;
        // $status_details = '...'.' ('.'isset($value["authorization"]["redirectUri"])'.')';
        $status_details = array(
          "label" => '',
          "code" => "isset(\$value['authorization']['redirectUri'])",
          "sub_status" => array()
        );
      }
    }
    if ($has_auth_creds_status !== false) {
      if (isset($value['authorization']['urlAuthorize'])) {
        $result["urlAuthorize"] = $value["authorization"]["urlAuthorize"];

      } else {
        $has_auth_creds_status = false;
        // $status_details = '...'.' ('.'isset($value["authorization"]["urlAuthorize"])'.')';
        $status_details = array(
          "label" => '',
          "code" => "isset(\$value['authorization']['urlAuthorize'])",
          "sub_status" => array()
        );
      }
    }
    if ($has_auth_creds_status !== false) {
      if (isset($value['authorization']['urlAccessToken'])) {
        $result["urlAccessToken"] = $value["authorization"]["urlAccessToken"];

      } else {
        $has_auth_creds_status = false;
        // $status_details = '...'.' ('.'isset($value["authorization"]["urlAccessToken"])'.')';
        $status_details = array(
          "label" => '',
          "code" => "isset(\$value['authorization']['urlAccessToken'])",
          "sub_status" => array()
        );
      }
    }
    if ($has_auth_creds_status !== false) {
      if (isset($value['authorization']['urlResourceOwnerDetails'])) {
        $result["urlResourceOwnerDetails"] = $value["authorization"]["urlResourceOwnerDetails"];

      } else {
        $has_auth_creds_status = false;
        // $status_details = '...'.' ('.'isset($value["authorization"]["urlResourceOwnerDetails"])'.')';
        $status_details = array(
          "label" => '',
          "code" => "isset(\$value['authorization']['urlResourceOwnerDetails'])",
          "sub_status" => array()
        );
      }
    }

    if ($has_auth_creds_status !== false) {
			return array(
				"status"=>true,
				"status_details"=>"",
				"data"=>$result
			);
    } else {
			return array(
				"status"=>false,
				"status_details"=>$status_details,
				"data"=>""
			);
    }


  }


}
