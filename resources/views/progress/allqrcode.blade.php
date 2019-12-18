<!doctype html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>All QR Code</title>
    </head>
    <body>
        <table>
            @foreach($qrcode as $key => $qr)
                @if ($loop->iteration % 2 == 1)
                <tr>
                    <td style="text-align: center;">
                        {{$name[$key]}}<br>
                        
                        @if(str_contains(url()->current(), '/pp/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/pp.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">    
                        @elseif(str_contains(url()->current(), '/pr/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/pr.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">
                        @elseif(str_contains(url()->current(), '/fa/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/fa.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">    
                        @elseif(str_contains(url()->current(), '/sa/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/sa.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">
                        @elseif(str_contains(url()->current(), '/as/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/as.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">    
                        @elseif(str_contains(url()->current(), '/er/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/er.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">      
                        @endif
                    </td>
                @endif
                @if ($loop->iteration % 2 == 0)
                    <td style="text-align: center;">
                        {{$name[$key]}}<br>

                        @if(str_contains(url()->current(), '/pp/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/pp.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">    
                        @elseif(str_contains(url()->current(), '/pr/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/pr.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">
                        @elseif(str_contains(url()->current(), '/fa/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/fa.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">    
                        @elseif(str_contains(url()->current(), '/sa/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/sa.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">
                        @elseif(str_contains(url()->current(), '/as/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/as.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">    
                        @elseif(str_contains(url()->current(), '/er/all/qrcode'))
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('images/er.png', 0.3, true)->size(298)->errorCorrection('H')->generate($qr)) !!}">      
                        @endif
                    </td>
                </tr>             
                @endif
            @endforeach
        </table>
    </body>
</html>
