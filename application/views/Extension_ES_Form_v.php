<title>EasySync</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style media="screen">
.G_PreWrap {
  white-space: pre-wrap;       /* Since CSS 2.1 */
  white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
  white-space: -pre-wrap;      /* Opera 4-6 */
  white-space: -o-pre-wrap;    /* Opera 7 */
  word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
</style>
<div class="container p-3">

  <h1>EasySync</h1>

  <h2>Messages</h2>

  <div class="dropdown ">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Date (<?php echo $data["activity_graph"]["overview"]["dropdown_dates"]["current"] ?>)
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <?php
      foreach ($data["activity_graph"]["overview"]["dropdown_dates"]["options"] as $key => $value) {
        ?>
        <a class="dropdown-item" href="?id=<?php echo $_GET["id"] ?>&month=<?php echo $value ?>"><?php echo $value ?></a>
        <?php
      }
      ?>
    </div>
  </div>



  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>


  <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
    <?php
    $graphs_index = 0;
    foreach ($data["activity_graph"]["children"] as $key_1 => $value_1) {
      if ($graphs_index ==0) {
        $extra_css = " active";
        $aria_selected = "true";

      } else {
        $extra_css = "";
        $aria_selected = "false";
      }

      ?>

      <li class="nav-item">
        <a class="nav-link <?php echo $extra_css ?>" id="" data-toggle="tab" href="#tab_<?php echo $key_1 ?>" role="tab" aria-controls="tab_<?php echo $key_1 ?>" aria-selected="<?php echo $aria_selected ?>">
          <?php echo $key_1 ?>
        </a>
      </li>
      <?php
      $graphs_index = $graphs_index+1;
    }
    ?>
  </ul>



  <div class="tab-content" id="myTabContent">
  <?php
  $graphs_index = 0;
  foreach ($data["activity_graph"]["children"] as $key_1 => $value_1) {

    if ($graphs_index ==0) {
      $extra_css = " show active";

    } else {
      $extra_css = "";
    }
    ?>

    <div class="tab-pane fade <?php echo $extra_css ?>" id="tab_<?php echo $key_1 ?><?php echo $extra_css ?>" role="tabpanel" aria-labelledby="home-tab" style="height: 1100px;">
      <div class="card mt-3">
        <div class="card-header">Overview</div>
        <div class="card-body">
          <div class="container">
            <canvas id="chart_<?php echo $key_1 ?>"></canvas>
          </div>
          <script type="text/javascript">
            var ctx = document.getElementById("chart_<?php echo $key_1 ?>").getContext("2d");

            var myChart = new Chart(ctx, {
              type: 'bar',
              options: {
                scales: {
                  xAxes: [{
                    barPercentage: 1,
                    stacked: true,
                    type: 'time',
                    time: {
                      // min: <?php echo strtotime($data["activity_graph"]["overview"]["start"]); ?>,
                      // max: <?php echo strtotime($data["activity_graph"]["overview"]["end"]) ?>

                      min: "<?php echo $data["activity_graph"]["overview"]["start"] ?>",
                      max: "<?php echo $data["activity_graph"]["overview"]["end"] ?>"
                    }
                  }]
                  // ,
                  // yAxes: [{
                  //   stacked: true,
                  // }]
                }
              },
              data: {
                datasets: [
                  <?php
                  $colors = array(
                    "255, 0, 0",
                    "255, 165, 0",
                    "255, 255, 0",
                    "0, 128, 0",
                    "0, 0, 255",
                    "75, 0, 130",
                    "238, 130, 238",

                    "255, 0, 0",
                    "255, 165, 0",
                    "255, 255, 0",
                    "0, 128, 0",
                    "0, 0, 255",
                    "75, 0, 130",
                    "238, 130, 238",
                  );
                  $key = 0;
                  foreach ($value_1["children"] as $key => $value) {
                    ?>
                    {
                      label: "<?php echo $value["label"] ?>",
                      data: <?php echo json_encode($value["data"], JSON_PRETTY_PRINT); ?>,
                      backgroundColor: 'rgba(<?php echo $colors[$key] ?>, 0.5)',
                      // borderColor: [
                      //   'rgba(255, 159, 64, 0.2)'
                      // ],
                      // borderWidth: 1
                    },
                    <?php
                  } ?>
                  ]
                }
              });
          </script>
        </div>
      </div>


      <div class="card mt-3">
        <div class="card-header">Details - find them using this query</div>
        <div class="card-body">
          <div class="card mt-3">
            <div class="card-header">Show them</div>
            <div class="card-body">
              <pre class="G_PreWrap"><?php echo $value_1["query"] ?></pre>
            </div>
          </div>
          <div class="card mt-3">
            <div class="card-header">Delete their bulky debug values</div>
            <div class="card-body">
              <pre class="G_PreWrap"><?php echo $value_1["delete_query"] ?></pre>
            </div>
          </div>
        </div>
      </div>


    </div>
    <?php

    $graphs_index = $graphs_index+1;
  }
  ?>

  </div>
  <h2>Connections</h2>
  <h3>Sources</h3>

  <?php

  if (isset($data["processed__Extension_ES_Integration_engines_json"]["retrieval_sources"])) {
    ?>
    <div class="card mb-3">
      <div class="card-header">Retrieval</div>
      <div class="card-body">
        <?php
        foreach ($data["processed__Extension_ES_Integration_engines_json"]["retrieval_sources"] as $key => $value) {
          ?>
          <div class="card mb-3">
            <div class="card-header"><?php echo $value["id"] ?> (<?php echo $value["url"] ?>)</div>
            <div class="card-body">
              <div class="card mb-3">
                <div class="card-header">Linked delivery destinations</div>
                <div class="card-body">
                  <?php
                  foreach ($value["linked_delivery_destinations"] as $key_2 => $value_2) {
                    ?>
                    <?php echo $value_2 ?><br>
                    <?php
                  }
                  ?>
                </div>
              </div>
              <div class="card">
                <div class="card-header">Cases</div>
                <div class="card-body">
                  <?php
                  foreach ($value["map"]["cases"] as $key_2 => $value_2) {
                    ?>
                    <iframe src="/Extension_ES_Inbox_c/source_ret_read?integration_engine=<?php echo $data["integration_engine"] ?>&retrieval_source=<?php echo $value["id"] ?>&id=<?php echo $value_2["id"] ?>&request_suffix=<?php echo $value_2["request_suffix"] ?>&verb=<?php echo $value_2["id"] ?>" width="100%" height="63px" style="border: none;">
                    </iframe>

                    <?php
                  }
                  ?>

                </div>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php

  }

  if (isset($data["processed__Extension_ES_Integration_engines_json"]["delivery_sources"])) {
    ?>

    <div class="card mb-3">
      <div class="card-header">Delivery</div>
      <div class="card-body">
        <?php
        foreach ($data["processed__Extension_ES_Integration_engines_json"]["delivery_sources"] as $key => $value) {
          ?>
          <div class="card">
            <div class="card-header"><?php echo $value["id"] ?> (<?php echo $data["self_url"]; ?>/Extension_ES_Inbox_c/inbox?integration_engine=<?php echo $data["integration_engine"] ?>&source=<?php echo $value["id"] ?>)</div>
            <div class="card-body">
              <div class="card">
                <div class="card-header">Linked delivery destinations</div>
                <div class="card-body">
                  <?php
                  foreach ($value["linked_delivery_destinations"] as $key_2 => $value_2) {
                    ?>
                    <?php echo $value_2 ?><br>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php

  }
  ?>

  <h3>Destinations</h3>

  <?php
  if (isset($data["processed__Extension_ES_Integration_engines_json"]["delivery_destinations"])) {
    ?>
    <div class="card mb-3">
      <div class="card-header">Delivery</div>
      <div class="card-body">
        <?php
        foreach ($data["processed__Extension_ES_Integration_engines_json"]["delivery_destinations"] as $key => $value) {
          ?>
          <div class="card">
            <div class="card-header"><?php echo $value["id"] ?> (<?php echo $value["url"] ?>)</div>
            <div class="card-body">
              <div class="card mb-3">


                <div class="card-header">Auth</div>
                <div class="card-body">
                  Return address: <?php echo $data["self_url"]."/Extension_ES_Form_c/tokens?integration_engine=".$data["integration_engine"]."&delivery_destination=".urlencode($value["id"]); ?><br>
                  <?php
                  if (isset($value["authorization_link"])) {
                    ?>
                    <a href="<?php echo $value["authorization_link"] ?>" class="btn btn-primary mt-3">Grant connection</a>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php
  }

  ?>

  <!-- <a href="http://indigo.bluegemify.co.za/table/d/Integration%20Management%20System/t/IMS%20Messages" class="btn btn-primary mb-3">All messages</a>
  <a href="http://indigo.bluegemify.co.za/table/d/Integration%20Management%20System/t/IMS%20Connections" class="btn btn-primary mb-3">All connection sessions</a> -->




</div>
