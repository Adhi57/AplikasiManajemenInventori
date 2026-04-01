{{-- ========================================= --}}
{{-- HERO HEADER --}}
{{-- ========================================= --}}
<div class="bg-gradient-to-br from-red-950 via-red-900 to-red-950 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden mb-6">
    <div class="absolute top-0 right-0 w-72 h-72 bg-red-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl -ml-16 -mb-16"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <i class="fa-solid {{ $icon ?? 'fa-cube' }} text-xl text-amber-400"></i>
                </div>
                <div>
                    <p class="text-sm text-red-200/80 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    <h1 class="text-2xl font-bold">{{ $title ?? 'Judul Halaman' }}</h1>
                </div>
            </div>
            <p class="text-sm text-red-200/60 max-w-lg mt-1">{{ $description ?? 'Deskripsi Halaman' }}</p>
        </div>

        {{-- Quick Actions --}}
        @isset($actions)
            <div class="flex flex-wrap gap-2 lg:justify-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
