@extends('layouts.admin')
@section('title', 'Nouveau Plan')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.plans.index') }}" class="text-gray-400 hover:text-gray-600">← Retour</a>
        <h1 class="text-2xl font-bold text-gray-900">Nouveau plan</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-5">
            @csrf
            @include('admin.plans._form')
            <div class="pt-2">
                <button type="submit"
                        class="bg-blue-700 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-800 transition">
                    Créer le plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
