<div class="form-group">
    <label>Pilih Fasilitas</label>
    <select name="facility_id" class="form-control" required>
        
        {{-- HANYA MANAGER YANG LIHAT OPSI INI --}}
        @if(Auth::user()->role->role_name === 'Manager')
            <option value="" class="font-weight-bold text-danger">
                ⚠️ BLOCK SEMUA FASILITAS (Global Suspend)
            </option>
            <option disabled>---------------------------------</option>
        @endif

        {{-- OPSI NORMAL --}}
        @foreach($facilities as $facility)
            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
        @endforeach

    </select>
    <small class="text-muted">
        Manager bisa memilih opsi paling atas untuk memblokir akses resident ke seluruh fasilitas asrama.
    </small>
</div>