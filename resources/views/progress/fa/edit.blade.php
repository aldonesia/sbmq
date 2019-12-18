<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Update Piecepart Process</h3>
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
				<form method="POST" id="material_form" name="material_form" class="js-validation-bootstrap form-horizontal" enctype="application/x-www-form-urlencoded">
					@csrf
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="proc_id">Process</label>
						<div class="col-lg-8">
							<select class="form-control" id="proc_id" name="proc_id">
								@for ($i=0; $i < count($_FaProcDats['proc_id']); $i++)
									@if ($_FaProcDats['proc_id'][$i] < $fa_dats->proc_id)
										@continue
									@endif
									<option value="{{ $_FaProcDats['proc_id'][$i] }}" {{ $_FaProcDats['proc_id'][$i] == $fa_dats->proc_id ? 'selected' : '' }}> {{ $_FaProcDats['proc_name'][$i] }} </option>
								@endfor
							</select>
						</div>
					</div>
					<div class="form-group row ">
						<label class="col-lg-4 col-form-label" for="mt_id">Material Type</label>
						<div class="col-lg-8">
							<select class="form-control" id="mt_id" name="mt_id" readonly=true>
								<option value="{{ $fa_dats->mt_id }}" selected> {{ $fa_dats->mt_name }} </option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="pp_name">Piecepart Name</label>
						<div class="col-lg-8">
						<input type="text" class="form-control" id="pp_name" name="pp_name" placeholder="Piecepart Name" value="{{$fa_dats->pp_name}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="pp_no">Piecepart No</label>
						<div class="col-lg-8">
						<input readonly type="text" class="form-control" id="pp_no" name="pp_no" placeholder="Piecepart No" value="{{$fa_dats->pp_no}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="remark">Remark</label>
						<div class="col-lg-8">
								<input type="text" class="form-control" id="remark" name="remark" placeholder="remark" value="{{$fa_dats->remark}}">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto">
							<input type="hidden" name="proc_old" id="proc_old" value={{ $fa_dats->proc_id }} />
							<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $fa_dats->pp_id }}" />
							<input type="hidden" name="mat_id" id="mat_id" value="{{ $fa_dats->mat_id }}" />
							<input type="submit" name="action_button" id="action_button" class="btn btn-success" value="Update" />
							<button type="button" id="cancel_button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$('#material_form').on('submit', function(event){
	event.preventDefault();
	$.ajaxSetup({
    headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
	$.ajax({
		url:"{{ route('fa.update', [$proj_id, $fa_id]) }}",
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
