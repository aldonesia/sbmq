<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Update Panel Process</h3>
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
				<form method="POST" id="panel_form" name="panel_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="proc_id">Process</label>
						<div class="col-lg-8">
							<select class="form-control" id="proc_id" name="proc_id">
								@for ($i=0; $i < count($_SaProcDats['proc_id']); $i++)
									@if ($_SaProcDats['proc_id'][$i] < $panDats->proc_id) @continue @endif
									<option value="{{ $_SaProcDats['proc_id'][$i] }}" {{ $_SaProcDats['proc_id'][$i] == $panDats->proc_id ? 'selected' : '' }}> {{ $_SaProcDats['proc_name'][$i] }} </option>
								@endfor
							</select>
						</div>
					</div>
					@for($i=1; $i < count($ppSelected) + 1; $i++)
						<div class="form-group row" id="pp_form{{$i}}">
							<label class="col-lg-4 col-form-label" for="pp_id{{$i}}">Piecepart {{$i}}</label>
							<div class="col-lg-8">
								<select class="form-control" id="pp_id{{$i}}" name="pp_id{{$i}}" onchange="selectOnChange('pp_id{{$i}}')">
									@foreach ( $ppDats as $item)
										@if(in_array($item->pp_id, $ppSelectedIds) && $item->pp_id == $ppSelected[$i-1]->pp_id)
											<option id="id{{ $item->pp_id }}" value="{{ $item->pp_id }}" selected> {{ $item->mt_name . ' - ' .$item->pp_name }} </option>
										@elseif(in_array($item->pp_id, $ppSelectedIds) && $item->pp_id != $ppSelected[$i-1]->pp_id)
											<option id="id{{ $item->pp_id }}" value="{{ $item->pp_id }}" disabled> {{ $item->mt_name . ' - ' .$item->pp_name }} </option>
										@else
											<option id="id{{ $item->pp_id }}" value="{{ $item->pp_id }}"> {{ $item->mt_name . ' - ' .$item->pp_name }} </option>
										@endif
									@endforeach
								</select>
							</div>
						</div>
					@endfor
					<div class="form-group row">
						<div class="col-lg-8 ml-auto" style="margin-left:0!important;">
							<input type="button" name="add_pp" id="add_pp" class="btn btn-outline-primary" value="Add a piecepart" />
							<input type="button" name="del_pp" id="del_pp" class="btn btn-outline-danger" value="Remove a piecepart" />
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="pan_no">Panel No</label>
						<div class="col-lg-8">
						<input readonly type="text" class="form-control" id="pan_no" name="pan_no" placeholder="pan_no" value="{{$panDats->pan_no}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="pt_id">Panel Type</label>
						<div class="col-lg-8">
							<select class="form-control" id="pt_id" name="pt_id">
								<option>Select Panel Type</option>
								@foreach ( $ptDats as $item)
								<option value="{{ $item->pt_id }}" {{ $item->pt_id == $panDats->pt_id ? 'selected': ''}}> {{ $item->pt_name }} </option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="ppos_id">Panel Position</label>
						<div class="col-lg-8">
							<select class="form-control" id="ppos_id" name="ppos_id">
								<option>Select Panel Position</option>
								@foreach ( $pposDats as $item)
								<option value="{{ $item->ppos_id }}" {{ $item->ppos_id == $panDats->ppos_id ? 'selected': ''}}> {{ $item->ppos_name }} </option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="remark">Remark</label>
						<div class="col-lg-8">
						<input type="text" class="form-control" id="remark" name="remark" placeholder="remark" value="{{$panDats->remark}}">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto">
							<input type="hidden" name="action" id="action" />
							<input type="hidden" name="old_pp_ids" id="old_pp_ids" value="{{$ppSelectedIdsStr}}" />
							<input type="hidden" name="old_pp_count" id="old_pp_count" value="{{count($ppSelectedIds)}}" />
							<input type="hidden" name="proc_old" id="proc_old" value="{{$panDats->proc_id}}" />
							<input type="hidden" name="hidden_id" id="hidden_id" value="{{$panDats->pan_id}}" />
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
var index ={{count($ppSelected)}};
var index_max = {{count($ppDats)}};
function add_form(){
	index++;
	var format= ' \
	<div class="form-group row" id="pp_form%d"> \
	<label class="col-lg-4 col-form-label" for="pp_id%d">Piecepart %d</label> \
	<div class="col-lg-8"> \
	<select class="form-control" id="pp_id%d" name="pp_id%d" onchange="selectOnChange(\'pp_id%d\')"> \
	<option>Select Piecepart</option> \
	@foreach ( $ppDats as $item) \
	<option id="id{{ $item->pp_id }}" value="{{ $item->pp_id }}"> {{ $item->mt_name . ' - ' .$item->pp_name }} </option> \
	@endforeach \
	</select>\
	</div>\
	</div>\
	'.trim();

	var html= format.replace(/%d/g, index);
	var old_index= index - 1;
	$('#pp_form'+ old_index).after(html);
	for(var i=1; i < index; i++){
		selectOnChange('pp_id'+i)
	}
}

function remove_form(){
	// enable previous selected
	var sel=$("#pp_id"+index);
	if(sel.data("prev")){
		var prev_ids = document.querySelectorAll("#"+sel.data("prev"));
		for (var i = 0; i < prev_ids.length; i++) {
			prev_ids[i].disabled = false;
		}
	}
	$('#pp_form'+index).remove();
	index--;
}

$('#add_pp').on('click',function(){
	if(index >= index_max ){
		return alert('All Piecepart in your project have been selected !')
	}
	add_form();
});

$('#del_pp').on('click',function(){
	if (index == 1){
		return;
	}
	remove_form();
});

// $('select').on('change', function (e) {
function selectOnChange(id){
	var myselect = document.getElementById(id);
	var myoptions= myselect.options[myselect.selectedIndex];
	var idSelected = myoptions.id;
	console.log(myselect);

	// enable previous selected
	var sel=$("#"+id);
	if(sel.data("prev")){
		var prev_ids = document.querySelectorAll("#"+sel.data("prev"));
		for (var i = 0; i < prev_ids.length; i++) {
			prev_ids[i].disabled = false;
		}
	}

	// var op = document.getElementById(idSelected);
	var op = document.querySelectorAll("#"+idSelected);

	for (var i = 0; i < op.length; i++) {
		// console.log("id " + op[i].id + " selected ? " + op[i].selected +" disabled ? "+op[i].disabled);
		if (op[i].selected){
			op[i].selected = true;
		}
		else {
			op[i].disabled = true;
		}
	}

	sel.data("prev", idSelected);
};
// });

$('#panel_form').on('submit', function(event){
	event.preventDefault();
	var data= new FormData(document.getElementById("panel_form"));
	data.append('pp_count', index)
	$.ajaxSetup({
    headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
	$.ajax({
		url:"{{ route('sa.update', [$proj_id, $panDats->pan_id]) }}",
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
				$('#panel_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});
</script>
