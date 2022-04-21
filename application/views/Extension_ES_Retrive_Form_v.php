<title>EasySync</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <?php
  // echo $request["URL"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   ?>
  <!-- <form class="pt-3" action="/sync/retrieval_sources_add_transaction" method="post"> -->
  <form class="mb-0" action="/Extension_ES_Inbox_c/source_ret?integration_engine=<?php echo $_GET["integration_engine"] ?>&retrieval_source=<?php echo $_GET["retrieval_source"] ?>" method="post">
    <table class="table mb-0">
      <tbody>
        <tr>
          <td><?php echo $_GET["verb"] ?></td>
          <td><?php echo $_GET["request_suffix"] ?>/</td>
          <td><input type="text" name="ID" value="" class="ml-3"></td>
          <td><input type="hidden" name="verb" value="<?php echo $_GET["verb"] ?>" class="form-control"></td>
          <td><input type="submit" name="" value="Submit" class="btn btn-primary "></td>
        </tr>
      </tbody>
    </table>


    <!-- <br> -->

  </form>
