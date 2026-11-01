@extends('layouts.app')
@section('title','Admin · Integrasi API')
@section('page-heading','Admin · Integrasi API')
@section('content')
<div class="mb-6 rounded-2xl border border-amber-400/20 bg-amber-400/5 p-4 text-sm text-amber-100">
<strong>Pengelola konfigurasi integrasi.</strong> Halaman ini menyimpan daftar penyedia, URL API, dan API key secara terenkripsi. Menambahkan konfigurasi belum menjalankan sinkronisasi game; adapter dan alur autentikasi tiap penyedia harus diimplementasikan serta diuji secara terpisah.
</div>
<livewire:admin.platform-integration-manager />
@endsection