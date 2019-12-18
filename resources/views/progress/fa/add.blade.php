<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
		<h3 class="block-title modal-title" id="modelHeading">Add New Piecepart</h3>
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
				<form method="POST" id="piecepart_form" name="piecepart_form" class="js-validation-bootstrap form-horizontal">
					@csrf
					<div class="form-group row mt-lvl-1">
						<label class="col-lg-4 col-form-label" for="mat_id">Select Material</label>
						<div class="col-lg-8">
							<select class="form-control" id="mat_id" name="mat_id">
								<option>Select Material</option>
								@foreach ( $dats as $item)
								<option value="{{ $item->mat_id }}"> {{ $item->mt_name }} -  {{ $item->mat_no }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<fieldset class="form-group">
						<div class="row">
							<legend class="col-form-label col-sm-4 pt-0">Process</legend>
							<div class="col-sm-8">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="marking" id="marking" onclick="validate()">
									<label class="form-check-label" for="marking">
										Marking
									</label>
								</div>
								<div class="form-check" id="cutting-div" style="display:none;">
									<input class="form-check-input" type="checkbox" name="cutting" id="cutting">
									<label class="form-check-label" for="cutting">
										Cutting
									</label>
								</div>
								<div class="form-check" id="bending-div" style="display:block;">
									<input class="form-check-input" type="checkbox" name="bending" id="bending" checked disabled>
									<label class="form-check-label" for="bending">
										Bending
									</label>
								</div>
							</div>
						</div>
					</fieldset>

					<div class="form-group row" id="pp-count-div" style="display:none;">
						<label class="col-lg-4 col-form-label" for="pp_count">Piecepart Total</label>
						<div class="col-lg-8">
							<input type="number" onKeyDown="return false" step="1" min="1" class="form-control" id="pp_count" name="pp_count" value="1">
						</div>
					</div>
					<div class="form-row" id="pp1">
						<div class="form-group col-md-6">
							<label for="pp_name1">Piecepart Name</label>
							<input type="text" class="form-control" name="pp_name1" id="pp_name1" placeholder="piecepart name">
						</div>
						<div class="form-group col-md-6">
							<label for="remark1">Remark</label>
							<input type="text" class="form-control" name="remark1" id="remark1" placeholder="remark">
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
function validate() {
	if (document.getElementById('marking').checked) {
		document.getElementById('pp-count-div').style.display = "";
		document.getElementById('cutting-div').style.display = "";
		document.getElementById('bending-div').style.display = "none";
	} else {
		document.getElementById('pp-count-div').style.display = "none";
		document.getElementById('cutting-div').style.display = "none";
		document.getElementById('bending-div').style.display = "";
	}
}

function add_form(index){
	var format= ' \
	<div class="form-row" id="pp%d"> \
	<div class="form-group col-md-6"> \
	<label for="pp_name%d">Piecepart Name</label> \
	<input type="text" class="form-control" name="pp_name%d" id="pp_name%d" placeholder="piecepart name"> \
	</div> \
	<div class="form-group col-md-6"> \
	<label for="remark%d">Remark</label> \
	<input type="text" class="form-control" name="remark%d" id="remark%d" placeholder="remark"> \
	</div> \
	</div> \
	'.trim();

	var html= format.replace(/%d/g, index);
	var old_index= index - 1;
	$('#pp'+ old_index).after(html);
}

function remove_form(index){
	$('#pp'+ index).remove();
}

$("#pp_count").bind('keyup change click', function (e) {
	if (! $(this).data("previousValue") || $(this).data("previousValue") < $(this).val())
	{
		add_form( $(this).val());
		$(this).data("previousValue", $(this).val());
	}
	else if (! $(this).data("previousValue") || $(this).data("previousValue") > $(this).val())
	{
		remove_form( $(this).data("previousValue"));
		$(this).data("previousValue", $(this).val());
	}
});




$("#pp_count").each(function () {
    $(this).data("previousValue", $(this).val());
});

$('#piecepart_form').on('submit', function(event){
	event.preventDefault();
	var data= new FormData(document.getElementById("piecepart_form"));
	var checkbox = $("#piecepart_form").find("input[type=checkbox]");
	$.each(checkbox, function(key, val) {
			data.append($(val).attr('name'), $(val).is(':checked'))
	});
	$.ajaxSetup({
    headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
	$.ajax({
		url:"{{ route('fa.store', $proj_id) }}",
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
				$('#piecepart_form')[0].reset();
				$('#form_result').html(html);
				setTimeout(function(){ $("#cancel_button").click(); }, 500);
			}
		}
	})
});
</script>
