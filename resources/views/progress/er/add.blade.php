<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Add New Ship</h3>
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
					<div class="form-group row" id="ship_form1">
						<label class="col-lg-4 col-form-label" for="block_id1">Block 1</label>
						<div class="col-lg-8">
							<select class="form-control" id="block_id1" name="block_id1" onchange="selectOnChange('block_id1')">
								<option>Select Block</option>
								@foreach ( $blockDats as $item)
								<option id="id{{ $item->block_id }}" value="{{ $item->block_id }}"> {{ $item->bt_name }} </option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-8 ml-auto" style="margin-left:0!important;">
							<input type="button" name="add_block" id="add_block" class="btn btn-outline-primary" value="Add a Block" />
							<input type="button" name="del_block" id="del_block" class="btn btn-outline-danger" value="Remove a Block" />
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
var index_max = {{count($blockDats)}};
function add_form(){
	index++;
	var format= ' \
	<div class="form-group row" id="ship_form%d"> \
	<label class="col-lg-4 col-form-label" for="block_id%d">Block %d</label> \
	<div class="col-lg-8"> \
	<select class="form-control" id="block_id%d" name="block_id%d" onchange="selectOnChange(\'block_id%d\')"> \
	<option>Select Block</option> \
	@foreach ( $blockDats as $item) \
	<option id="id{{ $item->block_id }}" value="{{ $item->block_id }}"> {{ $item->bt_name }} </option> \
	@endforeach \
	</select>\
	</div>\
	</div>\
	'.trim();

	var html= format.replace(/%d/g, index);
	var old_index= index - 1;
	$('#ship_form'+ old_index).after(html);
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
	$('#ship_form'+index).remove();
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
		url:"{{ route('er.store', $proj_id) }}",
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
