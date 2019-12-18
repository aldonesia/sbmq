<div class="block block-themed block-transparent mb-0">
	<div class="block-header bg-primary">
			<h3 class="block-title modal-title" id="modelHeading">Material Log</h3>
			<div class="block-options">
					<button type="button" class="btn-block-option close" data-dismiss="modal" aria-label="Close">
							<i class="fa fa-close"></i>
					</button>
			</div>
	</div>
	<div class="block-content block-content-full">
		<table id="log_table" class="table table-bordered table-striped table-vcenter">
			<thead>
					<tr>
							<th class="d-none d-sm-table-cell text-center">No</th>
							<th class="d-none d-sm-table-cell text-center">User</th>
							<th class="d-none d-sm-table-cell text-center" style="width:20%;">Process</th>
							<th class="d-none d-sm-table-cell text-center" style="width:40%;">log</th>
							<th class="d-none d-sm-table-cell text-center" style="width:20%;">Remark</th>
							<th class="text-center" style="width:10%;">Date</th>
					</tr>
			</thead>
			<tbody>
					<?php $i=1?>
					@foreach($dats as $adat)
					<tr>
							<td class="text-center">{{$i++}}</td>
							<td class="d-none d-sm-table-cell text-center">{{$adat->name}}</td>
							<td class="d-none d-sm-table-cell text-center">{{$adat->proc_name}}</td>
							<td class="d-none d-sm-table-cell text-center">{{$adat->prog_remark}}</td>
							<td class="d-none d-sm-table-cell text-center">{{$adat->remark}}</td>
							<td class="text-center">{{date_format($adat->created_at, "d M y")}}</td>
					</tr>
					@endforeach
			</tbody>
		</table>
	</div>
</div>
