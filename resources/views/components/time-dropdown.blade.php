{{-- resources/views/components/time-dropdown.blade.php --}}
@props(['name', 'label', 'required' => false])

<div class="mb-3">
    <label class="form-label fw-bold">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}" class="form-select" {{ $required ? 'required' : '' }}>
        <option value="">-- Pilih {{ $label }} --</option>
        @for ($i = 0; $i <= 23; $i++)
            @php 
                $h = str_pad($i, 2, '0', STR_PAD_LEFT); 
            @endphp
            <option value="{{ $h }}:00" {{ old($name) == "$h:00" ? 'selected' : '' }}>{{ $h }}:00</option>
            <option value="{{ $h }}:30" {{ old($name) == "$h:30" ? 'selected' : '' }}>{{ $h }}:30</option>
        @endfor
    </select>
</div>