<?php
$editable_rows = $data["g_select"]["editable"];
$readable_rows = $data["g_select"]["visible"];
?>
<script type="text/javascript">

$(document).on("click", "#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"]); ?>_edit", function(e){
  e.preventDefault();

  var edit_record_id = $("#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"])."_".$action_type."_" ?>record_id").val();
  <?php
  foreach ($editable_rows as $key => $value) {
    if ($key !== "id") {
      ?>
      var edit_<?php echo makeSafeForCSS($key); ?> = $("#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"])."_".$action_type."_".makeSafeForCSS($key); ?>").val();
      <?php
    }
  }
  ?>
  var edit_permissions_owner = $("#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"])."_".$action_type."_"."permissions_owner"; ?>").val();
  var edit_permissions_editability = $("#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"])."_".$action_type."_"."permissions_editability"; ?>").val();
  var edit_permissions_visibility = $("#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"])."_".$action_type."_"."permissions_visibility"; ?>").val();
  <?php
  ?>



  // if (edit_record_id == "" || edit_name == "")
  if (1 !== 1) {
    alert("Both field is required");
  }else{
    $.ajax({
      url: "<?php echo base_url(); ?>api/table/d/<?php echo $database ?>/t/<?php echo $data["g_identity"]["g_from"]; ?>/update",
      type: "post",
      dataType: "json",
      data: {
        variables: {
          edit_record_id: edit_record_id,
          <?php
          foreach ($editable_rows as $key => $value) {
            if ($key !== "id") {
              ?>
              edit_<?php echo makeSafeForCSS($key); ?>: edit_<?php echo makeSafeForCSS($key); ?>,
              <?php
            }
          }
          ?>
          // edit_name: edit_name,
          // edit_event_children: edit_event_children
        },
        permissions: {
          edit_permissions_owner: edit_permissions_owner,
          edit_permissions_editability: edit_permissions_editability,
          edit_permissions_visibility: edit_permissions_visibility
        }
      },
      success: function(data){
        if (data.responce == "success") {
          $('#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"]); ?>_records').DataTable().destroy();
          <?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"]); ?>_fetch();
          $('#<?php echo makeSafeForCSS($data["g_identity"]["g_ability_html_id"])."_".$action_type."_" ?>modal').modal('hide');
          toastr["success"](data.message);
        }else{
          toastr["error"](data.message);
        }
      }
    });

  }

})

</script>
