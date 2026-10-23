@extends('layouts.feed')
@section('title','Meus chamados')
@section('content')
<div class="page-body"><h1>Meus chamados ao Gubbo</h1><p><a href="{{ route('home') }}">Voltar ao Lobby</a></p>@foreach($tickets as $t)<p style="padding:14px;margin:10px 0;border:1px solid #71569a;border-radius:10px"><a href="{{ route('support.show',$t->id) }}">#{{ $t->id }} — {{ $t->subject }}</a> · {{ $t->status }}</p>@endforeach{{ $tickets->links() }}</div>
@endsection