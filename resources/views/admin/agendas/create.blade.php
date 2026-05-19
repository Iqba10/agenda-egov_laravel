<x-app-layout>
    <x-slot name="header">Tambah Agenda</x-slot>
    <x-slot name="subheader">Isi data agenda baru secara lengkap dan konsisten.</x-slot>

    <form method="POST" action="{{ route('admin.agendas.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.agendas._form')
    </form>
</x-app-layout>
