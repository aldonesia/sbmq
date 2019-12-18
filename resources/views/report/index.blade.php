@extends('layouts.progress.index')

@section('content')
<div class="content">
	<h2 class="content-heading">Reports</h2>

	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Report Table <small>Full</small></h3>
			@if (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')
			<a href="{{ route('process.edit', $proj_id) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
				<button type="button"name="edit_process" id="edit_process" class="btn btn-sm btn-rounded btn-noborder btn-info"><i class="fa fa-edit text-primary-dark"></i>&nbsp;&nbsp;Update Process Score</button>
			</a>
			&nbsp;&nbsp;
			@if($flag < 1)
			<a href="{{ route('reports.create', $proj_id) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
				<button type="button"name="add_plan" id="add_plan" class="btn btn-sm btn-rounded btn-noborder btn-primary"><i class="fa fa-plus text-primary-dark"></i>&nbsp;&nbsp;Add a plan</button>
			</a>
			@else
			<a href="{{ route('reports.edit', [$proj_id, $proj_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
				<button type="button"name="edit_plan" id="edit_plan" class="btn btn-sm btn-rounded btn-noborder btn-primary"><i class="fa fa-edit text-primary-dark"></i>&nbsp;&nbsp;Update plan</button>
			</a>
			@endif
			@endif
		</div>
		<div class="block-content block-content-full">
			<!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
			<table id="report_table" class="table table-bordered table-striped table-vcenter table-responsive">
				<tbody>
					<tr>
						<th rowspan="3" class="text-center" style="width: 25%;">Description / Time Frame</th>
						@for($i=0; $i < count($year); $i ++)
							<td colspan="{{ count($month[$year[$i]]) }}" class="text-center">{{ $year[$i] }}</td>
						@endfor
						<tr>
							<?php echo $monthstr; ?>
						</tr>
						<tr>
							<?php echo $keystr; ?>
						</tr>
					</td>
					<tr>
						<th>Plan Progress / Month ( % )</th>
						<?php echo $planstr; ?>
					</tr>
					<tr>
						<th>Plan Progress Cumulative ( % )</th>
						<?php echo $planCumstr; ?>
					</tr>
					<tr>
						<th>Realization Progress / Month ( % )</th>
						@foreach($realMonScore as $realMonth)
							<td class="text-center">{{ $realMonth }}</td>
						@endforeach
					</tr>
					<tr>
						<th>Realization Progress Cumulative( % )</th>
						@foreach($realCumScore as $realCumulative)
							<td class="text-center">{{ $realCumulative }}</td>
						@endforeach
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Dynamic Table Full -->
	<div class="block">
			<div style="width:100%;">
					<canvas id="canvas"></canvas>
			</div>
	</div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-slidedown" role="document">
		<div class="modal-content">
		</div>
	</div>
</div>
@endsection

@section('ajax')
<script>
	var config = {
    type: 'line',
    data: {
        labels: [<?php echo $labels; ?>],
        datasets: [{
            label: 'Plan Progress',
            fill: false,
            backgroundColor: window.chartColors.red,
            borderColor: window.chartColors.red,
            data: [<?php echo $planDats; ?>]
        }, 
		{
            label: 'Realization Progress',
            fill: false,
            backgroundColor: window.chartColors.blue,
            borderColor: window.chartColors.blue,
            data: @json($realCumScore),
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Reports'
        },
    }
};

window.onload = function() {
	var ctx = document.getElementById('canvas').getContext('2d');
	window.myLine = new Chart(ctx, config);
};
$(document).ready(function(){
	// show modal
	$("#formModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-content").load(link.attr("href"));
	});

	// close modal
	$("#formModal").on("hide.bs.modal", function(e) {
		$('.modal-content').html('');
		location.reload();
	});
});
</script>
@endsection
