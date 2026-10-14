<?php

use App\Enums\SlotStatus;
use App\Exceptions\SlotUnavailableException;
use App\Models\City;
use App\Models\Slot;
use App\Models\Specialty;
use App\Services\AppointmentSearch;
use App\Services\RequestAppointment;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $cityQuery = '';

    public ?int $cityId = null;

    public string $specialtyQuery = '';

    public ?int $specialtyId = null;

    public ?int $days = null;

    // Booking (guest) state.
    public ?int $openDoctorId = null;

    public ?int $bookingSlotId = null;

    public string $patientName = '';

    public string $patientPhone = '';

    public string $patientEmail = '';

    public string $patientNote = '';

    /** @var array{clinic: string, doctor: string, time: string}|null */
    public ?array $confirmation = null;

    /** Editing the city invalidates everything downstream. */
    public function updatedCityQuery(): void
    {
        $this->reset('cityId', 'specialtyQuery', 'specialtyId', 'days', 'openDoctorId', 'bookingSlotId');
    }

    public function updatedSpecialtyQuery(): void
    {
        $this->reset('specialtyId', 'days', 'openDoctorId', 'bookingSlotId');
    }

    public function selectCity(int $id): void
    {
        $city = City::find($id);

        if ($city !== null) {
            $this->cityId = $city->id;
            $this->cityQuery = $city->name;
        }
    }

    public function selectSpecialty(int $id): void
    {
        $specialty = Specialty::find($id);

        if ($specialty !== null) {
            $this->specialtyId = $specialty->id;
            $this->specialtyQuery = $specialty->name;
        }
    }

    public function chooseWindow(int $days): void
    {
        $this->days = $days;
        $this->reset('openDoctorId', 'bookingSlotId');
    }

    public function showSlots(int $doctorId): void
    {
        $this->openDoctorId = $this->openDoctorId === $doctorId ? null : $doctorId;
        $this->reset('bookingSlotId');
        $this->resetErrorBag();
    }

    public function chooseSlot(int $slotId): void
    {
        $this->bookingSlotId = $slotId;
        $this->resetErrorBag();
    }

    public function submitBooking(RequestAppointment $request): void
    {
        $this->validate([
            'bookingSlotId' => 'required|integer',
            'patientName' => 'required|string|max:255',
            'patientPhone' => 'required|string|max:50',
            'patientEmail' => 'nullable|email|max:255',
            'patientNote' => 'nullable|string|max:500',
        ]);

        try {
            $appointment = $request(
                $this->bookingSlotId,
                $this->patientName,
                $this->patientPhone,
                $this->patientEmail ?: null,
                $this->patientNote ?: null,
            );
        } catch (SlotUnavailableException $e) {
            $this->addError('bookingSlotId', $e->getMessage().' Odaberi drugi termin.');
            $this->reset('bookingSlotId');
            unset($this->results, $this->doctorSlots);

            return;
        }

        $appointment->load('clinic', 'slot.doctor');
        $this->confirmation = [
            'clinic' => $appointment->clinic->name,
            'doctor' => $appointment->slot->doctor->name,
            'time' => $appointment->slot->start_time->translatedFormat('d.m.Y. \u H:i'),
        ];

        $this->reset('openDoctorId', 'bookingSlotId', 'patientName', 'patientPhone', 'patientEmail', 'patientNote');
        unset($this->results, $this->doctorSlots);
    }

    public function dismissConfirmation(): void
    {
        $this->reset('confirmation');
    }

    /** @return Collection<int, City> */
    #[Computed]
    public function citySuggestions(): Collection
    {
        if ($this->cityId !== null || trim($this->cityQuery) === '') {
            return collect();
        }

        return City::startingWith($this->cityQuery)->limit(8)->get(['id', 'name', 'county']);
    }

    /** @return Collection<int, Specialty> */
    #[Computed]
    public function specialtySuggestions(): Collection
    {
        if ($this->cityId === null || $this->specialtyId !== null || trim($this->specialtyQuery) === '') {
            return collect();
        }

        return Specialty::matching($this->specialtyQuery)->limit(8)->get(['id', 'name']);
    }

    /** The third bar's payoff: nearby doctors with their earliest free slot. */
    #[Computed]
    public function results(): Collection
    {
        if ($this->cityId === null || $this->specialtyId === null || $this->days === null) {
            return collect();
        }

        return app(AppointmentSearch::class)->results($this->cityId, $this->specialtyId, $this->days);
    }

    /** Free slots for the expanded doctor, within the chosen window. */
    #[Computed]
    public function doctorSlots(): Collection
    {
        if ($this->openDoctorId === null || $this->days === null) {
            return collect();
        }

        return Slot::query()
            ->where('doctor_id', $this->openDoctorId)
            ->where('status', SlotStatus::Free->value)
            ->whereBetween('start_time', [now(), now()->addDays($this->days)])
            ->orderBy('start_time')
            ->limit(12)
            ->get(['id', 'start_time']);
    }
}; ?>

<div class="mx-auto max-w-3xl px-4 py-12">
    <header class="mb-8 text-center">
        <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Pronađi slobodan termin</h1>
        <p class="mt-2 text-slate-500">Grad &rarr; specijalist &rarr; najraniji slobodan termin, bez obilaženja ordinacija.</p>
    </header>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        {{-- Bar 1: grad --}}
        <div class="mb-5" x-data="{ open: false }">
            <label class="mb-1 block text-sm font-medium text-slate-700">1 &middot; Grad</label>
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="cityQuery"
                    @focus="open = true"
                    @keydown.escape="open = false"
                    autocomplete="off"
                    placeholder="npr. Zadar"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none"
                >
                @if ($this->citySuggestions->isNotEmpty())
                    <ul x-show="open" x-transition @click.outside="open = false"
                        class="absolute z-10 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                        @foreach ($this->citySuggestions as $city)
                            <li>
                                <button type="button" wire:click="selectCity({{ $city->id }})" @click="open = false"
                                    class="flex w-full items-baseline justify-between px-4 py-2 text-left hover:bg-indigo-50">
                                    <span class="text-slate-900">{{ $city->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $city->county }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Bar 2: specijalist --}}
        <div class="mb-5" x-data="{ open: false }">
            <label class="mb-1 block text-sm font-medium text-slate-700">2 &middot; Specijalist</label>
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="specialtyQuery"
                    @focus="open = true"
                    @keydown.escape="open = false"
                    autocomplete="off"
                    placeholder="npr. ortoped"
                    @disabled($cityId === null)
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                >
                @if ($this->specialtySuggestions->isNotEmpty())
                    <ul x-show="open" x-transition @click.outside="open = false"
                        class="absolute z-10 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                        @foreach ($this->specialtySuggestions as $specialty)
                            <li>
                                <button type="button" wire:click="selectSpecialty({{ $specialty->id }})" @click="open = false"
                                    class="w-full px-4 py-2 text-left text-slate-900 hover:bg-indigo-50">
                                    {{ $specialty->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Bar 3: rok --}}
        <div @class(['pointer-events-none opacity-40' => $specialtyId === null])>
            <label class="mb-1 block text-sm font-medium text-slate-700">3 &middot; Najkasnije za</label>
            <div class="flex gap-2">
                @foreach ([3 => 'Hitno (3 dana)', 7 => '7 dana', 14 => '14 dana'] as $value => $label)
                    <button type="button" wire:click="chooseWindow({{ $value }})"
                        @class([
                            'flex-1 rounded-lg border px-4 py-2.5 text-sm font-medium transition',
                            'border-indigo-600 bg-indigo-600 text-white' => $days === $value,
                            'border-slate-300 text-slate-700 hover:border-indigo-400' => $days !== $value,
                        ])>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Rezultati --}}
    @if ($days !== null || $confirmation !== null)
        <div class="mt-8">
            @if ($confirmation !== null)
                <div class="mb-4 flex items-start justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                    <div>
                        <p class="font-semibold text-emerald-800">Zahtjev poslan &check;</p>
                        <p class="text-sm text-emerald-700">
                            {{ $confirmation['clinic'] }} &mdash; {{ $confirmation['doctor'] }}, {{ $confirmation['time'] }}.
                            Klinika će potvrditi termin.
                        </p>
                    </div>
                    <button type="button" wire:click="dismissConfirmation" class="text-xl leading-none text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if ($days !== null)
                @if ($this->results->isEmpty())
                    <p class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-500">
                        Nema slobodnih termina u odabranom roku. Pokušaj produžiti rok ili drugi grad.
                    </p>
                @else
                    <h2 class="mb-3 text-sm font-medium tracking-wide text-slate-500 uppercase">
                        {{ $this->results->count() }} dostupnih termina
                    </h2>
                    <ul class="space-y-3">
                        @foreach ($this->results as $row)
                            <li class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $row->clinic }}</p>
                                        <p class="text-sm text-slate-500">{{ $row->doctor }} &middot; {{ number_format((float) $row->distance_km, 1, ',', '.') }} km</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <p class="text-xs text-slate-400">najraniji termin</p>
                                            <p class="font-medium text-indigo-700">
                                                {{ \Illuminate\Support\Carbon::parse($row->earliest)->translatedFormat('d.m.Y. \u H:i') }}
                                            </p>
                                        </div>
                                        <button type="button" wire:click="showSlots({{ $row->doctor_id }})"
                                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-500">
                                            Zatraži termin
                                        </button>
                                    </div>
                                </div>

                                @if ($openDoctorId === (int) $row->doctor_id)
                                    <div class="mt-4 border-t border-slate-100 pt-4">
                                        <p class="mb-2 text-sm font-medium text-slate-600">Odaberi termin</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($this->doctorSlots as $slot)
                                                <button type="button" wire:click="chooseSlot({{ $slot->id }})"
                                                    @class([
                                                        'rounded-lg border px-3 py-1.5 text-sm transition',
                                                        'border-indigo-600 bg-indigo-600 text-white' => $bookingSlotId === $slot->id,
                                                        'border-slate-300 text-slate-700 hover:border-indigo-400' => $bookingSlotId !== $slot->id,
                                                    ])>
                                                    {{ $slot->start_time->translatedFormat('d.m. \u H:i') }}
                                                </button>
                                            @endforeach
                                        </div>
                                        @error('bookingSlotId')
                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror

                                        @if ($bookingSlotId !== null)
                                            <form wire:submit="submitBooking" class="mt-4 grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <input type="text" wire:model="patientName" placeholder="Ime i prezime"
                                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
                                                    @error('patientName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <input type="text" wire:model="patientPhone" placeholder="Telefon"
                                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
                                                    @error('patientPhone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <input type="email" wire:model="patientEmail" placeholder="Email (neobavezno)"
                                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
                                                    @error('patientEmail') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <input type="text" wire:model="patientNote" placeholder="Napomena (neobavezno)"
                                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
                                                </div>
                                                <div class="flex justify-end sm:col-span-2">
                                                    <button type="submit"
                                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-500"
                                                        wire:loading.attr="disabled">
                                                        Pošalji zahtjev
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-4 text-center text-xs text-slate-400">Naručivanje je zahtjev &mdash; klinika potvrđuje termin.</p>
                @endif
            @endif
        </div>
    @endif
</div>
