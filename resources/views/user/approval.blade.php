@extends('layouts.admin.index')

@section('content')
<div class="row mx-0 justify-content-center">
    <div class="hero-static col-lg-6">
        <div class="content content-full overflow-hidden">
            <div class="col-md-12">
                <div class="block block-themed block-rounded block-shadow">
                    <div class="block-header bg-secondary">
                        <h3 class="block-title"><b>Waiting for Approval</b></h3>
                    </div>

                    <div class="block-content">
                        <div class="form-group row">
                            <div class="col-12">
                                Your account is waiting for our administrator approval.
                                <br />
                                Please check back later.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection