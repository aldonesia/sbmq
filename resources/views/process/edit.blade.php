<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Update Process Score</h3>
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
				<form method="POST" id="process_form" name="process_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<?php $index=count($dats); ?>
					@foreach($dats as $key => $adat)
					@if($adat->proc_lvl == 1)
					<div class="jumbotron">
						<br>
						<h3>{{$adat->proc_name}}</h3>
						<hr class="my-4">
					</div>
					@else
					<div class="form-row" id="proc{{$key}}">
						<div class="form-group col-md-4">
							<label for="proc_name{{$key}}">Sub Process</label>
							<input type="text" class="form-control" name="proc_name{{$key}}" id="proc_name{{$key}}" value="{{$adat->proc_name}}" readonly>
						</div>
						<div class="form-group col-md-4">
							<label for="proc_score{{$key}}">Score</label>
							<input type="number" step="0.1" class="form-control" name="proc_score{{$key}}" id="proc_score" value="{{$adat->proc_score}}">
						</div>
						<div class="form-group col-md-4">
							<label for="remark{{$key}}">Remark</label>
							<input type="text" class="form-control" id="remark{{$key}}" name="remark{{$key}}" placeholder="remark" value="{{$adat->remark}}">
						</div>
						<input type="hidden" name="proc_id{{$key}}" id="proc_id{{$key}}" value="{{$adat->proc_id}}"/>
					</div>
					@endif
					@endforeach
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="total_score">Total Score</label>
						<div class="col-lg-8">
								<input type="number" step="0.1" class="form-control" id="total_score" name="total_score" value="0" readonly>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto">
							<input type="hidden" name="action" id="action" />
							<input type="hidden" name="proj_id" id="proj_id" value="{{$proj_id}}"/>
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
var index = {{$index}};
function change_score(){
	$('input[id=total_score]').val(function(){
		return $('input[id=proc_score]:checked, input[id=proc_score][type=number]').toArray().reduce(function(tot, val) {
			return Number((tot + parseFloat(val.value)).toFixed(2));
		}, 0);
	});
}


$('input[id=proc_score]').on('change', function() {
	change_score();
});

$('#process_form').on('submit', function(event){
	event.preventDefault();
	var data= new FormData(document.getElementById("process_form"));
	data.append('proc_count', index)
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url:"{{ route('process.update', $proj_id) }}",
		method:"POST",
		data: data,
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
				$('#process_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});

change_score();
</script>
