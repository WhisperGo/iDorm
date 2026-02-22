{{-- resources/views/components/time-dropdown.blade.php --}}
{{-- Tambahkan 'value' => null di baris props --}}
@props(['name', 'label', 'required' => false, 'value' => null])

<div class="mb-3">
    <label class="form-label fw-bold">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}" class="form-select" {{ $required ? 'required' : '' }}>
        <option value="">-- Pilih {{ $label }} --</option>
        @for ($i = 0; $i <= 23; $i++)
            @php 
                $h = str_pad($i, 2, '0', STR_PAD_LEFT); 
            @endphp
            {{-- LOGIKA BARU: Cek old() DULU, kalau gak ada baru cek $value dari AI --}}
            <option value="{{ $h }}:00" {{ old($name, $value) == "$h:00" ? 'selected' : '' }}>{{ $h }}:00</option>
            <option value="{{ $h }}:30" {{ old($name, $value) == "$h:30" ? 'selected' : '' }}>{{ $h }}:30</option>
        @endfor
    </select>
</div>