@extends('layouts.app')
@section('title', 'Uploader des factures')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('factures.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Uploader des factures</h1>
    </div>

    {{-- Info quota --}}
    @php
        $tenant = auth()->user()->tenant;
        $restantes = $tenant->quotaDisponible();
        $pct = $tenant->quotaPourcentage();
    @endphp
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-blue-800">Quota mensuel</p>
            <p class="text-xs text-blue-600 mt-0.5">
                {{ $tenant->facturesCeMois() }} / {{ $tenant->quota_factures_mensuel }} factures utilisées ce mois
            </p>
        </div>
        <div class="text-right">
            <span class="text-lg font-bold {{ $restantes <= 5 ? 'text-yellow-600' : 'text-blue-700' }}">
                {{ $restantes }} restante{{ $restantes > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- Formulaire upload --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('factures.store') }}" enctype="multipart/form-data"
              x-data="uploadForm()" @submit.prevent="submitForm">

            @csrf

            {{-- Zone drag & drop --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fichiers PDF <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal">(max 10 fichiers, 50 Mo chacun)</span>
                </label>

                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-colors"
                     :class="{'border-blue-400 bg-blue-50': dragover, 'border-gray-300': !dragover}"
                     @dragover.prevent="dragover = true"
                     @dragleave.prevent="dragover = false"
                     @drop.prevent="handleDrop($event)">

                    <input type="file" name="pdfs[]" id="pdfs" multiple accept=".pdf,application/pdf"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           @change="handleFiles($event)">

                    <div x-show="files.length === 0">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Glissez vos PDF ici</p>
                        <p class="text-gray-400 text-sm mt-1">ou cliquez pour parcourir</p>
                    </div>

                    {{-- Liste des fichiers sélectionnés --}}
                    <div x-show="files.length > 0" class="text-left space-y-2">
                        <template x-for="(file, index) in files" :key="index">
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-700 truncate" x-text="file.name"></span>
                                    <span class="text-xs text-gray-400 flex-shrink-0" x-text="formatSize(file.size)"></span>
                                </div>
                                <button type="button" @click="removeFile(index)"
                                        class="text-gray-400 hover:text-red-500 transition flex-shrink-0 ml-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <p class="text-xs text-gray-500 text-center pt-1">
                            <span x-text="files.length"></span> fichier<span x-show="files.length > 1">s</span> sélectionné<span x-show="files.length > 1">s</span>
                            — Cliquez encore pour ajouter
                        </p>
                    </div>
                </div>

                {{-- Erreurs validation --}}
                @error('pdfs')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('pdfs.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Erreurs client-side --}}
            <div x-show="errors.length > 0" class="bg-red-50 border border-red-300 rounded-lg px-4 py-3 mb-4">
                <template x-for="error in errors" :key="error">
                    <p class="text-red-600 text-sm" x-text="error"></p>
                </template>
            </div>

            {{-- Erreurs Laravel --}}
            @if($errors->any() && !$errors->has('pdfs') && !$errors->has('pdfs.*'))
                <div class="bg-red-50 border border-red-300 rounded-lg px-4 py-3 mb-4">
                    @foreach($errors->all() as $e)
                        <p class="text-red-600 text-sm">{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Boutons --}}
            <div class="flex gap-3">
                <button type="submit"
                        :disabled="files.length === 0 || uploading"
                        class="flex-1 bg-blue-900 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg x-show="uploading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="uploading ? 'Envoi en cours…' : 'Uploader et traiter'"></span>
                </button>
                <a href="{{ route('factures.index') }}"
                   class="px-5 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition font-medium">
                    Annuler
                </a>
            </div>

            {{-- Barre de progression --}}
            <div x-show="uploading" class="mt-4">
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 bg-blue-600 rounded-full transition-all duration-300"
                         :style="'width:' + progress + '%'"></div>
                </div>
                <p class="text-xs text-gray-400 text-center mt-1" x-text="progress + '% envoyé'"></p>
            </div>
        </form>
    </div>

    {{-- Infos et conseils --}}
    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Comment ça marche ?
        </h3>
        <ol class="space-y-2 text-sm text-gray-600">
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                <span>Uploadez vos factures PDF (achat, vente ou charge)</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                <span>Notre IA (OCR + GPT) extrait automatiquement les données</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                <span>Les écritures SYSCOHADA sont générées et soumises à validation</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">4</span>
                <span>Vous validez ou corrigez les écritures avant comptabilisation</span>
            </li>
        </ol>
    </div>

</div>
@endsection

@push('scripts')
<script>
function uploadForm() {
    return {
        files: [],
        dragover: false,
        uploading: false,
        progress: 0,
        errors: [],

        handleFiles(event) {
            this.addFiles(Array.from(event.target.files));
        },

        handleDrop(event) {
            this.dragover = false;
            const dropped = Array.from(event.dataTransfer.files).filter(f => f.type === 'application/pdf');
            if (dropped.length === 0) {
                this.errors = ['Seuls les fichiers PDF sont acceptés.'];
                return;
            }
            this.addFiles(dropped);
        },

        addFiles(newFiles) {
            this.errors = [];
            const combined = [...this.files, ...newFiles];
            if (combined.length > 10) {
                this.errors = ['Maximum 10 fichiers par upload.'];
                return;
            }
            const oversized = newFiles.filter(f => f.size > 52428800);
            if (oversized.length > 0) {
                this.errors = ['Certains fichiers dépassent 50 Mo : ' + oversized.map(f => f.name).join(', ')];
                return;
            }
            this.files = combined;
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.errors = [];
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' o';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
            return (bytes / 1048576).toFixed(1) + ' Mo';
        },

        submitForm() {
            this.errors = [];
            if (this.files.length === 0) {
                this.errors = ['Veuillez sélectionner au moins un fichier PDF.'];
                return;
            }

            this.uploading = true;
            this.progress = 0;

            const formData = new FormData();
            formData.append('_token', document.querySelector('[name="_token"]').value);
            this.files.forEach(file => formData.append('pdfs[]', file));

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('factures.store') }}', true);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    this.progress = Math.round(e.loaded / e.total * 90);
                }
            };

            xhr.onload = () => {
                this.progress = 100;
                if (xhr.status === 302 || (xhr.status >= 200 && xhr.status < 300)) {
                    // Suivre la redirection
                    const redirect = xhr.responseURL || '{{ route('factures.index') }}';
                    window.location.href = redirect;
                } else {
                    this.uploading = false;
                    this.errors = ['Une erreur est survenue. Veuillez réessayer.'];
                }
            };

            xhr.onerror = () => {
                this.uploading = false;
                this.errors = ['Erreur réseau. Vérifiez votre connexion.'];
            };

            xhr.send(formData);
        }
    }
}
</script>
@endpush
