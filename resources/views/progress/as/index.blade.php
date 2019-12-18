@extends('layouts.progress.index')

@section('content')
<!-- Page Content -->
<div class="content">
	<h2 class="content-heading">Assembly Process (AS)</h2>
	<!-- Dynamic Table Full -->
	<div class="block">
		<div class="block-header block-header-default">
			{{-- <h3 class="block-title">Assembly Process Table <small>Full</small></h3> --}}
			Filter by Process&ensp;:&emsp;
			<select class="js-select2 form-control" id="proc-filter" style="width: 20%;">
				<option value="" selected>Show All</option>
				@for ($i=0; $i < count($_AsProcDats['proc_id']); $i++)
					<option value="{{ $_AsProcDats['proc_name'][$i] }}"> {{ $_AsProcDats['proc_name'][$i] }} </option>
				@endfor
			</select>
			&nbsp;
			Filter by Status&emsp;:&emsp;
			<select class="js-select2 form-control" id="stat-filter" style="width: 24%;">
				<option value="" selected>Show All</option>
				@foreach ( $stat_dats as $item)
					<option value="{{ $item->stat_name }}"> {{ $item->stat_name }} </option>
				@endforeach
			</select>
			&nbsp;&nbsp;
			@if(DB::table('tb_block')->select('stat_id')->where('proj_id',$proj_id)->where('stat_id','>',1)->exists())
			<a href="{{ route('as.allqrcode',$proj_id) }}" target="_blank">
				<button type="button" name="download all qr code" id="download_all_qr_code" class="btn btn-sm btn-rounded btn-noborder btn-dark"><i class="fa fa-download text-primary-light"></i>&nbsp;&nbsp;Download All QR Code</button>
			</a>
			@endif
			&nbsp;
			@if (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')
				<a href="{{ route('as.create', $proj_id) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
					<button type="button"name="add_block" id="add_block" class="btn btn-sm btn-rounded btn-noborder btn-primary"><i class="fa fa-plus text-primary-dark"></i>&nbsp;&nbsp;Add Block</button>
				</a>
			@endif

		</div>
		<div class="block-content block-content-full">
			<table id="block_table" class="table table-bordered table-striped table-vcenter js-dataTable-full table-responsive">
				<thead>
					<tr>
						<th class="text-center">No</th>
						<th class="text-center" style="width: 2%;">ID</th>
						<th class="text-center" style="width: 2%;">Process</th>
						<th class="d-none d-sm-table-cell text-center">Block No </th>
						<th class="d-none d-sm-table-cell text-center">Block Type </th>
						<th class="d-none d-sm-table-cell text-center">Status</th>
						<th class="d-none d-sm-table-cell text-center">Panel</th>
						<th class="d-none d-sm-table-cell text-center">Remark</th>
						<th class="d-none d-sm-table-cell text-center">Created at</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	<!-- END Dynamic Table Full -->
</div>
<!-- END Page Content -->
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
$(document).ready(function(){
	// destroy datatable by codebase
	$("#block_table").dataTable().fnDestroy()
	// init and fill datatable
	var table= $('#block_table').DataTable({
		// dom: 'lrtip', // hide search field
		processing: true,
		serverSide: true,
		pageLength: 8,
		lengthMenu: [[5, 8, 15, 20], [5, 8, 15, 20]],
		autoWidth: false,
		ajax:{
			url: "{{ route('as.index', $proj_id) }}",
		},
		columns:[
			{
				data: null,
				sortable: false,
				class: 'text-center',
       	render: function (data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}
			},
			{
				data:'block_id',
				name:'block_id',
				class: 'text-center',
			},
			{
				data:'proc_name',
				name:'proc_name',
				class: 'text-center',
			},
			{
				data:'block_no',
				name:'block_no',
				class: 'text-center',
			},
			{
				data:'bt_name',
				name:'bt_name',
				class: 'text-center',
			},
			{
				data:'stat_name',
				name:'stat_name',
				class: 'text-center',
			},
			{
				data:'panel',
				name:'panel',
				class: 'text-center',
			},
			{
				data:'remark',
				name:'remark',
				class: 'text-center',
			},
			{
				data:'created_at',
				name:'created_at',
				class: 'text-center',
			},
			{
				data:'action',
				name:'action',
				class: 'text-center',
			},
		],
		columnDefs: [
			{
				searchable: false,
				orderable: false,
				class: 'index',
				targets: 0
			},
			{
				searchable: false,
				orderable: false,
				visible: false,
				targets: 1
			},
			{
				render: function ( data, type, row ) {
					return row.proc_shortname +' - '+data;
				},
				targets: 2,
			},
			{
				render: function ( data, type, row ) {
					return row.bt_shortname +' ('+data+')';
				},
				targets: 4,
			},
			{
				targets:8,
				render: function ( data, type, row ) {
					if(data){
						return moment(data).format('Do MMM YY');
					}
					else {
						return '-';
					}
				},
			},
		],
	});


	$('#stat-filter').on('change', function(){
		table.column(4).search(this.value).draw();
	});

	$('#proc-filter').on('change', function(){
		table.column(2).search(this.value).draw();
	});

	// show modal
	$("#formModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-content").load(link.attr("href"));
	});

	// close modal
	$("#formModal").on("hide.bs.modal", function(e) {
		$('.modal-content').html('');
		$('#block_table').DataTable().ajax.reload();
	});
});
</script>
@endsection
