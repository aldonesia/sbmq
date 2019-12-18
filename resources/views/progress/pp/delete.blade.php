<div class="modal-header">
  <h3 class="modal-title">Confirmation</h3>
  <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
  <span id="form_result"></span>
  <h4 align="center" style="margin:0;">Are you sure you want to remove data with id {{ $pp_id }}?</h4>
</div>
<div class="modal-footer">
  <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
  <button type="button" id="cancel_button" class="btn btn-default" data-dismiss="modal">Cancel</button>
</div>

<script>
$('#ok_button').click(function(){
  $.ajaxSetup({
    headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
    url:"{{ route('pp.destroy', [$proj_id, $pp_id]) }}",
    type: 'delete', // replaced from put
    dataType: "JSON",
    data: {
        "id": {{ $pp_id }} // method and token not needed in data
    },
    beforeSend:function(){
        $('#ok_button').text('Deleting...');
    },
    success:function(data)
    {
      var html='';
      if(data.success)
      {
        html = '<div class="alert alert-success">' + data.success + '</div>';
				$('#form_result').html(html);
				$('#ok_button').text('Ok');
				setTimeout(function(){ $("#cancel_button").click();}, 500);
      }
      else if(data.errors)
      {
        html = '<div class="alert alert-danger">' + data.errors + '</div>';
				$('#form_result').html(html);
	      $('#ok_button').text('Ok');
      }
    }
  })
});
</script>
