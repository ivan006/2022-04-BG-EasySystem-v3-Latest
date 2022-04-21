<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extension_ES_Form_c extends CI_Controller {

  // docs
  // https://stackoverflow.com/questions/47479243/use-oauth2-client-with-codeigniter-class-not-found
  function __construct()
  {
    parent::__construct();

    // $this->load->library('erd_lib');



    $this->load->library([
      'Extension_ES_Inbox_l'
    ]);
    $this->load->database();
  }

  public function index()
  {




    // $delivery_destination = "https://zohoapis.com/crm/v2";
    // $toggle_articulation_has_errors = false;
    // if ($toggle_articulation_has_errors !== true) {
    //   if ($this->extension_es_inbox_l->provider($delivery_destination)["status"] !== false) {
    //
    //     $provider = $this->extension_es_inbox_l->provider($delivery_destination)["data"];
    //
    //   } else {
    //     $toggle_articulation_has_errors = true;
    //     $articulation_status_details = $this->extension_es_inbox_l->provider($delivery_destination)["status_details"];
    //   }
    // }
    //
    // try {
    //   $request = $provider->getAuthenticatedRequest(
    //     'GET',
    //     "https://zohoapis.com/crm/v2/Contacts",
    //     "1000.05758670e667ac521df928a59ceab4b1.a5296ae8992f314f82e6147a1ef376ff"
    //   );
    //   $reply = $provider->getParsedResponse( $request );
    //
    // } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    //
    //   // Failed to get the access token or user details.
    //   exit($e->getMessage());
    //
    // }
    // // echo $reply;
    // $this->extension_es_inbox_l->pre_json($reply);

    $status = true;

    if ($status !== false) {
      if ($this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()["status"] !== false) {

        $valid_Extension_ES_Integration_engines_json = $this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()["data"];

      } else {
        $status = false;
        $error = '$this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()["status"] !== false';
      }
    }

    if ($status !== false) {
      if (isset($valid_Extension_ES_Integration_engines_json["delivery_destinations"]) & is_array($valid_Extension_ES_Integration_engines_json["delivery_destinations"])) {
        $processed__Extension_ES_Integration_engines_json = $valid_Extension_ES_Integration_engines_json;

        foreach ($processed__Extension_ES_Integration_engines_json["delivery_destinations"] as $key => $value) {
          if (isset($value["authorization"])) {

            if ($status !== false) {
              if (isset($value["authorization"]["type"])) {
              } else {
                $status = false;
              }
            }
            if ($status !== false) {
              if ($value["authorization"]["type"] == "oauth2") {


              } else {
                $status = false;
                $error = '$value["authorization"]["type"] == "oauth2"';
              }
            }

            if ($status !== false) {
              if ($this->extension_es_inbox_l->auth_creds($value)["status"] !== false) {

                $auth_creds = $this->extension_es_inbox_l->auth_creds($value)["data"];

              } else {
                $status = false;
                $error = '$this->extension_es_inbox_l->auth_creds($value)["status"] !== false';
              }
            }

            if ($status !== false) {
              $urlencode_value_id = urlencode($value["id"]);
              $processed__Extension_ES_Integration_engines_json["delivery_destinations"][$key]["authorization_link"] = "/Extension_ES_Form_c/form"."?delivery_destination=$urlencode_value_id";

            }

          }
        }
      } else {
        $status = false;
        $error = '$this->extension_es_inbox_l->valid_Extension_ES_Integration_engines_json()["status"] !== false';
      }
    }

    if ($status !== false) {
      if (isset($_GET["id"])) {

        $integration_engine = $_GET["id"];

      } else {
        $status = false;
        $error = 'isset($_GET["id"])';
      }
    }

    if ($status !== false) {

      $activity_graph_func = $this->activity_graph();
      if ($activity_graph_func['status'] !== false) {

        $activity_graph = $activity_graph_func["data"];
        $data["data"]["activity_graph"] = $activity_graph;
      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "\$element_read_func['status'] !== false OR !isset(\$value['in_required'])",
          "sub_status" => $activity_graph_func["status_details"]
        );
      }

    }


    if ($status !== false) {
      $data["data"]["processed__Extension_ES_Integration_engines_json"] = $processed__Extension_ES_Integration_engines_json;
      $data["data"]["integration_engine"] = $integration_engine;
      $data["data"]["self_url"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
      $this->load->view('Extension_ES_Form_v', $data);
      // $this->extension_es_inbox_l->pre_json($processed__Extension_ES_Integration_engines_json);
    } else {
      echo $error;
    }




  }

  public function form()
  {
    $status = true;
    if ($status !== false) {
      $delivery_destination = $_GET["delivery_destination"];
      $provider_func = $this->extension_es_inbox_l->provider($delivery_destination);
      if ($provider_func["status"] !== false) {

        $provider = $provider_func["data"];

      } else {
        $status = false;
        $error = $provider_func["status_details"];
      }
    }

    if ($status !== false) {


      // Fetch the authorization URL from the provider; this returns the
      // urlAuthorize option and generates and applies any necessary parameters
      // (e.g. state).
      $authorizationUrl = $provider->getAuthorizationUrl();

      // Get the state generated for you and store it to the session.
      $_SESSION['oauth2state'] = $provider->getState();

      // Redirect the user to the authorization URL.
      header('Location: ' . $authorizationUrl);
      exit;

    }

    // echo '<pre>';
    // var_dump( $provider );
    // echo '</pre>';


  }

  public function tokens()
  {

    $status = true;
    if ($status !== false) {
      $delivery_destination = $_GET["delivery_destination"];
      $integration_engine = $_GET["integration_engine"];
      $provider_func = $this->extension_es_inbox_l->provider($delivery_destination);
      if ($provider_func["status"] !== false) {

        $provider = $provider_func["data"];

      } else {
        $status = false;
        $error = $provider_func["status_details"];
      }
    }

    // If we don't have an authorization code then get one
    if (!isset($_GET['code'])) {

      // Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

      if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
      }

      exit('Invalid state');

    } else {

      try {

        if (1==1) {
          // Try to get an access token using the authorization code grant.
          $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
          ]);

          // // We have an access token, which we may use in authenticated
          // // requests against the service provider's API.
          // echo 'Access Token: ' . $accessToken->getToken() . "<br>";
          // echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
          // echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
          // echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";


        }


        if (1==1) {
          // $expired_in = strtotime("2021-12-08 09:20:57");
          $expired_in = date('Y-m-d H:i:s', $accessToken->getExpires());
          $insert_data = array(
            "date"=> date('Y-m-d H:i:s'),
            "connection"=> $_GET["delivery_destination"],
            "auth_token"=> $_GET['code'],
            "access_token"=> $accessToken->getToken(),
            "refresh_token"=> $accessToken->getRefreshToken(),
            "expired_in"=> $expired_in,
          );

          $insert_data_escaped = array();
          foreach ($insert_data as $key => $value) {
            // code...
            $insert_data_escaped["`".$key."`"] = '"'.str_replace('"','\"', $value).'"';
          }

          $table = "IMS Connections";


          $this->db->_protect_identifiers=false;
          $query_result = $this->db->insert("`$table`", $insert_data_escaped);
          $this->db->_protect_identifiers=true;

          // Redirect the user to the authorization URL.
          header('Location: ' . "/Extension_ES_Form_c/?id=".$integration_engine);
          exit;
        }



      } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

      }

    }

  }

  public function refresh_token_TEMPLATE()
  {

    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => '1000.NZFV9JV7B7LP5NOTNNQNJ7T1E5K2LW',    // The client ID assigned to you by the provider
      'clientSecret'            => 'c2fd62cf3f74d8911a9b279cbd4922c8a1313ec53c',   // The client password assigned to you by the provider
      'redirectUri'             => 'http://red.bluegemify.co.za/oauth2_client/showform_and_get_token',
      'urlAuthorize'            => 'https://accounts.zoho.com/oauth/v2/auth?scope=aaaserver.profile.READ&access_type=offline',
      'urlAccessToken'          => 'https://accounts.zoho.com/oauth/v2/token',
      'urlResourceOwnerDetails' => 'https://accounts.zoho.com/oauth/user/info'
    ]);
    if (1==1) {
      // object(League\OAuth2\Client\Token\AccessToken)#46 (5) {
      //   ["accessToken":protected]=>
      //   string(70) "1000.b73e8ebace8a6b874939c870db8e134d.05c0a98775088c9a5b2573640bd73de1"
      //   ["expires":protected]=>
      //   int(1638795188)
      //   ["refreshToken":protected]=>
      //   NULL
      //   ["resourceOwnerId":protected]=>
      //   NULL
      //   ["values":protected]=>
      //   array(2) {
      //     ["api_domain"]=>
      //     string(24) "https://www.zohoapis.com"
      //     ["token_type"]=>
      //     string(6) "Bearer"
      //   }
      // }
      $existingAccessToken = new \League\OAuth2\Client\Token\AccessToken([

        // 'access_token'			=> $accessToken->getToken(), // has to be defined by you
        'access_token'			=> "1000.7196597be005d8e670a9c179c1f8137d.462ea75dca6849a70a8310b9a8b76549", // has to be defined by you
        // 'access_token'			=> "1000.7f5c1f6901b0870b49a1b1c739ff75e8.4e768812510327b17d478ab71ea4765b", // has to be defined by you
        'refresh_token'			=> "1000.93812805ebe7779cc9565dcc657fbdaf.60dafd4663fc554a4e0942d1a5ef5650", // has to be defined by you
        'expires'			=> "1638796978" // has to be defined by data from you

      ]);
      // $existingAccessToken = getAccessTokenFromYourDataStore();


      if ($existingAccessToken->hasExpired()) {
        echo 'Existing Access Token: ' . $existingAccessToken->getToken() . "<br>";
        echo 'Existing Refresh Token: ' . $existingAccessToken->getRefreshToken() . "<br>";
        echo 'Existing Expired in: ' . $existingAccessToken->getExpires() . "<br>";
        echo 'Already expired? ' . ($existingAccessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

        $newAccessToken = $provider->getAccessToken('refresh_token', [
          'refresh_token' => $existingAccessToken->getRefreshToken()
        ]);


        echo 'New Access Token: ' . $newAccessToken->getToken() . "<br>";
        echo 'New Refresh Token: ' . $newAccessToken->getRefreshToken() . "<br>";
        echo 'New Expired in: ' . $newAccessToken->getExpires() . "<br>";
        echo 'New Already expired? ' . ($newAccessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

        // Purge old access token and store new access token to your data store.
      }
    }

  }

  public function showform_and_get_token_and_userInfo__NOT_USED()
  {






    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => '1000.NZFV9JV7B7LP5NOTNNQNJ7T1E5K2LW',    // The client ID assigned to you by the provider
      'clientSecret'            => 'c2fd62cf3f74d8911a9b279cbd4922c8a1313ec53c',   // The client password assigned to you by the provider
      'redirectUri'             => 'http://red.bluegemify.co.za/Extension_ES_Form_c/form',
      'urlAuthorize'            => 'https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL&access_type=offline',
      // 'urlAuthorize'            => 'https://accounts.zoho.com/oauth/v2/auth?scope=aaaserver.profile.READ&access_type=offline',
      'urlAccessToken'          => 'https://accounts.zoho.com/oauth/v2/token',
      'urlResourceOwnerDetails' => 'https://accounts.zoho.com/oauth/user/info'
    ]);

    // echo '<pre>';
    // var_dump( $provider );
    // echo '</pre>';

    if (1==1) {
      // If we don't have an authorization code then get one
      if (!isset($_GET['code'])) {

        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $provider->getAuthorizationUrl();

        // Get the state generated for you and store it to the session.
        $_SESSION['oauth2state'] = $provider->getState();

        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
        exit;

        // Check given state against previously stored one to mitigate CSRF attack
      } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

        if (isset($_SESSION['oauth2state'])) {
          unset($_SESSION['oauth2state']);
        }

        exit('Invalid state');

      } else {

        try {

          if (1==1) {
            // Try to get an access token using the authorization code grant.
            $accessToken = $provider->getAccessToken('authorization_code', [
              'code' => $_GET['code']
            ]);

            // We have an access token, which we may use in authenticated
            // requests against the service provider's API.
            echo 'Access Token: ' . $accessToken->getToken() . "<br>";
            echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
            echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
            echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

            // Using the access token, we may look up details about the
            // resource owner.
            $resourceOwner = $provider->getResourceOwner($accessToken);

            var_export($resourceOwner->toArray());

            // The provider provides a way to get an authenticated API request for
            // the service, using the access token; it returns an object conforming
            // to Psr\Http\Message\RequestInterface.
            $request = $provider->getAuthenticatedRequest(
            'GET',
            'https://service.example.com/resource',
            $accessToken
            );
          }



          if (1==1) {
            // object(League\OAuth2\Client\Token\AccessToken)#46 (5) {
            //   ["accessToken":protected]=>
            //   string(70) "1000.b73e8ebace8a6b874939c870db8e134d.05c0a98775088c9a5b2573640bd73de1"
            //   ["expires":protected]=>
            //   int(1638795188)
            //   ["refreshToken":protected]=>
            //   NULL
            //   ["resourceOwnerId":protected]=>
            //   NULL
            //   ["values":protected]=>
            //   array(2) {
            //     ["api_domain"]=>
            //     string(24) "https://www.zohoapis.com"
            //     ["token_type"]=>
            //     string(6) "Bearer"
            //   }
            // }
            $existingAccessToken = new \League\OAuth2\Client\Token\AccessToken([

              'access_token'			=> $accessToken->getToken(), // has to be defined by you
              'refresh_token'			=> $accessToken->getRefreshToken(), // has to be defined by you
              'expires'			=> $accessToken->getExpires() // has to be defined by data from you

            ]);
            // $existingAccessToken = getAccessTokenFromYourDataStore();


            if ($existingAccessToken->hasExpired()) {
              echo 'Existing Access Token: ' . $existingAccessToken->getToken() . "<br>";
              echo 'Existing Refresh Token: ' . $existingAccessToken->getRefreshToken() . "<br>";
              echo 'Existing Expired in: ' . $existingAccessToken->getExpires() . "<br>";
              echo 'Already expired? ' . ($existingAccessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

              $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken()
              ]);


              echo 'New Access Token: ' . $newAccessToken->getToken() . "<br>";
              echo 'New Refresh Token: ' . $newAccessToken->getRefreshToken() . "<br>";
              echo 'New Expired in: ' . $newAccessToken->getExpires() . "<br>";
              echo 'New Already expired? ' . ($newAccessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

              // Purge old access token and store new access token to your data store.
            }
          }


        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

          // Failed to get the access token or user details.
          exit($e->getMessage());

        }

      }
    }


  }

  public function getAccessTokenFromYourDataStore($access_token, $refresh_token, $expires)
  {
    $existingAccessToken = new \League\OAuth2\Client\Token\AccessToken([

      'access_token'			=> $access_token, // has to be defined by you
      'refresh_token'			=> $refresh_token, // has to be defined by you
      'expires'			=> $expires // has to be defined by data from you

    ]);

    return $existingAccessToken;

  }

  public function activity_graph()
  {
    $status = true;
    $status_details = array(
      "label" => '',
      "code" => "",
      "sub_status" => array()
    );

    $data = array();

    if ($status !== false) {
      if (isset($_GET['id'])) {
        $integration_engine = $_GET['id'];
      } else {
        $status = false;
        $status_details = array(
          "label" => '',
          "code" => "isset(\$_GET['id'])",
          "sub_status" => array()
        );

      }
    }

    if ($status !== false) {

      // $sql="SELECT `direction`, count(*) as count
      // from `ES Messages`
      // WHERE `ES Integration engine` = $integration_engine group by `direction`;";
      // $query = $this->db->query($sql);
      // $directions_result = $query->result_array();
      //
      // if (1==1) {
      //   // unset($directions[1]);
      //   // unset($directions[2]);
      //   // unset($directions[3]);
      //
      //   // $directions = array(
      //   //   $directions_result[0],
      //   //   $directions_result[4]
      //   // );
      //   $directions = array();
      //   foreach ($directions_result as $key => $value) {
      //     // if ($value["direction"] == "Destination" OR $value["direction"] == "Source") {
      //     //   $directions[] = $value;
      //     // }
      //     $directions[] = $value;
      //   }
      //   // echo "<pre>";
      //   // echo json_encode($directions, JSON_PRETTY_PRINT);
      //   // echo "</pre>";
      //
      // }


      $sql="SELECT `debug_status`, count(*) as count
      from `ES Messages`
      WHERE `ES Integration engine` = $integration_engine group by `debug_status`;";
      $query = $this->db->query($sql);
      $debug_statuses = $query->result_array();

      $sql="SELECT `verb`, count(*) as count
      from `ES Messages`
      WHERE `ES Integration engine` = $integration_engine group by `verb`;";
      $query = $this->db->query($sql);
      $verbs = $query->result_array();



      // $sql="SELECT date_format(`date`, '%Y-%m-%d %H:00:00') as t, 0 as y
      // from `ES Messages`
      // WHERE `ES Integration engine` = $integration_engine LIMIT 1;";
      // $query = $this->db->query($sql);
      // $first_timestamp = $query->result_array()[0];
      //
      // $sql="SELECT date_format(`date`, '%Y-%m-%d %H:00:00') as t, 0 as y
      // from `ES Messages`
      // WHERE `ES Integration engine` = $integration_engine ORDER BY id DESC LIMIT 1;";
      // $query = $this->db->query($sql);
      // $last_timestamp = $query->result_array()[0];

      // $start = date('Y-m-d H:i:s', strtotime('-3 week', time()));
      // $end = date('Y-m-d H:i:s', time());

      $current_month = date('Y-m', time());
      if (isset($_GET["month"])) {

        $query_date = $_GET["month"];

      } else {

        $query_date = $current_month;

      }



      $start = date('Y-m-01 00:00:00', strtotime($query_date));
      $end  = date('Y-m-t 23:59:59', strtotime($query_date));

      $graphs = array();

      // $graphs = array();
      foreach ($verbs as $key => $value) {
        $verb = $value["verb"];



        $overlaying_graphs = array();
        $label_index = 0;

        foreach ($debug_statuses as $key_2 => $value_2) {
          $debug_status = $value_2["debug_status"];

          if (!empty($verb)) {
            $valid_html_id = $this->valid_html_id($verb);
          } else {
            $valid_html_id = $this->valid_html_id("none");
          }

          // $label = $direction." - ".$status;
          $label = $debug_status;

          if (1==1) {
            // $sql="SELECT date_format(`date`, '%Y-%m-%d %H:00:00') as t, count(*) as y
            // from `ES Messages`
            // WHERE `ES Integration engine` = ".$integration_engine." AND `verb` = '$verb' AND `debug_status` = '$status' AND `direction` = '$direction' AND `date` between '$start' AND '$end' group by date_format(`date`, '%Y-%m-%d %H:00:00');";
            // $query = $this->db->query($sql);
            $sql="SELECT date_format(`date`, '%Y-%m-%d %H:00:00') as t, count(*) as y
            from `ES Messages`
            WHERE `ES Integration engine` = ".$integration_engine." AND `verb` = '$verb' AND `debug_status` = '$debug_status' AND `date` between '$start' AND '$end' group by date_format(`date`, '%Y-%m-%d %H:00:00');";
            $query = $this->db->query($sql);
            $result_array = $query->result_array();

            $overlaying_graphs[$label_index]["label"] = $label;



            // array_unshift($result_array , $first_timestamp);
            // array_push($result_array,$last_timestamp);

            $overlaying_graphs[$label_index]["data"] = $result_array;


            // $result_array = json_encode($result_array);
            // $result_array = json_decode($result_array, true);
          }


          $label_index = $label_index + 1;
        }
        // foreach ($directions as $key_1 => $value_1) {
        //   $direction = $value_1["direction"];
        //
        //
        // }
        $graphs[$valid_html_id]["children"] = $overlaying_graphs;
        $graphs[$valid_html_id]["query"] = "SELECT * FROM `ES Messages` WHERE `ES Integration engine` = ".$integration_engine." AND `verb` = '$verb' AND `date` between '$start' AND '$end' ORDER BY `id` DESC;";

        $graphs[$valid_html_id]["delete_query"] = "UPDATE `ES Messages` SET `debug_initiator` = '', `debug_message` = '', `debug_reply` = '', `debug_status_details` = '' WHERE `ES Integration engine` = ".$integration_engine." AND `verb` = '$verb';";

        // echo "<details>";
        // echo "<summary>x</summary>";
        // echo "<pre>";
        // echo json_encode($verbs, JSON_PRETTY_PRINT);
        // echo "</pre>";
        // echo "</details> ";



        // echo "<details>";
        // echo "<summary>".$valid_html_id."</summary>";
        // echo "<table>";
        // foreach ($result_array as $key => $value) {
        //   echo "<tr>";
        //   echo "<td>";
        //   echo $value["y"];
        //   echo "</td>";
        //   echo "<td>";
        //   echo $value["t"];
        //   echo "</td>";
        //   echo "</tr>";
        // }
        // echo "</table>";
        // echo "</details> ";

        // $for_graph = array();
        // $i = 0;
        // foreach ($result_array as $key => $value) {
        //   // $for_graph[$i]["t"] = date("Y-m-d\TH:i:s\Z", strtotime($value["date"]));
        //   $for_graph[$i]["t"] = $value["t"];
        //   $for_graph[$i]["y"] = $value["y"];
        //
        //   $i = $i+1;
        // }

      }







      // ob_start();
      // // echo "Hello ";
      //
      //
      //
      // $out2 = ob_get_contents();
      // ob_end_clean();
      $data["children"] = $graphs;
      // $data["overview"]["start"] = $first_timestamp["t"];
      // $data["overview"]["start"] = date('Y-m-d H:i:s', (strtotime('-3 week', strtotime($last_timestamp["t"]))));
      $data["overview"]["start"] = $start;
      // $data["overview"]["end"] = $last_timestamp["t"];
      $data["overview"]["end"] = $end;


      $dropdown_date = strtotime($current_month);
      $dropdown_date_string = date('Y-m', $dropdown_date);
      $dropdown_dates = array($dropdown_date_string);

      while ($dropdown_date_string !== "2021-01") {
        $dropdown_date = strtotime('-1 months', $dropdown_date);
        $dropdown_date_string = date('Y-m', $dropdown_date);
        $dropdown_dates[] = $dropdown_date_string;
      }


      $data["overview"]["dropdown_dates"]["current"] = $query_date;
      $data["overview"]["dropdown_dates"]["options"] = $dropdown_dates;


      // echo $out2;
    }

    return array(
      "status"=>$status,
      "status_details"=>$status_details,
      "data"=>$data
    );




  }

  function valid_html_id($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "_", $string);
    return $string;
  }


}
?>
