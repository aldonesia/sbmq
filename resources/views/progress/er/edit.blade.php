<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Update Ship Process</h3>
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
				<form method="POST" id="ship_form" name="ship_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="proc_id">Process</label>
						<div class="col-lg-8">
							<select class="form-control" id="proc_id" name="proc_id" onchange="procOnChange()" >
								@for ($i=0; $i < count($_ErProcDats['proc_id']); $i++)
									@if ($_ErProcDats['proc_id'][$i] < $shipDats->proc_id) @continue @endif
									<option value="{{ $_ErProcDats['proc_id'][$i] }}" {{ $_ErProcDats['proc_id'][$i] == $shipDats->proc_id ? 'selected' : '' }}> {{ $_ErProcDats['proc_name'][$i] }} </option>
								@endfor
							</select>
						</div>
					</div>
					@for($i=1; $i < count($blockSelected) + 1; $i++)
						<div class="form-group row" id="block_form{{$i}}">
							<label class="col-lg-4 col-form-label" for="block_id{{$i}}">Block {{$i}}</label>
							<div class="col-lg-8">
								<select class="form-control" id="block_id{{$i}}" name="block_id{{$i}}" onchange="selectOnChange('block_id{{$i}}')">
									@foreach ( $blockDats as $item)
										@if(in_array($item->block_id, $blockSelectedIds) && $item->block_id == $blockSelected[$i-1]->block_id)
											<option id="block_id{{ $item->block_id }}" value="{{ $item->block_id }}" selected> {{ $item->bt_name }}  </option>
										@elseif(in_array($item->block_id, $blockSelectedIds) && $item->block_id != $blockSelected[$i-1]->block_id)
											<option id="block_id{{ $item->block_id }}" value="{{ $item->block_id }}" disabled> {{ $item->bt_name }}  </option>
										@else
											<option id="block_id{{ $item->block_id }}" value="{{ $item->block_id }}"> {{ $item->bt_name }}  </option>
										@endif
									@endforeach
								</select>
							</div>
						</div>
					@endfor
					<div class="form-group row">
						<div class="col-lg-8 ml-auto" style="margin-left:0!important;">
							<input type="button" name="add_block" id="add_block" class="btn btn-outline-primary" value="Add a Block" />
							<input type="button" name="del_block" id="del_block" class="btn btn-outline-danger" value="Remove a Block" />
						</div>
					</div>
				<div class="form-group row" id="delivery_form" {{$shipDats->proc_id < 27 ? "style=\"display: none;\"" : ""}}>
							<label class="col-lg-4 col-form-label" for="delivery_at">Delivery at</label>
							<div class="col-lg-8">
								<input type="date" class="form-control" id="delivery_at" name="delivery_at" placeholder="delivery_at" value="{{ date_format( date_create( $shipDats->delivered_at ), "Y-m-d" )}}">
							</div>
						</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="remark">Remark</label>
						<div class="col-lg-8">
						<input type="text" class="form-control" id="remark" name="remark" placeholder="remark" value="{{$shipDats->remark}}">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto">
							<input type="hidden" name="action" id="action" />
							<input type="hidden" name="old_block_ids" id="old_block_ids" value="{{$blockSelectedIdsStr}}" />
							<input type="hidden" name="old_block_count" id="old_block_count" value="{{count($blockSelectedIds)}}" />
							<input type="hidden" name="proc_old" id="proc_old" value="{{$shipDats->proc_id}}" />
							<input type="hidden" name="hidden_id" id="hidden_id" value="{{$shipDats->ship_id}}" />
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
var index ={{count($blockSelected)}};
var index_max = {{count($blockDats)}};
function add_form(){
	index++;
	var format= ' \
	<div class="form-group row" id="block_form%d"> \
	<label class="col-lg-4 col-form-label" for="block_id%d">Block %d</label> \
	<div class="col-lg-8"> \
	<select class="form-control" id="block_id%d" name="block_id%d" onchange="selectOnChange(\'block_id%d\')"> \
	<option>Select Block</option> \
	@foreach ( $blockDats as $item) \
	<option id="block_id{{ $item->block_id }}" value="{{ $item->block_id }}"> {{ $item->bt_name }}  </option> \
	@endforeach \
	</select>\
	</div>\
	</div>\
	'.trim();

	var html= format.replace(/%d/g, index);
	var old_index= index - 1;
	$('#block_form'+ old_index).after(html);
	for(var i=1; i < index; i++){
		selectOnChange('block_id'+i)
	}
}

function remove_form(){
	// enable previous selected
	var sel=$("#block_id"+index);
	if(sel.data("prev")){
		var prev_ids = document.querySelectorAll("#"+sel.data("prev"));
		for (var i = 0; i < prev_ids.length; i++) {
			prev_ids[i].disabled = false;
		}
	}
	$('#block_form'+index).remove();
	index--;
}

$('#add_block').on('click',function(){
	if(index >= index_max ){
		return alert('All Block in your project have been selected !')
	}
	add_form();
});

$('#del_block').on('click',function(){
	if (index == 1){
		return;
	}
	remove_form();
});

function procOnChange(){
	var myselect = document.getElementById('proc_id');
	var myoptions= myselect.options[myselect.selectedIndex];
	var selectedVal = myoptions.value;
	console.log(selectedVal);
	if(selectedVal >= 27){ // delivery
		document.getElementById('delivery_form').style.display = "";
	}
	else{
		document.getElementById('delivery_form').style.display = "none";
	}
}

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

$('#ship_form').on('submit', function(event){
	event.preventDefault();
	var data= new FormData(document.getElementById("ship_form"));
	data.append('block_count', index)
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url:"{{ route('er.update', [$proj_id, $shipDats->ship_id]) }}",
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
				$('#ship_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});
</script>
	