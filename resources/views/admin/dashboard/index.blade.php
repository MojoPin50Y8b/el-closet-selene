@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">Ventas (hoy): $0</div>
        <div class="bg-white p-4 rounded shadow">Pedidos: 0</div>
        <div class="bg-white p-4 rounded shadow">Usuarios nuevos: 0</div>
        <div class="bg-white p-4 rounded shadow">Productos agotados: 0</div>
    </div>
@endsection