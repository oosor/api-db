@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ $title  }}
                    <ul class="nav" style="position: absolute; right: 4px; top: 4px">
                        <li class="nav-item">
                            <a class="nav-link @if ($type == 'construct') active @endif" href="{{ route('show-construct') }}">Client construct</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($type == 'queries') active @endif" href="{{ route('show-queries') }}">Client queries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($type == 'token') active @endif" href="{{ route('show-token') }}">Client token</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        {!! Markdown::parse($data) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
