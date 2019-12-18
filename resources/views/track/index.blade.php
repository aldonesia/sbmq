@extends('layouts.track.index')

@section('content')
<!-- Main Container -->
<main id="main-container">
	<!-- Page Content -->
	<div class="bg-body-dark bg-pattern" style="background-image: url('{{ asset('media/various/bg-pattern-inverse.png') }}');">
		<div class="row mx-0 justify-content-center">
			<div class="hero-static col-lg-6 col-xl-5">
				<div class="content content-full overflow-hidden">
					<!-- Header -->
					<div class="py-30 text-center">
						<a class="link-effect font-w700" href="/">
							<img style="width:35%" src="{{ asset('media/photos/SBMQ_Website.png') }}">
						</a>
						<h1 class="h4 font-w700 mt-30 mb-10">Monitoring Your Item</h1>
						<h2 class="h5 font-w400 text-muted mb-0">Please scan your QR Code and get QR Code Number.<br>Then Click 'Search'.</h2>
					</div>
					<!-- END Header -->

					<!-- Reminder Form -->
					<!-- jQuery Validation functionality is initialized with .js-validation-reminder class in js/pages/op_auth_reminder.min.js which was auto compiled from _es6/pages/op_auth_reminder.js -->
					<!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
					<form class="js-validation-reminder" action="{{ url('/track') }}" method="POST" role="search">
						@csrf
						<div class="block block-themed block-rounded block-shadow">
								<div class="block-header bg-gd-primary">
										<h3 class="block-title"><b>QR Code Number (19 digits)</b></h3>
								</div>
								<div class="block-content">
										<div class="form-group row">
												<div class="col-12">
														<input type="text" class="qrcode-text" size=34 id="search" name="code" Placeholder="E.g: 1234567890987654321" value="{{ old('code') }}" aria-label="search">
														<label class="qrcode-text-btn" id="qrcode-text-btn" data-remote="false" data-toggle="modal" data-target="#formModal"></label>
												</div>
										</div>
										<div class="form-group text-center">
												<button type="submit" id="but_search" class="btn btn-alt-primary">
														<i class="fa fa-search mr-10"></i> Search
												</button>
										</div>
								</div>
								<br>
								<div class="block-content bg-gray-light">
										<div class="form-group text-center">
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
					</form>
					<!-- END Reminder Form -->
					<br>
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
			Scan your QR Code
			<div class="video-container">
				<video id="video-preview"></video>
				<canvas id="qr-canvas" class="hidden" ></canvas>
			</div>
		</div>
	</div>
</div>
@endsection

@section('ajax')
<script>
$(document).ready(function(){
	$('#search').change(function() {
		var value = $('#search').val();
		if(value.length > 18) {
			$('#but_search').click();
		}
	});

	$('#search').bind("change keyup", function() {
		var value = $('#search').val();
		if(value.length > 18) {
			$('#but_search').click();
		}
	});
});
</script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/grid.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/version.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/detector.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/formatinf.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/errorlevel.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/bitmat.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/datablock.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/bmparser.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/datamask.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/rsdecoder.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/gf256poly.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/gf256.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/decoder.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/qrcode.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/findpat.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/alignpat.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jsqrcode/databr.js') }}"></script>
<script type="text/javascript">
var btn = document.getElementById("qrcode-text-btn");
btn.onclick =  function() {
  /* Ask for "environnement" (rear) camera if available (mobile), will fallback to only available otherwise (desktop).
   * User will be prompted if (s)he allows camera to be started */
  navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" }, audio: false }).then(function(stream) {
    var video = document.getElementById("video-preview");
    video.srcObject = stream;
    video.setAttribute("playsinline", true); /* otherwise iOS safari starts fullscreen */
    video.play();
    setTimeout(tick, 100); /* We launch the tick function 100ms later (see next step) */
  })
  .catch(function(err) {
    console.log(err); /* User probably refused to grant access*/
  });
};
function tick() {
  var video                   = document.getElementById("video-preview");
  var qrCanvasElement         = document.getElementById("qr-canvas");
  var qrCanvas                = qrCanvasElement.getContext("2d");
  var width, height;

  if (video.readyState === video.HAVE_ENOUGH_DATA) {
    qrCanvasElement.height  = video.videoHeight;
    qrCanvasElement.width   = video.videoWidth;
    qrCanvas.drawImage(video, 0, 0, qrCanvasElement.width, qrCanvasElement.height);
    try {
      var result = qrcode.decode();
      console.log(result)
			if(result.length > 18) {
				document.getElementById("search").value = result;
				$('#but_search').click();
			}
      /* Video can now be stopped */
      video.pause();
      video.src = "";
      video.srcObject.getVideoTracks().forEach(track => track.stop());

      /* Display Canvas and hide video stream */
      qrCanvasElement.classList.remove("hidden");
      video.classList.add("hidden");
    } catch(e) {
      /* No Op */
    }
  }

  /* If no QR could be decoded from image copied in canvas */
  if (!video.classList.contains("hidden"))
    setTimeout(tick, 100);
}
</script>
@endsection
