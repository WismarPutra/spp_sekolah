@extends('components.layouts.admin')

@section('content')
Laporan
@endsection

@section('main')
<div class="mb-3">
    <form action="{{ route('admin.laporan') }}" method="GET" class="d-inline">
        <select name="bulan" class="form-select d-inline-block w-auto">
            <option value="">Semua Bulan</option>
            <option value="Januari">Januari</option>
            <option value="Februari">Februari</option>
            <!-- Dan seterusnya... sesuai format teks bulan Anda -->
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <a href="{{ route('laporan.export', ['bulan' => request('bulan')]) }}" class="btn btn-success">
        Download CSV (Excel)
    </a>
</div>
@endsection