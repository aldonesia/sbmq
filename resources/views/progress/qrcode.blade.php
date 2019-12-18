@if(str_contains(url()->current(), '/pp/'))
    <img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/pp.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qrcode)) !!}">    
@elseif(str_contains(url()->current(), '/pr/'))
    <img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/pr.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qrcode)) !!}">
@elseif(str_contains(url()->current(), '/fa/'))
    <img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/fa.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qrcode)) !!}">    
@elseif(str_contains(url()->current(), '/sa/'))
    <img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/sa.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qrcode)) !!}">
@elseif(str_contains(url()->current(), '/as/'))
    <img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/as.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qrcode)) !!}">    
@elseif(str_contains(url()->current(), '/er/'))
    <img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/er.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qrcode)) !!}">      
@endif
