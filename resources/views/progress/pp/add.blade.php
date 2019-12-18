<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Add New material</h3>
		<div class="block-options">
			<button type="button" class="btn-block-option close" data-dismiss="modal" aria-label="Close">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>
	<div class="block-content">
		<div class="row justify-content-center py-20">
			<div class="col-xl-10">
				<span id="form_result"></span>
				<form method="POST" id="material_form" name="material_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<div class="form-group row mt-lvl-1">
						<label class="col-lg-4 col-form-label" for="mt_parid">Material Type</label>
						<div class="col-lg-8">
							<select class="form-control" id="mt_parid" name="mt_parid">
								<option>Select Item</option>
								@foreach ( $mt_lvl1 as $item)
								<option value="{{ $item->mt_id }}"> {{ $item->mt_name }} </option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="mat_no">Total</label>
						<div class="col-lg-8">
							<input type="number" step="1" class="form-control" id="mat_no" name="mat_no" placeholder="Material Total">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="mat_spec">Specification</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" id="mat_spec" name="mat_spec" placeholder="Material Specification">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="mat_thick">Thickness</label>
						<div class="col-lg-8">
							<input type="number" step="0.0001" class="form-control" id="mat_thick" name="mat_thick" placeholder="Material Thickness">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="purchased_at">Purchased At</label>
						<div class="col-lg-8">
							<input type="date" class="form-control" id="purchased_at" name="purchased_at" placeholder="date">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="remark">Remark</label>
						<div class="col-lg-8">
								<input type="text" class="form-control" id="remark" name="remark" placeholder="remark">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto">
							<input type="hidden" name="action" id="action" />
							<input type="hidden" name="hidden_id" id="hidden_id" />
							<input type="submit" name="action_button" id="action_button" class="btn btn-success" value="Add" />
							<button type="button" id="cancel_button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$('#mt_parid').change(function() {
	var parid = $(this).val();
	$.ajaxSetup({
    headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
	$.ajax({
		url:"{{ route('pp.ajax-get-mt-lvl-2', $proj_id) }}",
		method:"GET",
		data: {parid: parid},
		success:function(html)
		{
			$('.mt-lvl-2').remove();
			$('.mt-lvl-1').after(html.data);
		}
	})
});

$('#material_form').on('submit', function(event){
	event.preventDefault();
	$.ajaxSetup({
    headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
	$.ajax({
		url:"{{ route('pp.store', $proj_id) }}",
		method:"POST",
		data: new FormData(document.getElementById("material_form")),
		contentType: false,
		cache:false,
		processData: false,
		success:function(data)
		{
			var html = '';
			if(data.errors)
			{
				html = '<div class="alert alert-danger">';
				for(var count = 0; count < data.errors.length; count++)
				{
						html += '<p>' + data.errors[count].replace("proj_","") + '</p>';
				}
				html += '</div>';
				$('#form_result').html(html);
			}
			if(data.success)
			{
				html = '<div class="alert alert-success">' + data.success + '</div>';
				$('#material_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});
</script>
