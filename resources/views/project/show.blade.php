@extends('layouts.progress.index')

@section('content')
<!-- Page Content -->
<div class="content">
    <!-- Bootstrap Forms Validation -->
    <h2 class="content-heading">Project : {{ $projects->proj_name }}</h2>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Project Details</h3>
        </div>
        <div class="block-content">
            <div class="row justify-content-center py-20">
                <div class="col-xl-10">
                    <!-- jQuery Validation functionality is initialized in js/pages/be_forms_validation.min.js which was auto compiled from _es6/pages/be_forms_validation.js -->
                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->    

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="user_name">Creator </label>
                        <div class="col-lg-8">
                            {{ $projects->name }}  
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="proj_name">Name </label>
                        <div class="col-lg-8">
                            {{ $projects->proj_name }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="proj_building_no">Building No. </label>
                        <div class="col-lg-8">
                            {{ $projects->proj_building_no }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="proj_owner">Owner </label>
                        <div class="col-lg-8">
                            {{ $projects->proj_owner }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="proj_workgroup">Workgroup </label>
                        <div class="col-lg-8">
                            {{ $projects->proj_workgroup }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="proj_weight_factor">Weight Factor </label>
                        <div class="col-lg-8">
                            {{ $projects->proj_weight_factor }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="remark">Remark </label>
                        <div class="col-lg-8">
                            {{ $projects->remark }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Bootstrap Forms Validation -->
</div>
<!-- END Page Content -->
@endsection
