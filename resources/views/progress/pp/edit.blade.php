<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Update Material Process</h3>
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
								@for ($i=0; $i < count($_PpProcDats['proc_id']); $i++)
									@if ($_PpProcDats['proc_id'][$i] < $mat_dats->pp_proc_id) @continue @endif
									<option value="{{ $_PpProcDats['proc_id'][$i] }}" {{ $_PpProcDats['proc_id'][$i] == $mat_dats->pp_proc_id ? 'selected' : '' }}> {{ $_PpProcDats['proc_name'][$i] }} </option>
								@endfor
							</select>
						</div>
					</div>
					<div class="form-group row ">
						<label class="col-lg-4 col-form-label" for="mt_parid">Material Type</label>
						<div class="col-lg-8">
							<select class="form-control" id="mt_parid" name="mt_parid" readonly=true>
								<option value="{{ $mt_dats->mt_id }}" selected> {{ $mt_dats->mt_name }} </option>
							</select>
						</div>
					</div>
					<div class="form-group row ">
						<label class="col-lg-4 col-form-label" for="mt_id">Material Sub Type</label>
						<div class="col-lg-8">
							<select class="form-control" id="mt_id" name="mt_id" readonly=true>
								<option value="{{ $mat_dats->mt_id }}" selected> {{ $mat_dats->mt_name }} </option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="mat_no">Material No</label>
						<div class="col-lg-8">
							<input type="number" step="1" class="form-control" id="mat_no" name="mat_no" placeholder="Material No" value="{{$mat_dats->mat_no}}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="mat_spec">Specification</label>
						<div class="col-lg-8">
						<input type="text" class="form-control" id="mat_spec" name="mat_spec" placeholder="Material Specification" value="{{$mat_dats->mat_spec}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="mat_thick">Thickness</label>
						<div class="col-lg-8">
							<input type="number" step="0.0001" class="form-control" id="mat_thick" name="mat_thick" placeholder="Material Thickness" value="{{$mat_dats->mat_thick}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="purchased_at">Purchased At</label>
						<div class="col-lg-8">
							<input type="date" class="form-control" id="purchased_at" name="purchased_at" placeholder="date" @if(!is_null($mat_dats->purchased_at)) value="{{$mat_dats->purchased_at}}" @endif>
						</div>
					</div>
					<div class="form-group row">
							<label class="col-lg-4 col-form-label" for="arrived_at">Arrived At</label>
							<div class="col-lg-8">
							<input type="date" class="form-control" id="arrived_at" name="arrived_at" placeholder="date" @if(!is_null($mat_dats->arrived_at)) value="{{$mat_dats->arrived_at}}" @endif>
							</div>
						</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="remark">Remark</label>
						<div class="col-lg-8">
								<input type="text" class="form-control" id="remark" name="remark" placeholder="remark" value="{{$mat_dats->remark}}">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto">
							<input type="hidden" name="proc_old" id="proc_old" value={{ $mat_dats->pp_proc_id }} />
							<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $mat_dats->mat_id }}" />
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
		url:"{{ route('pp.update', [$proj_id, $pp_id]) }}",
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
