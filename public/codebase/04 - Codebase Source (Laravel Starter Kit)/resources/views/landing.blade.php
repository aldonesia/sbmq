@extends('layouts.simple')

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('/media/photos/bg_hero_landing.jpg') }}');">
        <div class="hero bg-white-op-90 overflow-hidden">
            <div class="hero-inner">
                <div class="content content-full text-center">
                    <div class="pt-100 pb-150">
                        <h1 class="font-w700 display-4 mt-20 invisible" data-toggle="appear" data-timeout="50">
                            Codebase <span class="text-primary font-w300">3.0</span> <span class="font-w300">+ Laravel</span>
                        </h1>
                        <h2 class="h3 font-w400 text-muted mb-50 invisible" data-toggle="appear" data-class="animated fadeInDown" data-timeout="300">
                            Welcome to the starter kit! Build something amazing!
                        </h2>
                        <div class="invisible" data-toggle="appear" data-class="animated fadeInDown" data-timeout="300">
                            <a class="btn btn-hero btn-noborder btn-success min-width-175 mb-10 mx-5" href="/dashboard">
                                <i class="fa fa-fw fa-arrow-right mr-1"></i> Enter Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection
