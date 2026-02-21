@extends('layouts.app')

@section('content')
    {{-- Bagian Header --}}
    <div class="card mb-5">
        <div class="card-body text-center py-4">
            <h4 class="card-title mb-2 fw-bold">iDorm</h4>
            <p class="text-muted">iDorm is here to make it easier to borrow dormitory facilities quickly, practically, and in
                an organized manner.</p>
        </div>
    </div>

    {{-- Bagian Deretan Card Pengumuman --}}
    <div class="row">
        @forelse($announcements as $item)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $item->title }}</h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($item->content, 100, '...') }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('announcements.show', $item->id) }}" class="btn btn-link p-0 fw-bold">
                                Read more <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center">
                    Belum ada pengumuman terbaru saat ini.
                </div>
            </div>
        @endforelse
    </div>
@endsection
