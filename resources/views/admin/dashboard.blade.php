@extends('admin.layouts')

@section('content')
    {{-- Bagian Header --}}
    <div class="card mb-5">
        <div class="card-body text-center py-4">
            <h4 class="card-title mb-2 fw-bold">Dashboard</h4>
            <p class="text-muted">iDorm is here to make it easier to borrow dormitory facilities quickly, practically, and in
                an organized manner.</p>
        </div>
    </div>

    {{-- Bagian Deretan Card Pengumuman --}}
    <div class="d-flex gap-4 justify-content-center flex-wrap">
        @for ($i = 1; $i <= 3; $i++)
            <div class="card shadow-sm" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Judul pengumuman</h5>
                    <p class="card-text">{{ Str::limit('Some quick example text to build on the card title and make up the bulk of the cards content.', 50) }}</p>
                    {{-- Sisi Kanan: Read More --}}
                    <div class="d-flex justify-content-end">
                        <a href="#" class="d-flex text-end align-items-center text-primary text-decoration-none small fw-bold">
                            Read more
                            <svg class="ms-1" style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endfor
    </div>
@endsection
