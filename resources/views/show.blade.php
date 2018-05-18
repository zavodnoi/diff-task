@extends('common')
@section('content')
    <style type="text/css">
        span {
            display: inline-block;
            margin: 0;
            padding: 0;
        }
    </style>
    @for($i = 0; $i < $diff['count']; $i++)
        @if (isset($diff['unchanged'][$i]))
            <span style="background: darkgrey; ">
                {!!$diff['original'][$i]!!}
            </span>
        @endif

        @if (in_array($i, $diff['added']))
            <span style="background: #28a745; ">
                {!!$diff['corrected'][$i]!!}
            </span>
        @endif

        @if(isset($diff['deleted'][$i]))
            <span style="background: #dc3545; ">
                {!!$diff['original'][$i]!!}
            </span>
        @endif

        @if (isset($diff['changed'][$i]))
            <span class="changed" style="background:#ffc107;">
                <span style="">{!!$diff['corrected'][$i]!!}</span>
                <span style=" display: none;">{!!$diff['original'][$diff['changed'][$i]]!!}</span>
            </span>
        @endif
    @endfor

    <script type="text/javascript">
        $(document).ready(function () {
            $('.changed').hover(
                function () {
                    $(this).find("span:first").hide();
                    $(this).find("span:last").show();
                }, function () {
                    $(this).find("span:last").hide();
                    $(this).find("span:first").show();
                }
            );
        });
    </script>
@stop
