<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extension_ES_Inbox_c extends CI_Controller {

  function __construct()
  {
    parent::__construct();

    // $this->load->library('erd_lib');



    $this->load->library([
      'Extension_ES_Inbox_l'
    ]);

    $this->load->database();

  }

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function inbox()
	{
    // file_put_contents("test.txt", date('m/d/Y h:i:s a', time()));
		// e.g. https://red.bluegemify.co.za/sync/inbox?source=sendinblueivan
		$this->load->database();

		$status = true;
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );

    $verb = "";

    $source_name = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $connection = $source_name;
    $integration_engine = "";

    if (isset($_SERVER['HTTP_CLIENT_IP']) ) {
      $debug_initiator = "HTTP_CLIENT_IP: ".$_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
      $debug_initiator = "HTTP_X_FORWARDED_FOR: ".$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $debug_initiator = "REMOTE_ADDR: ".$_SERVER['REMOTE_ADDR'];
    }

		if ($status !== false) {
			if (isset($_GET['integration_engine'])) {

				$integration_engine = $_GET['integration_engine'];

			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_GET['integration_engine'])",
          "sub_status" => array()
        );

			}
		}

		if ($status !== false) {
			if ($this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()['status'] !== false) {

				$valid_Extension_ES_Integration_engines_json = $this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()["data"];

				$status = true;
			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()['status'] !== false",
          "sub_status" => array()
        );

			}
		}


		if ($status !== false) {
			if (isset($valid_Extension_ES_Integration_engines_json['delivery_sources'])) {

				$delivery_sources = $valid_Extension_ES_Integration_engines_json["delivery_sources"];

				$status = true;
			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$valid_Extension_ES_Integration_engines_json['delivery_sources'])",
          "sub_status" => array()
        );
			}
		}

		if ($status !== false) {
			if (isset($_GET['source'])) {

				$source_name = $_GET['source'];
        $connection = $source_name;
				// $status = true;
			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_GET['source'])",
          "sub_status" => array()
        );
			}
		}


    if ($status !== false) {
      // $source_name = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

      $source_value_func = $this->extension_es_inbox_l->find_nested_array($delivery_sources, "id", $source_name);

      if ($source_value_func['status'] !== false) {

        $source_value = $source_value_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$source_value_func['status'] !== false",
          "sub_status" => $source_value_func["status_details"]
        );
      }
    }

		if ($status !== false) {
			$php_input = file_get_contents('php://input');
			$json_decode_php_input = json_decode($php_input, true);
			if ($json_decode_php_input !== null & is_array($json_decode_php_input)) {

				$status = true;
			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$json_decode_php_input !== null & is_array(\$json_decode_php_input)",
          "sub_status" => array()
        );
			}
		}

		if ($status !== false) {
			if (isset($source_value['map']) & is_array($source_value['map'])) {

				$source_map = $source_value['map'];

				$status = true;
			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$source_value['map']) & !is_array(\$source_value['map'])",
          "sub_status" => array()
        );
			}
		}



  	if ($status !== false) {
  		if (isset($source_map['event_type_trigger']) & is_string($source_map['event_type_trigger'])) {

  			$event_type_trigger = $source_map['event_type_trigger'];

  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$source_map['event_type_trigger']) & is_string(\$source_map['event_type_trigger'])",
          "sub_status" => array()
        );
  		}
  	}

  	if ($status !== false) {

      if ($status !== false) {

        $element_read_event_type_trigger_func = $this->element_read($event_type_trigger, $json_decode_php_input);

        if ($element_read_event_type_trigger_func['status'] !== false) {

          $element_read_event_type_trigger = $element_read_event_type_trigger_func["data"];
          $verb = $element_read_event_type_trigger;

        } else {
          $status = false;
          $status_details = array(
            "label" => '',
            "code" => "\$element_read_event_type_trigger_func['status'] !== false",
            "sub_status" => $element_read_event_type_trigger_func["status_details"]
          );
        }
      }



      $event_type_func = $this->extension_es_inbox_l->find_nested_array($source_map["event_types"], "id", $element_read_event_type_trigger);

      if ($event_type_func['status'] !== false) {

        $event_type = $event_type_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => 'Source (del.) case is not supported.',
          "code" => "\$event_type_func['status'] !== false",
          "sub_status" => $event_type_func["status_details"]
        );
      }
  	}

  	if ($status !== false) {

      if (isset($event_type['fields'])) {

        $field_map = $event_type['fields'];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$event_type['fields'])",
          "sub_status" => array()
        );
      }
  	}

    if ($status !== false) {
      $interpretation_func = $this->translation($field_map, $json_decode_php_input);
      if ($interpretation_func['status'] !== false) {

        $interpretation = $interpretation_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$interpretation_func['status'] !== false",
          "sub_status" => $interpretation_func["status_details"]
        );
      }
    }



		if ($status !== false) {
			$interpretation_status_label = "success (in)";

			$debug_reply = $interpretation_status_label;

      $json_encode_interpretation = json_encode($interpretation, JSON_PRETTY_PRINT);
      $debug_message = $json_encode_interpretation;

		} else {
			$interpretation_status_label = "error (in)";
			$debug_reply = "";
			$debug_reply = $debug_reply."<pre>";
      // $debug_reply = $debug_reply.$interpretation_status_label." - ".str_replace('"','\"', json_encode($status_details, JSON_PRETTY_PRINT));
      $debug_reply = $debug_reply.$interpretation_status_label." - ".json_encode($status_details, JSON_PRETTY_PRINT);
			$debug_reply = $debug_reply."</pre>";

      $request["php://input"] = file_get_contents('php://input');
      $debug_message = $request["php://input"];

		}

    $insert_data_escaped = array();

    $request["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];


    // if (1==1) {
    //   if (isset($_SERVER['HTTP_CLIENT_IP']) ) {
    //     $ip = "HTTP_CLIENT_IP ".$_SERVER['HTTP_CLIENT_IP'];
    //   } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
    //     $ip = "HTTP_X_FORWARDED_FOR".$_SERVER['HTTP_X_FORWARDED_FOR'];
    //   } else {
    //     $ip = "REMOTE_ADDR".$_SERVER['REMOTE_ADDR'];
    //   }
    //   // $ip = isset($_SERVER['HTTP_CLIENT_IP'])
    //   // ? $_SERVER['HTTP_CLIENT_IP']
    //   // : isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    //   // ? $_SERVER['HTTP_X_FORWARDED_FOR']
    //   // : $_SERVER['REMOTE_ADDR'];
    //
    //   $connection = $connection.$_SERVER['REQUEST_METHOD'].$ip
    // }

    $insert_data = array(
      "date"=> date('Y-m-d H:i:s'),
      "ES Integration engine"=> $integration_engine,
      // "direction"=> "In",
      "method"=> "Push",
      // "connection"=> $request["REMOTE_ADDR"],
      // "connection"=> $request["URL"],
      "connection"=> $connection,
      "debug_initiator"=> $debug_initiator,
      "verb"=> $verb,
      "debug_status"=> $interpretation_status_label,
      "debug_message"=> $debug_message,
      "debug_reply"=> $this->strip_html($debug_reply),
      "debug_status_details"=> json_encode($status_details, JSON_PRETTY_PRINT),
      // "ES Message"=> null
    );

    if (isset($interpretation["subject"])) {
      $insert_data["subject"] = $interpretation["subject"];
    }
    if (isset($interpretation["object"])) {
      $insert_data["object"] = $interpretation["object"];
    }

    if ($status) {
      $insert_data["debug_message"] = "";
      $insert_data["debug_reply"] = "";
      $insert_data["debug_status_details"] = "";
    }

    foreach ($insert_data as $key => $value) {
      // code...
      // $insert_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', $value).'"';
      $insert_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', str_replace('\\','\\\\', $value)).'"';
    }



    // $this->extension_es_inbox_l->pre_json($insert_data_escaped);

    $table = "ES Messages";


    $this->db->_protect_identifiers=false;
    $query_result = $this->db->insert("`$table`", $insert_data_escaped);
    $this->db->_protect_identifiers=true;


    echo $debug_reply;


    if ($status !== false) {
      $insert_id = $this->db->insert_id();
      $articulate_to_destination_func = $this->articulate_to_destination($source_name, $source_value, $valid_Extension_ES_Integration_engines_json, $interpretation, $insert_id, $integration_engine);

      if ($articulate_to_destination_func['status'] !== false) {

        $articulate_to_destination = $articulate_to_destination_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$articulate_to_destination_func['status'] !== false",
          "sub_status" => $articulate_to_destination_func["status_details"]
        );
      }
    }

    if ($status !== false) {
			$articulate_to_destination_status_label = "success (out)";

			$debug_reply = $articulate_to_destination_status_label;

      $json_encode_interpretation = json_encode($interpretation, JSON_PRETTY_PRINT);
      $debug_message = $json_encode_interpretation;

		} else {
			$articulate_to_destination_status_label = "error (out)";
			$debug_reply = "";
			$debug_reply = $debug_reply."<pre>";
      // $debug_reply = $debug_reply.$articulate_to_destination_status_label." - ".str_replace('"','\"', json_encode($status_details, JSON_PRETTY_PRINT));
      $debug_reply = $debug_reply.$articulate_to_destination_status_label." - ".json_encode($status_details, JSON_PRETTY_PRINT);
			$debug_reply = $debug_reply."</pre>";

      $request["php://input"] = file_get_contents('php://input');
      $debug_message = $request["php://input"];

		}


    echo $debug_reply;



	}

	public function articulate_to_destination($source_name, $source_value, $valid_Extension_ES_Integration_engines_json, $interpretation, $insert_id, $integration_engine)
	{

    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );
		$status = true;
    $data = "";

    // $receiver = "";
    $connection = "";
    $debug_reply = "";
    $articulation = array();
    $json_encode_articulation = "";
    $verb = "";



    $debug_initiator = "SERVER_ADDR: ".$_SERVER['SERVER_ADDR'];


		if ($status !== false) {
			if (isset($source_value["linked_delivery_destinations"]) & is_array($source_value["linked_delivery_destinations"])) {

				$source_linked_delivery_destinations = $source_value["linked_delivery_destinations"];

			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$source_value['map']) & !is_array(\$source_value['map'])",
          "sub_status" => array()
        );
			}
		}

		if ($status !== false) {
			if (isset($valid_Extension_ES_Integration_engines_json['delivery_destinations']) & is_array($valid_Extension_ES_Integration_engines_json['delivery_destinations'])) {

				$delivery_destinations = $valid_Extension_ES_Integration_engines_json['delivery_destinations'];

			} else {
				$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$valid_Extension_ES_Integration_engines_json['delivery_destinations']) & is_array(\$valid_Extension_ES_Integration_engines_json['delivery_destinations'])",
          "sub_status" => array()
        );

			}
		}


    if ($status !== false) {
      if (!empty($source_linked_delivery_destinations)) {

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "empty(\$source_linked_delivery_destinations)",
          "sub_status" => array()
        );
      }
    }

		if ($status !== false) {

			foreach ($source_linked_delivery_destinations as $key_1 => $destination) {
        $status = true;
        $debug_reply = "";
        $articulation = array();

        // $receiver = $destination;
        $headers = array();

        if ($status !== false) {

          $destination_value_func = $this->extension_es_inbox_l->find_nested_array($delivery_destinations, "id", $destination);

          if ($destination_value_func['status'] !== false) {


            $connection = $destination;
						if ($status !== false) {

              $destination_value = $destination_value_func["data"];
							if (isset($destination_value['map']) & is_array($destination_value['map'])) {

								$destination_map = $destination_value['map'];

							} else {
								$status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "isset(\$destination_value['map']) & is_array(\$destination_value['map'])",
                  "sub_status" => array()
                );
							}
						}

						if ($status !== false) {

							if (isset($destination_value['url'])) {

								$destination_url = $destination_value['url'];

							} else {
								$status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "isset(\$destination_value['url'])",
                  "sub_status" => array()
                );
							}
						}

            if ($status !== false) {
              if (isset($destination_map['event_type_trigger']) & is_string($destination_map['event_type_trigger'])) {

                $event_type_trigger = $destination_map['event_type_trigger'];

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "isset(\$destination_map['event_type_trigger']) & is_string(\$destination_map['event_type_trigger'])",
                  "sub_status" => array()
                );
              }
            }


            if ($status !== false) {

              $element_read_event_type_trigger_func = $this->element_read($event_type_trigger, $interpretation);

              if ($element_read_event_type_trigger_func['status'] !== false) {

                $element_read_event_type_trigger = $element_read_event_type_trigger_func["data"];
                $verb = $element_read_event_type_trigger;
              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$element_read_event_type_trigger_func['status'] !== false",
                  "sub_status" => $element_read_event_type_trigger_func["status_details"]
                );

              }
            }


            if ($status !== false) {

              $find_nested_array_destination_map_event_types_func = $this->extension_es_inbox_l->find_nested_array($destination_map["event_types"], "id", $element_read_event_type_trigger);

              if ($find_nested_array_destination_map_event_types_func['status'] !== false) {

                $event_type = $find_nested_array_destination_map_event_types_func["data"];

              } else {
                $status = false;
                $status_details = array(
                  "label" => 'No destination case matching this source case exists.'. $event_type_trigger,
                  "code" => "\$find_nested_array_destination_map_event_types_func['status'] !== false",
                  "sub_status" => $find_nested_array_destination_map_event_types_func["status_details"]
                );
              }
            }

            if ($status !== false) {

              if (isset($event_type['fields'])) {

                $field_map = $event_type['fields'];

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "isset(\$event_type['fields'])",
                  "sub_status" => array()
                );

              }
            }



            if ($status !== false) {
              $articulation_func = $this->translation($field_map, $interpretation);
              if ($articulation_func['status'] !== false) {

                $articulation = $articulation_func["data"];
                $json_encode_articulation = json_encode($articulation, JSON_PRETTY_PRINT);

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$articulation_func['status'] !== false",
                  "sub_status" => $articulation_func["status_details"]
                );
              }
            }

            if ($status !== false) {

              $fresh_access_token_func = $this->fresh_access_token($destination);
              if ($fresh_access_token_func['status'] !== false) {

                $fresh_access_token = $fresh_access_token_func["data"];
                // echo $fresh_access_token;
              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$fresh_access_token_func['status'] !== false",
                  "sub_status" => $fresh_access_token_func["status_details"]
                );

              }

            }


            if ($status !== false) {
              $provider_func = $this->extension_es_inbox_l->provider($destination);
              if ($provider_func['status'] !== false) {

                $provider = $provider_func["data"];

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$provider_func['status'] !== false",
                  "sub_status" => $provider_func["status_details"]
                );

              }
            }

            if ($status !== false) {
              $provider_func = $this->extension_es_inbox_l->provider($destination);
              if ($provider_func['status'] !== false) {

                $provider = $provider_func["data"];

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$provider_func['status'] !== false",
                  "sub_status" => $provider_func["status_details"]
                );

              }
            }

            if ($status !== false) {
              if (isset($event_type['request_type'])) {

                $event_type_request_type = $event_type['request_type'];

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "isset(\$event_type['request_type'])",
                  "sub_status" => array()
                );

              }
            }

            if ($status !== false) {
              if (in_array($event_type_request_type, array('POST', 'GET', 'PUT', 'DEL'))) {

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "in_array(\$event_type_request_type, array('POST', 'GET', 'PUT', 'DEL'))",
                  "sub_status" => array()
                );

              }
            }

            if ($status !== false) {
              if (isset($event_type['request_suffix'])) {

                $event_type_request_suffix = $event_type['request_suffix'];

              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "isset(\$event_type['request_suffix'])",
                  "sub_status" => array()
                );

              }
            }

            if (isset($event_type['headers'])) {

              if ($status !== false) {
                if (is_array($event_type['headers'])) {

                  $headers = $event_type['headers'];

                } else {
                  $status = false;
                  $status_details = array(
                    "label" => '',
                    "code" => "is_array(\$event_type['headers'])",
                    "sub_status" => array()
                  );

                }
              }

            }

						if ($status !== false) {
							// $debug_reply = file_get_contents($destination);

              $end_point = $destination_url.$event_type_request_suffix;
              // $receiver = $end_point;


              if ($event_type_request_type == "POST") {
                $headers["content-type"] = "application/json";
                // "access_token"=> $fresh_access_token



                $body = $json_encode_articulation;
                if (isset($event_type['payload_type'])) {
                  $payload_type = $event_type['payload_type'];
                  if ($payload_type=="x-www-form-urlencoded") {
                    $body = http_build_query($articulation);
                    $headers["content-type"] = 'application/x-www-form-urlencoded';
                  }
                }

                $getAuthenticatedRequest = $provider->getAuthenticatedRequest(
                  $event_type_request_type,
                  $end_point,
                  $fresh_access_token,
                  array(
                    "body"=>$body,
                    "headers" => $headers
                  )
                );
              } else {
                $getAuthenticatedRequest = $provider->getAuthenticatedRequest(
                  $event_type_request_type,
                  $end_point,
                  $fresh_access_token
                );
              }
              $debug_reply_getParsedResponse = $provider->getParsedResponse($getAuthenticatedRequest);
              $debug_reply = json_encode($debug_reply_getParsedResponse);

              // $debug_reply = json_encode($debug_reply, JSON_PRETTY_PRINT);

							if ($debug_reply !== null) {

							} else {
								$status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$debug_reply !== null",
                  "sub_status" => array()
                );

							}
						}
						if ($status !== false) {
              $success_check_func = $this->success_check($event_type, $debug_reply_getParsedResponse);
              if ($success_check_func['status'] !== false) {

                $success_check = $success_check_func["data"];
              } else {
                $status = false;
                $status_details = array(
                  "label" => '',
                  "code" => "\$success_check_func['status'] !== false",
                  "sub_status" => $success_check_func["status_details"]
                );

              }
						}





            // success_check_where_field success_check_where_value

            //
						// if ($status !== false) {
            //
						// 	if ($debug_reply == $destination_value["expected_debug_reply"]) {
            //
						// 	} else {
						// 		$status = false;
						//
            // $status_details = array(
            //   "label" => '',
            //   "code" => "\$debug_reply == $destination_value['expected_debug_reply']",
            //   "sub_status" => array()
            // );
						// 	}
						// }



          } else {
            $status = false;
            $status_details = array(
              "label" => '',
              "code" => "\$destination_value_func['status'] !== false",
              "sub_status" => $destination_value_func["status_details"]
            );

          }
        }


        // if ($status !== false) {
        // } else {
        //   return array(
        //     "status"=>$status,
        //     "status_details"=>$status_details,
        //     "data"=>$data
        //   );
        // }


			}
		}







    $insert_data_escaped = array();


    if ($status !== false) {

      $status_string = "success (out)";

    } else {
      $status_string = "error (out)";
      //
      // $status_details = array(
      //   "label" => '',
      //   "code" => "",
      //   "sub_status" => $articulation_error
      // );




    }

    $insert_data = array(
      "date"=> date('Y-m-d H:i:s'),
      // "ES Integration engine"=> $integration_engine,
      // "direction"=> "Out",
      // "method"=> "Push",
      // "connection"=> $connection,
      // "connection"=> $receiver,
      // "debug_initiator"=> $debug_initiator,
      // "verb"=> $verb,
      "debug_status"=> $status_string,
      "debug_message"=> $json_encode_articulation,
      "debug_reply"=> $this->strip_html($debug_reply),
      "debug_status_details"=> json_encode($status_details, JSON_PRETTY_PRINT),
      // "ES Message"=> ""
    );

    if ($status) {
      $insert_data["debug_message"] = "";
      $insert_data["debug_reply"] = "";
      $insert_data["debug_status_details"] = "";
    }

    foreach ($insert_data as $key => $value) {
      // code...
      $insert_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', str_replace('\\','\\\\', $value)).'"';
    }



    // $this->extension_es_inbox_l->pre_json($insert_data_escaped);

    $table = "ES Messages";


    $this->db->_protect_identifiers=false;
    $this->db->where('id', $insert_id);
    $query_result = $this->db->update("`$table`", $insert_data_escaped);
    $this->db->_protect_identifiers=true;


    return array(
      "status"=>$status,
      "status_details"=>$status_details,
      "data"=>$data
    );

	}

	// public function element_read("", "dddddddddd")
	public function element_read($needle, $haystack)
	{
		$explode_needle = explode("/", $needle);
		$haystack_value = $haystack;

    $status = true;
    $status_details = '';

    if ($needle == "") {
      $haystack_value = $haystack_value;
    } else {
      foreach ($explode_needle as $key => $value) {
        if (isset($haystack_value[$value])) {
          $haystack_value = $haystack_value[$value];
        } else {
          $haystack_value = "";

          $status = false;
          $status_details = array(
            "label" => 'Element value ('.$value.') does not exist.',
            "code" => "isset(\$haystack_value[\$value])",
            "sub_status" => array()
          );


        }
      }
    }
    return array(
      "status"=>$status,
      "status_details"=>$status_details,
      "data"=>$haystack_value
    );
	}

	public function element_write($needle_key, $needle_value, $haystack)
	{
		$explode_needle = explode("/", $needle_key);
		$count_explode_needle = count($explode_needle);
		$EN = $explode_needle;

    if (is_numeric($needle_value) OR is_bool($needle_value)) {
      $needle_value = "$needle_value";
    } elseif (is_array($needle_value)) {
      $needle_value = json_encode($needle_value);
    }

		if ($count_explode_needle == 0) {
			$haystack = $needle_value;
		}
		if ($count_explode_needle == 1) {
			$haystack[$EN[0]] = $needle_value;
		}
		if ($count_explode_needle == 2) {
			$haystack[$EN[0]][$EN[1]] = $needle_value;
		}
		if ($count_explode_needle == 3) {
			$haystack[$EN[0]][$EN[1]][$EN[2]] = $needle_value;
		}
		if ($count_explode_needle == 4) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]] = $needle_value;
		}
		if ($count_explode_needle == 5) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]][$EN[4]] = $needle_value;
		}
		if ($count_explode_needle == 6) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]][$EN[4]][$EN[5]] = $needle_value;
		}
		if ($count_explode_needle == 7) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]][$EN[4]][$EN[5]][$EN[6]] = $needle_value;
		}
		if ($count_explode_needle == 8) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]][$EN[4]][$EN[5]][$EN[6]][$EN[7]] = $needle_value;
		}
		if ($count_explode_needle == 9) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]][$EN[4]][$EN[5]][$EN[6]][$EN[7]][$EN[8]] = $needle_value;
		}
		if ($count_explode_needle == 10) {
			$haystack[$EN[0]][$EN[1]][$EN[2]][$EN[3]][$EN[4]][$EN[5]][$EN[6]][$EN[7]][$EN[8]][$EN[9]] = $needle_value;
		}


		return $haystack;
	}

	public function translation($field_map, $input)
	{
    $status = true;
    $translation = array();
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );

  	if ($status !== false) {
  		$translation = array();
  		foreach ($field_map as $key => $value) {
  			if ($status !== false) {

          if ($status !== false) {

            $element_read_func = $this->element_read($value["in_"], $input);

            if ($element_read_func['status'] !== false OR !isset($value['in_required'])) {

              if ($element_read_func['status'] !== false) {
                $element_read = $element_read_func["data"];
              } else {
                $element_read = "";
              }

            } else {
              $status = false;
              $status_details = array(
                "label" => '',
                "code" => "\$element_read_func['status'] !== false OR !isset(\$value['in_required'])",
                "sub_status" => $element_read_func["status_details"]
              );

            }
          }

          if ($status !== false) {

            if (isset($value['indexed_array_fields'])) {

              $element_read_mapped_indexed_array_fields = $element_read;

              if ($status !== false) {
                if (is_array($element_read_mapped_indexed_array_fields)) {

                } else {
                  $status = false;
                  $status_details = array(
                    "label" => '',
                    "code" => "is_array(\$element_read_mapped_indexed_array_fields)",
                    "sub_status" => array()
                  );

                }
              }

              if ($status !== false) {
                if (is_array($value['indexed_array_fields'])) {

                  $element_read_mapped_var = array();
                  foreach ($element_read_mapped_indexed_array_fields as $key_2 => $value_2) {

                    if ($status !== false) {

                      $indexed_array_fields = array();
                      foreach ($value['indexed_array_fields'] as $key_3 => $value_3) {


                        if ($status !== false) {
                          // $element_read_value_3_func = $this->element_read($value_3["in_"], $value_2);
                          //
                          // if ($element_read_value_3_func['status'] !== false) {
                          //
                          //   $element_read_value_3 = $element_read_value_3_func["data"];
                          //
                          $element_read_value_3_func = $this->element_read($value_3["in_"], $value_2);

                          if ($element_read_value_3_func['status'] !== false OR !isset($value_3['in_required'])) {

                            if ($element_read_value_3_func['status'] !== false) {
                              $element_read_value_3 = $element_read_value_3_func["data"];
                            } else {
                              $element_read_value_3 = "";
                            }

                          } else {
                            $status = false;
                            $status_details = array(
                              "label" => 'Tryna intepret field that isnt available.',
                              "code" => "\$element_read_value_3_func['status'] !== false",
                              "sub_status" => $element_read_value_3_func["status_details"]
                            );

                          }
                        }






                        if ($status !== false) {
                          // $indexed_array_fields = $this->element_write($value_3["out"], $element_read_value_3, $indexed_array_fields);

                          $indexed_array_fields_func = $this->element_write_and_apply_func($value_3, $element_read_value_3, $indexed_array_fields);
                          if ($indexed_array_fields_func['status'] !== false) {

                            $indexed_array_fields = $indexed_array_fields_func["data"];
                          } else {
                            $status = false;
                            $status_details = array(
                              "label" => '',
                              "code" => "\$indexed_array_fields_func['status'] !== false",
                              "sub_status" => $indexed_array_fields_func["status_details"]
                            );

                          }


                        }

                      }

                      if ($status !== false) {

                        $element_read_mapped_var[] = $indexed_array_fields;

                      }

                    }
                  }

                } else {
                  $status = false;
                  $status_details = array(
                    "label" => '',
                    "code" => "is_array(\$value['indexed_array_fields'])",
                    "sub_status" => array()
                  );

                }
              }
            } else {

              $element_read_mapped_var = $element_read;

            }
          }



          if ($status !== false) {

            // $translation = $this->element_write($value["out"], $element_read_mapped_var, $translation);
            $translation_func = $this->element_write_and_apply_func($value, $element_read_mapped_var, $translation);
            if ($translation_func['status'] !== false) {

              $translation = $translation_func["data"];
            } else {
              $status = false;
              $status_details = array(
                "label" => '',
                "code" => "\$translation_func['status'] !== false",
                "sub_status" => $translation_func["status_details"]
              );

            }
          }

  			}
  		}
  	}

    return array(
      "status"=>$status,
      "status_details"=>$status_details,
      "data"=>$translation
    );
	}

  public function fresh_access_token($connection)
  {

    $status = true;

    $table = "IMS Connections";


    if ($status !== false) {

      $SQL="SELECT *
      from `$table`
      WHERE connection = '".$connection."'
      order by ID desc
      limit 1;";

      $ims_connections_query = $this->db->query($SQL);

      if (!empty($ims_connections_query->result_array())) {
        $ims_connections_result_array = $ims_connections_query->result_array();
      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "!empty(\$ims_connections_query->result_array())",
          "sub_status" => array()
        );

      }

    }
    if ($status !== false) {
      if (isset($ims_connections_result_array[0])) {

        // $this->extension_es_inbox_l->pre_json($ims_connections_result_array[0]);
        $ims_connection = $ims_connections_result_array[0];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$ims_connections_result_array[0])",
          "sub_status" => array()
        );

      }
    }
    // if ($status !== false) {
    //   if (isset($ims_connection['refresh_token'])) {
    //
    //
    //   } else {
    //     $status = false;
    //     $status_details = array(
    //       "label" => '',
    //       "code" => "isset(\$ims_connection['refresh_token'])",
    //       "sub_status" => array()
    //     );
    //
    //   }
    // }
    // echo $ims_connection['refresh_token'];
    // exit;
    if ($status !== false) {

      $expired_in = strtotime($ims_connection["expired_in"]);
      $existingAccessToken = new \League\OAuth2\Client\Token\AccessToken([
        'access_token'			=> $ims_connection["access_token"],
        'refresh_token'			=> $ims_connection["refresh_token"],
        'expires'			=> $expired_in
      ]);
      // $existingAccessToken = getAccessTokenFromYourDataStore();

    }

    if ($status !== false) {

      if ($existingAccessToken->hasExpired()) {

        if ($status !== false) {
          $refresh_tokens = $this->refresh_tokens($ims_connection["refresh_token"], $connection);
          if ($refresh_tokens['status'] !== false) {

            $refresh_tokens_data = $refresh_tokens["data"];
          } else {
            $status = false;
            $status_details = array(
              "label" => '',
              "code" => "\$refresh_tokens['status'] !== false",
              "sub_status" => $refresh_tokens["status_details"]
            );

          }
        }

        if ($status !== false) {

          $date_refresh_tokens = date('Y-m-d H:i:s', $refresh_tokens_data["getExpires"]);
          $update_data = array(
            "connection"=> $ims_connection["connection"],
            "access_token"=> $refresh_tokens_data["getToken"],
            "refresh_token"=> $refresh_tokens_data["getRefreshToken"],
            "expired_in"=> $date_refresh_tokens,
            "date"=> $ims_connection["date"],
          );

          $insert_data_escaped = array();
          foreach ($update_data as $key => $value) {
            // code...
            $update_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', str_replace('\\','\\\\', $value)).'"';
          }

          $table = "IMS Connections";


          $this->db->_protect_identifiers=false;
          $query_result = $this->db->update("`$table`", $update_data_escaped, array('id' => $ims_connection["id"]));
          $this->db->_protect_identifiers=true;

          $result = $refresh_tokens_data["getToken"];
        }

        // $this->extension_es_inbox_l->pre_json($refresh_tokens);

      } else {
        $result = $ims_connection["access_token"];
      }
    }

    if ($status !== false) {
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

  public function refresh_tokens($refresh_token, $connection)
  {

    $status = true;
    if ($status !== false) {
      $provider_func = $this->extension_es_inbox_l->provider($connection);
      if ($provider_func['status'] !== false) {

        $provider = $provider_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$provider_func['status'] !== false",
          "sub_status" => $provider_func["status_details"]
        );

      }
    }

    if ($status !== false) {

      // echo $refresh_token;

      try {

        $newAccessToken = $provider->getAccessToken('refresh_token', [
          'refresh_token' => $refresh_token

        ]);

      } catch (Exception $e) {

        // // Failed to get the access token or user details.
        // exit($e->getMessage());
        $caught_error = "";

      }

      if (!isset($caught_error)) {

        $result["getToken"] = $newAccessToken->getToken();
        $result["getRefreshToken"] = $newAccessToken->getRefreshToken();
        $result["getExpires"] = $newAccessToken->getExpires();

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "!isset(\$caught_error)",
          "sub_status" => array()
        );

      }


    }

    if ($status !== false) {
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

  public function source_ret_read()
  {
    $status = true;
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );

    if ($status !== false) {
      if (isset($_GET['retrieval_source']) && isset($_GET['verb'])) {

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_GET['retrieval_source']) && isset(\$_GET['verb'])",
          "sub_status" => array()
        );

      }
    }


    if ($status !== false) {

      $data["data"] = "";
      $this->load->view('Extension_ES_Retrive_Form_v', $data);
    } else {
      $debug_reply = "";
      $debug_reply = $debug_reply."<pre>";
      // $debug_reply = $debug_reply.$interpretation_status_label." - ".str_replace('"','\"', json_encode($status_details, JSON_PRETTY_PRINT));
      $debug_reply = $debug_reply.$status." - ".json_encode($status_details, JSON_PRETTY_PRINT);
      $debug_reply = $debug_reply."</pre>";

      echo $debug_reply;
    }

  }

  public function source_ret()
  {
  	$this->load->database();

  	$status = true;
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );
    $receiver = "";
    $debug_reply = "";
    $debug_message = "";
    $debug_initiator = "SERVER_ADDR: ".$_SERVER['SERVER_ADDR'];
    // $retrieval_source_name = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $connection = "";
    $verb = "";
    $integration_engine = "";

  	if ($status !== false) {
  		if (isset($_GET['integration_engine'])) {

  			$integration_engine = $_GET['integration_engine'];

  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_GET['integration_engine'])",
          "sub_status" => array()
        );

  		}
  	}

  	if ($status !== false) {
  		if ($this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()['status'] !== false) {

  			$valid_Extension_ES_Integration_engines_json = $this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()["data"];

  			$status = true;
  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()['status'] !== false",
          "sub_status" => array()
        );

  		}
  	}


  	if ($status !== false) {
  		if (isset($valid_Extension_ES_Integration_engines_json['retrieval_sources'])) {

  			$retrieval_sources = $valid_Extension_ES_Integration_engines_json['retrieval_sources'];

  			$status = true;
  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$valid_Extension_ES_Integration_engines_json['retrieval_sources'])",
          "sub_status" => array()
        );
  		}
  	}

  	if ($status !== false) {
  		if (isset($_GET['retrieval_source'])) {

  			$retrieval_source_name = $_GET['retrieval_source'];
        $connection = $retrieval_source_name;
  			// $status = true;
  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_GET['retrieval_source'])",
          "sub_status" => array()
        );
  		}
  	}

  	if ($status !== false) {
  		if (isset($_POST['verb'])) {

  			$verb_name = $_POST['verb'];

        $verb = $verb_name;
  			// $status = true;
  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_POST['verb'])",
          "sub_status" => array()
        );
  		}
  	}

  	if ($status !== false) {
  		if (isset($_POST['ID'])) {

  			$POST_ID = $_POST['ID'];

  			$status = true;
  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_POST['ID'])",
          "sub_status" => array()
        );
  		}
  	}


    if ($status !== false) {

      $source_value_func = $this->extension_es_inbox_l->find_nested_array($retrieval_sources, "id", $retrieval_source_name);

      if ($source_value_func['status'] !== false) {

        $source_value = $source_value_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$source_value_func['status'] !== false",
          "sub_status" => $source_value_func["status_details"]
        );
      }
    }

    if ($status !== false) {

      if (isset($source_value['url'])) {

        $source_url = $source_value['url'];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$source_value['url'])",
          "sub_status" => $source_value_func["status_details"]
        );
      }
    }

    if ($status !== false) {

      if (isset($source_value['headers']) && is_array($source_value['headers'])) {

        $source_value_headers = $source_value["headers"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$source_value['headers']) && is_array(\$source_value['headers'])",
          "sub_status" => array()
        );
      }
    }


  	if ($status !== false) {
  		if (isset($source_value['map']) & is_array($source_value['map'])) {

  			$source_map = $source_value['map'];

  			$status = true;
  		} else {
  			$status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$source_value['map']) & !is_array(\$source_value['map'])",
          "sub_status" => array()
        );
  		}
  	}





  	if ($status !== false) {


      $case_func = $this->extension_es_inbox_l->find_nested_array($source_map["cases"], "id", $verb_name);

      if ($case_func['status'] !== false) {

        $case_value = $case_func["data"];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$case_func['status'] !== false",
          "sub_status" => $case_func["status_details"]
        );
      }
  	}



    if ($status !== false) {
      if (isset($case_value['request_suffix'])) {

        $case_value_request_suffix = $case_value['request_suffix'];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$case_value['request_suffix'])",
          "sub_status" => array()
        );

      }
    }

    // if ($status !== false) {
    //   if (isset($source_map['cases_trigger'])) {
    //
    //     $cases_trigger = $source_map["cases_trigger"];
    //
    //   } else {
    //     $status = false;
    //     $status_details = array(
    //       "label" => '',
    //       "code" => "isset(\$source_map['cases_trigger']",
    //       "sub_status" => array()
    //     );
    //
    //   }
    // }

    if ($status !== false) {
      $endpoint = $source_url."/".$case_value_request_suffix."/".$POST_ID;
      $receiver = $endpoint;
      $post_curl_array = $this->get_curl_array(array(), $endpoint, $source_value_headers);
      $json_decode_post_curl_array = $post_curl_array;
      // $json_decode_post_curl_array[$cases_trigger] = $verb_name;

      if ($json_decode_post_curl_array !== null & is_array($json_decode_post_curl_array)) {

        $debug_reply = json_encode($json_decode_post_curl_array);
      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$json_decode_post_curl_array !== null & is_array(\$json_decode_post_curl_array)",
          "sub_status" => array()
        );
      }
    }

    if ($status !== false) {
      $success_check_func = $this->success_check($case_value, $json_decode_post_curl_array);
      if ($success_check_func['status'] !== false) {

        $success_check = $success_check_func["data"];
      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$success_check_func['status'] !== false",
          "sub_status" => $success_check_func["status_details"]
        );

      }
    }







  	if ($status !== false) {

      if (isset($case_value['fields'])) {

        $field_map = $case_value['fields'];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$case_value['fields'])",
          "sub_status" => array()
        );
      }
  	}

    if ($status !== false) {
      $interpretation_func = $this->translation($field_map, $json_decode_post_curl_array);
      if ($interpretation_func['status'] !== false) {

        $interpretation = $interpretation_func["data"];
        // $interpretation["retrival_source_case"] = $verb_name;
        $interpretation["verb"] = $verb_name;


        $json_encode_interpretation = json_encode($interpretation, JSON_PRETTY_PRINT);
        $debug_message = $json_encode_interpretation;

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$interpretation_func['status'] !== false",
          "sub_status" => $interpretation_func["status_details"]
        );
      }
    }



  	if ($status !== false) {
  		$interpretation_status_label = "success (in)";

  		$status_pretty = $interpretation_status_label;


  	} else {
  		$interpretation_status_label = "error (in)";
  		$status_pretty = "";
  		$status_pretty = $status_pretty."<pre>";
      // $status_pretty = $status_pretty.$interpretation_status_label." - ".str_replace('"','\"', json_encode($status_details, JSON_PRETTY_PRINT));
      $status_pretty = $status_pretty.$interpretation_status_label." - ".json_encode($status_details, JSON_PRETTY_PRINT);
  		$status_pretty = $status_pretty."</pre>";



  	}

    $insert_data_escaped = array();

    // $request["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];



    $insert_data = array(
      "date"=> date('Y-m-d H:i:s'),
      "ES Integration engine"=> $integration_engine,
      // "direction"=> "In",
      "method"=> "Pull",
      "connection"=> $connection,
      "debug_initiator"=> $debug_initiator,
      "verb"=> $verb_name,
      // "receiver"=> $receiver,
      "debug_status"=> $interpretation_status_label,
      "debug_message"=> $debug_message,
      "debug_reply"=> $debug_reply,
      "debug_status_details"=> json_encode($status_details, JSON_PRETTY_PRINT),
      // "ES Message"=> null
    );

    if (isset($interpretation["subject"])) {
      $insert_data["subject"] = $interpretation["subject"];
    }
    if (isset($interpretation["object"])) {
      $insert_data["object"] = $interpretation["object"];
    }

    if ($status) {
      // $insert_data["debug_message"] = "";
      // $insert_data["debug_reply"] = "";
      // $insert_data["debug_status_details"] = "";
    }

    foreach ($insert_data as $key => $value) {
      // code...
      // $insert_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', $value).'"';
      $insert_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', str_replace('\\','\\\\', $value)).'"';
    }



    // $this->extension_es_inbox_l->pre_json($insert_data_escaped);

    $table = "ES Messages";


    $this->db->_protect_identifiers=false;
    $query_result = $this->db->insert("`$table`", $insert_data_escaped);
    $this->db->_protect_identifiers=true;



    echo $status_pretty;


    if ($status !== false) {
      $insert_id = $this->db->insert_id();
      $articulate_to_destination_func = $this->articulate_to_destination($retrieval_source_name, $source_value, $valid_Extension_ES_Integration_engines_json, $interpretation, $insert_id, $integration_engine);

      // echo "<pre>";
      // var_dump($interpretation);
      // echo "</pre>";

      if ($articulate_to_destination_func['status'] !== false) {

        $articulate_to_destination = $articulate_to_destination_func["data"];

        $data["data"] = array(
          "integration_engine"=>$integration_engine,
          "source_id"=>$retrieval_source_name,
          "verb"=>$verb_name,
          "case_suffix"=>$case_value_request_suffix
        );


        $this->load->view('Extension_ES_Retrive_Submit_v', $data);
      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$articulate_to_destination_func['status'] !== false",
          "sub_status" => $articulate_to_destination_func["status_details"]
        );


      }
    }


    if ($status !== false) {
      $articulate_status_label = "success (out)";

      $status_pretty = $articulate_status_label;


    } else {
      $articulate_status_label = "error (out)";
      $status_pretty = "";
      $status_pretty = $status_pretty."<pre>";
      $status_pretty = $status_pretty.$articulate_status_label." - ".json_encode($status_details, JSON_PRETTY_PRINT);
      $status_pretty = $status_pretty."</pre>";



    }


    echo $status_pretty;


  }



  public function post_curl_array($fields, $url, $headers) {

    $curl = curl_init();

    $CURLOPT_POSTFIELDS = array();
    foreach ($fields as $key => $value) {
      $CURLOPT_POSTFIELDS[] = rawurlencode($key)."=".rawurlencode($value);
    }
    $CURLOPT_POSTFIELDS = implode("&", $CURLOPT_POSTFIELDS);

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.$this->api_key,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $CURLOPT_POSTFIELDS,
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $response = json_decode($response, true);
    return $response;
  }

  public function get_curl_array($fields, $url, $headers) {

    $request_headers = array();
    foreach ($headers as $key => $value) {
      $request_headers[] = "$key: $value";
    }
    // $request_headers = array(
    //   'X-Filename: blahblah.zip',
    //   'X-Filesize: 2677',
    //   'X-Filetype: application/zip',
    // );

    $curl = curl_init();

    $get_fields = array();
    foreach ($fields as $key => $value) {
      $get_fields[] = rawurlencode($key)."=".rawurlencode($value);
    }
    $get_fields = implode("&", $get_fields);

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url."?".$get_fields,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $request_headers,
      // CURLOPT_HEADER => 0,
    ));

    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

    $response = curl_exec($curl);

    // $info = curl_getinfo($curl);
    // echo "<pre>";
    // print_r($info);
    // echo "</pre>";

    curl_close($curl);
    // echo $response;


    $response = $this->strip_html($response);
    $response = json_decode($response, true);


    return $response;
  }

  public function strip_html($input) {

    $result = $input;
    // // $result = preg_replace('/<([^>]*)>[^<]*</[^>]*>/', '', $result);

    // $result = preg_replace('/<!DOCTYPE(.*?)>/', '', $result);
    // $result = preg_replace('/<html (.*?)>[^<]*<\\/html>/', '[html_has_been_stripped1]', $result);

    // $result = preg_replace('/<(.*?)(\h*)(.*?)>[^<]*<\\/\1>/s', '[html_has_been_stripped1]', $result);
    $result = strip_tags($result);

    // $doc = new DOMDocument();
    // $doc->loadHTML($result);
    // $xpath = new DOMXPath($doc);
    // foreach ($xpath->query('/html/body//div/div/div//div//span/@data-description') as $node) {
    //   $node->parentNode->removeChild($node);
    // }
    // $result = $doc->saveHTML();


    return $result;
  }



  public function element_write_and_apply_func($value, $element_read_value, $translation) {

    $status = true;
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );

    if ($status !== false) {

      if (isset($value["out_func"])) {


        if ($status !== false) {

          if (!is_array($element_read_value)) {


          } else {
            $status = false;
            $status_details = array(
              "label" => '',
              "code" => "!is_array(\$element_read_value)",
              "sub_status" => array()
            );
          }
        }

        if ($status !== false) {
          $func_needle = "/(.*?)\((.*?)\)/s";
          $preg_match_all_func_needle = preg_match_all($func_needle, $value["out_func"], $preg_match_all_func_results);
          if ($preg_match_all_func_needle) {

            $func_name = $preg_match_all_func_results[1][0];
            $func_param = $preg_match_all_func_results[2][0];

            if (in_array($func_name, ['prefix', 'suffix', 'datetime'])) {

              if ($func_name == "prefix") {

                $element_read_value = $func_param+$element_read_value;

              } elseif ($func_name == "suffix") {

                $element_read_value = $element_read_value+$func_param;

              } elseif ($func_name == "datetime") {


                $strtotime_element_read_value = strtotime( $element_read_value );
                $date_element_read_value = date( $func_param, $strtotime_element_read_value );
                $element_read_value = $date_element_read_value;

              }
              $translation = $this->element_write($value["out"], $element_read_value, $translation);

            } else {
              $status = false;
              $status_details = array(
                "label" => '',
                "code" => "\in_array(\$func_name, ['prefix', 'suffix', 'datetime'])",
                "sub_status" => array()
              );
            }

          } else {
            $status = false;
            $status_details = array(
              "label" => '',
              "code" => "\$preg_match_all_func_needle",
              "sub_status" => array()
            );
          }
        }

      } else {

        $translation = $this->element_write($value["out"], $element_read_value, $translation);

      }
    }



    return array(
      "status"=>$status,
      "status_details"=>$status_details,
      "data"=>$translation
    );
  }

  public function success_check($event_type, $debug_reply) {

    $status = true;
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );


    if ($status !== false) {
      if (isset($event_type['success_check_where_field']) & is_string($event_type['success_check_where_field'])) {

        $success_check_where_field = $event_type['success_check_where_field'];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$event_type['success_check_where_field']) & is_string(\$event_type['success_check_where_field'])",
          "sub_status" => array()
        );

      }
    }


    if ($status !== false) {
      if (isset($event_type['success_check_where_operator']) && in_array($event_type['success_check_where_operator'], ['==','!=', '!isset'])) {

        $success_check_where_operator = $event_type['success_check_where_operator'];

      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$event_type['success_check_where_operator']) && in_array(\$event_type['success_check_where_operator'], ['==','!='])",
          "sub_status" => array()
        );

      }
    }

		if ($status !== false) {

      if ($success_check_where_operator == "!isset") {

        $element_read_success_check_where_field_func = $this->element_read($success_check_where_field, $debug_reply);
        if ($element_read_success_check_where_field_func['status'] == false) {

        } else {
          $status = false;
          $status_details = array(
            "label" => '',
            "code" => "\$element_read_success_check_where_field_func['status'] == false",
            "sub_status" => array()
          );

        }

      } else {
        if ($status !== false) {

          $element_read_success_check_where_field_func = $this->element_read($success_check_where_field, $debug_reply);

          if ($element_read_success_check_where_field_func['status'] !== false) {

            $element_read_success_check_where_field = $element_read_success_check_where_field_func["data"];
            $expected_debug_reply = $element_read_success_check_where_field;

          } else {
            $status = false;
            $status_details = array(
              "label" => '',
              "code" => "\$element_read_success_check_where_field_func['status'] !== false",
              "sub_status" => $element_read_success_check_where_field_func["status_details"]
            );

          }
        }
        if ($status !== false) {
          if ($success_check_where_operator == "==") {

            if ($expected_debug_reply == $event_type['success_check_where_value']) {

            } else {
              $status = false;
              $status_details = array(
                "label" => '',
                "code" => "\$expected_debug_reply == \$event_type['success_check_where_value']",
                "sub_status" => array()
              );

            }

          } elseif ($success_check_where_operator == "!=") {


            if ($expected_debug_reply != $event_type['success_check_where_value']) {

            } else {
              $status = false;
              $status_details = array(
                "label" => '',
                "code" => "\$expected_debug_reply != \$event_type['success_check_where_value']",
                "sub_status" => array()
              );

            }
          }
        }


      }
		}



    return array(
      "status"=>$status,
      "status_details"=>$status_details,
      "data"=>""
    );
  }
}
