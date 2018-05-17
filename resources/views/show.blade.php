@extends('common')
@section('content')
    <style type="text/css">
        .alert {
            border-radius: 0;
            margin-bottom: 0;
        }
    </style>
    @for($i = 0; $i < $diff['count']; $i++)

        @if (in_array($i, $diff['added']))
            <p class="alert alert-success">
                {{$diff['corrected'][$i]}}
            </p>
        @endif

        @if(isset($diff['deleted'][$i]))
            <p class="alert alert-danger">
                -{{$diff['original'][$i]}}
            </p>
        @endif

        @if (isset($diff['unchanged'][$i]))
            <p class="alert alert-dark">
                {{$diff['original'][$i]}}
            </p>
        @endif

        @if (isset($diff['changed'][$i]))
            <p class="alert alert-warning">
                <span style="white-space: pre-wrap;">{{$diff['corrected'][$i]}}</span>
                <span style="white-space: pre-wrap; display: none;">{{$diff['original'][$diff['changed'][$i]]}}</span>
            </p>
        @endif
    @endfor

    <script type="text/javascript">
        $(document).ready(function () {
            $('.alert-warning').hover(
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
