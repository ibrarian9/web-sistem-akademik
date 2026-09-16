<div class="space-y-6 font-sans">
    @include('livewire.guru.input-nilai-tahfidz.partials.header-filter')

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('message') }}</span>
            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 rounded font-black text-[10px]">Tersimpan</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-800 rounded-xl text-xs font-bold shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    @include('livewire.guru.input-nilai-tahfidz.partials.tab-navigation')

    @if ($viewTab === 'daily')
        @include('livewire.guru.input-nilai-tahfidz.partials.table-daily')
    @endif

    @if ($viewTab === 'weekly')
        @include('livewire.guru.input-nilai-tahfidz.partials.table-weekly')
    @endif

    @if ($viewTab === 'monthly')
        @include('livewire.guru.input-nilai-tahfidz.partials.table-monthly')
    @endif

    @include('livewire.guru.input-nilai-tahfidz.partials.modal-score-form')

    @include('livewire.guru.input-nilai-tahfidz.partials.autocomplete-script')
</div>
