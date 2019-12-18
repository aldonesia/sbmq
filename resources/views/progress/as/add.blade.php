<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Add New Block</h3>
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
				<form method="POST" id="block_form" name="block_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<div class="form-group row" id="block_form1">
						<label class="col-lg-4 col-form-label" for="pan_id1">Panel 1</label>
						<div class="col-lg-8">
							<select class="form-control" id="pan_id1" name="pan_id1" onchange="selectOnChange('pan_id1')">
								<option>Select Panel</option>
								@foreach ( $panDats as $item)
								<option id="id{{ $item->pan_id }}" value="{{ $item->pan_id }}"> {{ $item->pt_name . ' - ' .$item->ppos_name  . ' - no ' .$item->pan_no }} </option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto" style="margin-left:0!important;">
							<input type="button" name="add_pan" id="add_pan" class="btn btn-outline-primary" value="Add a Panel" />
							<input type="button" name="del_pan" id="del_pan" class="btn btn-outline-danger" value="Remove a Panel" />
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label" for="bt_id">Block Type</label>
						<div class="col-lg-8">
							<select class="form-control" id="bt_id" name="bt_id">
								<option>Select Block Type</option>
								@foreach ( $btDats as $item)
								<option value="{{ $item->bt_id }}"> {{ $item->bt_name }} </option>
								@endforeach
							</select>
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
var index =1;
var index_max = {{count($panDats)}};
function add_form(){
	index++;
	var format= ' \
	<div class="form-group row" id="block_form%d"> \
	<label class="col-lg-4 col-form-label" for="pan_id%d">Panel %d</label> \
	<div class="col-lg-8"> \
	<select class="form-control" id="pan_id%d" name="pan_id%d" onchange="selectOnChange(\'pan_id%d\')"> \
	<option>Select Panel</option> \
	@foreach ( $panDats as $item) \
	<option id="id{{ $item->pan_id }}" value="{{ $item->pan_id }}"> {{ $item->pt_name . ' - ' .$item->ppos_name }} </option> \
	@endforeach \
	</select>\
	</div>\
	</div>\
	'.trim();

	var html= format.replace(/%d/g, index);
	var old_index= index - 1;
	$('#block_form'+ old_index).after(html);
	for(var i=1; i < index; i++){
		selectOnChange('pan_id'+i)
	}
}

function remove_form(){
	// enable previous selected
	var sel=$("#pan_id"+index);
	if(sel.data("prev")){
		var prev_ids = document.querySelectorAll("#"+sel.data("prev"));
		for (var i = 0; i < prev_ids.length; i++) {
			prev_ids[i].disabled = false;
		}
	}
	$('#block_form'+index).remove();
	index--;
}

$('#add_pan').on('click',function(){
	if(index >= index_max ){
		return alert('All Panel in your project have been selected !')
	}
	add_form();
});

$('#del_pan').on('click',function(){
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

$('#block_form').on('submit', function(event){
	event.preventDefault();
	var data= new FormData(document.getElementById("block_form"));
	data.append('pan_count', index)
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url:"{{ route('as.store', $proj_id) }}",
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
				$('#block_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});
</script>
	