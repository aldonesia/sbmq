@extends('layouts.admin.index')

@section('content')
<!-- Page Content -->
<div class="content">
    <h2 class="content-heading">Projects</h2>

    <!-- Dynamic Table Full -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Project Table <small>Full</small></h3>

            @if (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')
                <button type="button" name="create new project" id="create_project" class="btn btn-sm btn-rounded btn-noborder btn-primary"><i class="fa fa-plus text-primary-dark"></i>&nbsp;&nbsp;Create Project</button>
            @endif

        </div>
        <div class="block-content block-content-full">
            <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
            <table id="project_table" class="table table-bordered table-striped table-vcenter js-dataTable-full table-responsive">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="d-none d-sm-table-cell text-center">User</th>
                        <th class="d-none d-sm-table-cell text-center" style="width: 20%;">Project Name</th>
                        <th class="d-none d-sm-table-cell text-center">Build No.</th>
                        <th class="d-none d-sm-table-cell text-center">Owner</th>
                        <th class="d-none d-sm-table-cell text-center">Workgroup</th>
                        <th class="d-none d-sm-table-cell text-center" style="width: 5%;">Weight Factor</th>
                        <th class="d-none d-sm-table-cell text-center" style="width: 10%;">Remark</th>
                        <th class="text-center" style="width: 15%;">Action</th>
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
<!-- Create New Project Modal -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-slidedown" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary">
                    <h3 class="block-title modal-title" id="modelHeading">Add New Project</h3>
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
                            <form method="POST" id="project_form" name="project_form" class="js-validation-bootstrap form-horizontal">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="proj_name">Name</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="proj_name" name="proj_name" placeholder="Project Name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="proj_building_no">Building No.</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="proj_building_no" name="proj_building_no" placeholder="Project Building Number">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="proj_owner">Owner</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="proj_owner" name="proj_owner" placeholder="Project Owner">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="proj_workgroup">Workgroup</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="proj_workgroup" name="proj_workgroup" placeholder="Project Workgroup">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="proj_weight_factor">Weight Factor</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="proj_weight_factor" name="proj_weight_factor" placeholder="Project Weight Factor">
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
        </div>
    </div>
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-slidedown" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Confirmation</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                    <button type="button" id="cancel_button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
<!-- END Create New Project Modal -->
@endsection

@section('ajax')
<script>
$(document).ready(function(){
    // destroy datatable by codebase
    $("#project_table").dataTable().fnDestroy()
    // init and fill datatable
    $('#project_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 8,
        lengthMenu: [[5, 8, 15, 20], [5, 8, 15, 20]],
        autoWidth: false,
        ajax:{
        url: "{{ route('projects.index') }}",
        },
        columns:[
            {
                data:'proj_id',
                name:'proj_id',
                class: 'text-center',
            },
            {
                data:'name',
                name:'name',
                class: 'text-center',
            },
            {
                data:'proj_name',
                name:'proj_name',
                class: 'text-center',
            },
            {
                data:'proj_building_no',
                name:'proj_building_no',
                class: 'text-center',
            },
            {
                data:'proj_owner',
                name:'proj_owner',
                class: 'text-center',
            },
            {
                data:'proj_workgroup',
                name:'proj_workgroup',
                class: 'text-center',
            },
            {
                data:'proj_weight_factor',
                name:'proj_weight_factor',
                class: 'text-center',
            },
            {
                data:'remark',
                name:'remark',
                class: 'text-center',
            },
            {
                data:'action',
                name:'action',
                class: 'text-center',
            },
        ]
    });

    // show modal create project
    $('#create_project').click(function(){
        $('#form_result').html('');
        $('#project_form')[0].reset();
        $('.modal-title').text("Add New Project");
        $('#action_button').val("Add");
        $('#action').val("Add");
        $('#formModal').modal('show');
    });

    // button submit on modal
    $('#project_form').on('submit', function(event){
        event.preventDefault();
        // if add button
        if($('#action').val() == 'Add')
        {
            $.ajax({
                url:"{{ route('projects.store') }}",
                method:"POST",
                data: new FormData(this),
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
                        $('#project_form')[0].reset();
                        $('#project_table').DataTable().ajax.reload();
												$('#form_result').html(html);
												setTimeout(function(){ $("#cancel_button").click(); }, 500);
                    }
                }
            })
        }
        // if edit button
        if($('#action').val() == "Edit")
        {
            $.ajax({
                url:"/admin/projects/update",
                method:"POST",
                data:new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType:"json",
                success:function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">';
                        for(var count = 0; count < data.errors.length; count++)
                        {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
												$('#form_result').html(html);
                    }
                    if(data.success)
                    {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        //$('#project_form')[0].reset();
                        $('#project_table').DataTable().ajax.reload();
												$('#form_result').html(html);
												setTimeout(function(){ $("#cancel_button").click(); }, 500);
                    }
                }
            });
        }
    });

    // show modal edit
    $(document).on('click', '.edit', function(){
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url:"/admin/projects/"+id+"/edit",
            dataType:"json",
            success:function(html){
                $('#proj_name').val(html.data.proj_name);
                $('#proj_building_no').val(html.data.proj_building_no);
                $('#proj_owner').val(html.data.proj_owner);
                $('#proj_workgroup').val(html.data.proj_workgroup);
                $('#proj_weight_factor').val(html.data.proj_weight_factor);
                $('#remark').val(html.data.remark);
                $('#hidden_id').val(html.data.proj_id);
                $('.modal-title').text("Edit Project");
                $('#action_button').val("Edit");
                $('#action').val("Edit");
                $('#formModal').modal('show');
            }
        })
    });

    // show modal delete
    var id;
    $(document).on('click', '.delete', function(){
        id = $(this).attr('id');
				$('.modal-title').text("Confirmation");
        $('#confirmModal').modal('show');
    });

    // confirm delete
    $('#ok_button').click(function(){
        $.ajax({
            url:"/admin/projects/destroy/"+id,
            beforeSend:function(){
                $('#ok_button').text('Deleting...');
            },
            success:function(data)
            {
                setTimeout(function(){
                    $('#ok_button').text('Ok');
                    $('#confirmModal').modal('hide');
                    $('#project_table').DataTable().ajax.reload();
                }, 500);
             }
        })
    });
});
</script>
@endsection
