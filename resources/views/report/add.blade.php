<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Add New Plan</h3>
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
				<form method="POST" id="report_form" name="report_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="date_start">Project Created</label>
						<div class="col-lg-8">
								<input type="date" class="form-control" id="date_start" name="date_start" value={{$date_start}} readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="date_end">Last Progress</label>
						<div class="col-lg-8">
								<input type="date" class="form-control" id="date_end" name="date_end" value={{$date_end}} readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="date_estimated">Estimated Work Time</label>
						<div class="col-lg-8">
								<input type="date" class="form-control" id="date_estimated" name="date_estimated" value={{$date_end}}>
						</div>
					</div>
					@for($i=0;$i<$tot_date;$i++)
					<div class="form-row" id="report{{$i}}">
						<div class="form-group col-md-4">
							<label for="report_month{{$i}}">Month</label>
							<select readonly class="form-control" id="report_month{{$i}}" name="report_month{{$i}}">
							@for($j=1; $j <=12; $j++)
								{{-- @if($j != intval($month[$i])) @continue @endif --}}
								<?php $dateObj   = DateTime::createFromFormat('!m', $j); ?>
								<option value="{{ $j }}" {{$j == intval($month[$i]) ? 'selected' : ''}}> {{ $dateObj->format('F') }} </option>
							@endfor
							</select>
						</div>
						<div class="form-group col-md-4">
							<label for="report_year{{$i}}">Year</label>
							<input type="text" class="form-control" name="report_year{{$i}}" id="report_year{{$i}}" value="{{$year[$i]}}" readonly>
						</div>
						<div class="form-group col-md-4">
							<label for="report_plan{{$i}}">Score</label>
							<input type="number" step="0.1" class="form-control" name="report_plan{{$i}}" id="report_plan" value="0">
						</div>
					</div>
					@endfor
					<div class="form-group row">
						<div class="col-lg-8 ml-auto" style="margin-left:0!important;">
							<input type="button" name="add_plan" id="add_aplan" class="btn btn-outline-primary" value="Add a plan" />
							<input type="button" name="del_plan" id="del_aplan" class="btn btn-outline-danger" value="Remove a plan" />
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="total_score">Total Score</label>
						<div class="col-lg-8">
								<input type="number" step="0.1" class="form-control" id="total_score" name="total_score" value="0" readonly>
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
						<input type="hidden" name="proj_id" id="proj_id" value="{{$proj_id}}"/>
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
var index= {{$tot_date - 1 < 1 ? 0 : $tot_date - 1 }};
var last_month= {{  $last_month }};
var last_year= {{ $last_year }};
function add_form(){
	index++;
	last_month++;
	if(last_month > 12){
		last_month = 1;
		last_year ++;
	}
	var format= ' \
<div class="form-row" id="report%d"> \
<div class="form-group col-md-4">\
	<label for="report_month%d">Month</label>\
	<select readonly class="form-control" id="report_month%d" name="report_month%d">\
	@for($j=1; $j <=12; $j++)\
		<?php $dateObj   = DateTime::createFromFormat('!m', $j); ?>\
		<option value="{{ $j }}"> {{ $dateObj->format('F') }} </option>\
	@endfor\
	</select>\
</div>\
<div class="form-group col-md-4">\
	<label for="report_year%d">Year</label>\
	<input readonly type="text" class="form-control" name="report_year%d" id="report_year%d" value="%year">\
</div>\
<div class="form-group col-md-4">\
	<label for="report_plan%d">Score</label>\
	<input onchange="change_score()" type="number" step="0.1" class="form-control" name="report_plan%d" id="report_plan" value="0">\
</div>\
</div>\
	'.trim();

	var html= format.replace(/%d/g, index).replace(/%year/g, last_year);
	var old_index= index - 1;
	$('#report'+ old_index).after(html);

	var ddl = document.getElementById('report_month'+index);
	var opts = ddl.options.length;
	for (var i=0; i<opts; i++){
			if (ddl.options[i].value == last_month){
					ddl.options[i].selected = true;
					break;
			}
	}
}

function remove_form(){
	$('#report'+index).remove();
	index--;
}

function change_score(){
	$('input[id=total_score]').val(function(){
		return $('input[id=report_plan]:checked, input[id=report_plan][type=number]').toArray().reduce(function(tot, val) {
			return Number((tot + parseFloat(val.value)).toFixed(2));
		}, 0);
	});
}

$('#add_aplan').on('click',function(){
	add_form();
});

$('#del_aplan').on('click',function(){
	if (index == 0){
		return;
	}
	remove_form();
});

$('input[id=report_plan]').on('change', function() {
	change_score();
});

$('#report_form').on('submit', function(event){
	event.preventDefault();
	var data= new FormData(document.getElementById("report_form"));
	data.append('plan_count', index)
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url:"{{ route('reports.store', $proj_id) }}",
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
				$('#report_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});
</script>
