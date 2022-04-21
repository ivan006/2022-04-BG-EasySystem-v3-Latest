
<select class="form-control" id="active_group" style="display: inline-block;">
  <?php
  foreach ($active_groups_dropdown["options"] as $key => $value) {
    $selected_attr =  '';
    if ($active_groups_dropdown["assumed"] == $value["id"]) {
      $selected_attr =  'selected="selected"';
    }
    ?>
    <option value="<?php echo $value["id"] ?>" <?php echo $selected_attr ?>><?php echo $value["indent"] ?> <?php echo $value["name"] ?></option>
    <?php
  }
  ?>
</select>
<script type="text/javascript">

$( document ).ready(function() {
  $("#active_group").change(function(){
    // identify_rules_cookie_set("active_group", [this.value]);

    var cookie_value = {
      0: this.value
    };

    cookie_value = param_encode(cookie_value);

    setCookie("active_group", cookie_value, 365);

    cookie_value = getCookie("active_group");

    cookie_value = param_decode(cookie_value);

    // console.log(cookie_value[0]);

    location.reload();
  });
});




function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function param_encode(value) {

	var query = value;
	var queryString = [];
	for (var p in query)
	if (query.hasOwnProperty(p)) {
		queryString.push(encodeURIComponent(p) + "=" + encodeURIComponent(query[p]));
	}
	queryString = queryString.join("&");

	return queryString;

}

function param_decode(value) {

  var query = {};
  var pairs = (value[0] === '?' ? value.substr(1) : value).split('&');
  for (var i = 0; i < pairs.length; i++) {
    var pair = pairs[i].split('=');
    query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
  }

  return query

}



</script>
