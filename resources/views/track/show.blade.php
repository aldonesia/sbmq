@extends('layouts.track.index')

@section('content')
<!-- Main Container -->
<main id="main-container">
	<!-- Page Content -->
	<div class="bg-body-dark bg-pattern" style="background-image: url('{{ asset('media/various/bg-pattern-inverse.png') }}');">
		<div class="row mx-0 justify-content-center">
			<div class="hero-static">
				<div class="content content-full overflow-hidden">
					<!-- Header -->
					<div class="py-30 text-center">
						<a class="link-effect font-w700" href="/">
                            <img style="width:30%" src="{{ asset('media/photos/SBMQ_Website.png') }}">
						</a>
						<h1 class="h2 font-w700 mt-30 mb-10">Tracking Page</h1>
					</div>
					<!-- END Header -->
					<div class="block">
						<div class="block-content block-content-full">
							@if(isset($arr) && is_array($arr) && !empty($arr))
							<table id="item_table" class="table table-bordered table-striped table-vcenter js-dataTable-full table-responsive">
								<thead class="bg-dark" style="color: white">
									<tr>
										<th class="d-none d-sm-table-cell text-center">QR Code Number</th>
										<th class="d-none d-sm-table-cell text-center">Process</th>
										<th class="d-none d-sm-table-cell text-center">Block Type</th>
										<th class="d-none d-sm-table-cell text-center">Block Number</th>
										<th class="d-none d-sm-table-cell text-center">Panel Position</th>
										<th class="d-none d-sm-table-cell text-center">Panel Type</th>
										<th class="d-none d-sm-table-cell text-center">Panel Number</th>
										<th class="d-none d-sm-table-cell text-center">Piece Part</th>
										<th class="d-none d-sm-table-cell text-center">General Material</th>
										<th class="d-none d-sm-table-cell text-center">Material Type</th>
										<th class="d-none d-sm-table-cell text-center">Project Name</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center">{{ $arr['code'] }}</td>
										<td class="text-center">{{ $arr['process']  }}</td>
										<td class="text-center">{{ $arr['block_type']  }}</td>
										<td class="text-center"><?php echo $arr['block_no'];  ?></td>
										<td class="text-center">{{ $arr['pan_position']  }}</td>
										<td class="text-center">{{ $arr['pan_type']  }}</td>
										<td class="text-center"><?php echo $arr['pan_no']; ?></td>
										<td class="text-center"><?php echo $arr['piece_part'];  ?></td>
										<td class="text-center">{{ $arr['general_mat']  }}</td>
										<td class="text-center">{{ $arr['mat_type']  }}</td>
										<td class="text-center">{{ $arr['proj_name']  }}</td>
									</tr>
								</tbody>
							</table>
							@endif
							@if(isset($logs) && !empty($logs))
							<table id="log_table" class="table table-bordered table-striped table-vcenter">
								<thead>
										<tr>
												<th class="d-none d-sm-table-cell text-center">No</th>
												<th class="d-none d-sm-table-cell text-center">User</th>
												<th class="d-none d-sm-table-cell text-center" style="width:20%;">Process</th>
												<th class="d-none d-sm-table-cell text-center" style="width:40%;">log</th>
												<th class="d-none d-sm-table-cell text-center" style="width:20%;">Remark</th>
												<th class="text-center" style="width:10%;">Date</th>
												@auth
												@if(Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')
												<th class="d-none d-sm-table-cell text-center" style="width:20%;">Action</th>
												@endif
												@endif
										</tr>
								</thead>
								<tbody>
										<?php $i=1;?>
										@foreach($logs as $key => $alog)
										<tr>
												<td class="text-center">{{$i++}}</td>
												<td class="d-none d-sm-table-cell text-center">{{$alog->name}}</td>
												<td class="d-none d-sm-table-cell text-center">{{$alog->proc_name}}</td>
												<td class="d-none d-sm-table-cell text-center">{{$alog->prog_remark}}</td>
												<td class="d-none d-sm-table-cell text-center">{{$alog->remark}}</td>
												<td class="text-center">{{date_format($alog->created_at, "d M y")}}</td>
												@auth
												@if(Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')
													@if($alog->ship_id > 0)
													<td class="d-none d-sm-table-cell text-center">
														<a href="{{ route('er.edit', [$alog->proj_id, $alog->ship_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
															<button type="button" title="Update Item" name="Update Item" id="Update Item" class="update btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
														</a>
													</td>
													@elseif($alog->block_id > 0)
													<td class="d-none d-sm-table-cell text-center">
														<a href="{{ route('as.edit', [$alog->proj_id, $alog->block_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
															<button type="button" title="Update Item" name="Update Item" id="Update Item" class="update btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
														</a>
													</td>
													@elseif($alog->pan_id > 0)
													<td class="d-none d-sm-table-cell text-center">
														<a href="{{ route('sa.edit', [$alog->proj_id, $alog->pan_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
															<button type="button" title="Update Item" name="Update Item" id="Update Item" class="update btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
														</a>
													</td>
													@elseif($alog->pp_id > 0)
													<td class="d-none d-sm-table-cell text-center">
														<a href="{{ route('fa.edit', [$alog->proj_id, $alog->pp_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
															<button type="button" title="Update Item" name="Update Item" id="Update Item" class="update btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
														</a>
													</td>
													@elseif($alog->mat_id > 0 && in_array($alog->proc_id, array(6,7,8,9)))
													<td class="d-none d-sm-table-cell text-center">
														<a href="{{ route('pr.edit', [$alog->proj_id, $alog->mat_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
															<button type="button" title="Update Item" name="Update Item" id="Update Item" class="update btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
														</a>
													</td>
													@elseif($alog->mat_id > 0 && in_array($alog->proc_id, array(2,3,4,5)))
													<td class="d-none d-sm-table-cell text-center">
														<a href="{{ route('pp.edit', [$alog->proj_id, $alog->mat_id]) }}" data-remote="false" data-toggle="modal" data-target="#formModal">
															<button type="button" title="Update Item" name="Update Item" id="Update Item" class="update btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
														</a>
													</td>
													@else
													<td class="d-none d-sm-table-cell text-center"></td>
													@endif
												@endif
												@endif
										</tr>
										@endforeach
							</table>
							@endif
							@if(isset($message) && !empty($message))
							<div class="form-group row text-center">
								<div class="col-12">
									<h2>{{ $message }}</h2>
								</div>
							</div>
							@endif
							<div class="block-content bg-gray-light">
								<div class="form-group text-center">
									<a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="/track">
										<i class="fa fa-reply text-muted mr-5"></i><b> Track Other Item</b>
								</a>
								<a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="/">
										<i class="fa fa-home text-muted mr-5"></i><b> Back to Home Page</b>
								</a>
								@auth
								<a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="/admin">
									<i class="fa fa-dashboard text-muted mr-5"></i><b> Back to Admin Page</b>
								</a>
								@endauth
								</div>
						</div>
						</div>
					</div>
			</div>
		</div>
	</div>
	<!-- END Page Content -->
</main>
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
	// show modal
	$("#formModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-content").load(link.attr("href"));
	});

	// close modal
	$("#formModal").on("hide.bs.modal", function(e) {
		$('.modal-content').html('');
	});
});
</script>
@endsection
