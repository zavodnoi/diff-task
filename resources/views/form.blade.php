@extends('common')
@section('content')
    @forelse($errors->all() as $error)
        <p class="text-danger">{{$error}}</p>
    @empty
        <div style="padding: 40px;"></div>
    @endforelse
    <form action="{{route('diff.execute')}}" method="POST">
        <div class="row">
            {{ csrf_field() }}
            <div class="col-md-6 m-10 form-group">
                <label for="original">Исходный текст</label>
                <textarea name="original" cols="60" rows="10"></textarea>
            </div>
            <div class="col-md-6 m-10 form-group">
                <label for="corrected">Исправленный текст</label>
                <textarea name="corrected" cols="60" rows="10"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Сравнить</button>
    </form>
@stop
