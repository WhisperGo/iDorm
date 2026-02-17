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

    {{-- 1. Tombol Chat Melayang --}}
    <button id="chat-toggle"
        class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        style="position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; z-index: 1050; border: 3px solid #fff;">
        <i class="bi bi-robot fs-2"></i>
    </button>

    {{-- 2. Container Kotak Chat --}}
    <div id="chat-container" class="card shadow-lg d-none animate__animated animate__fadeInUp"
        style="position: fixed; bottom: 110px; right: 30px; width: 380px; max-height: 550px; z-index: 1050; border-radius: 20px; border: none; overflow: hidden;">

        {{-- Header Chat --}}
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                    style="width: 35px; height: 35px;">
                    <i class="bi bi-robot text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">iDorm AI Assistant</h6>
                    <small style="font-size: 0.7rem; opacity: 0.8;">Online | Ready to help</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" id="close-chat"></button>
        </div>

        {{-- Body Chat (Tempat pesan muncul) --}}
        <div id="chat-box" class="card-body overflow-auto p-3" style="height: 350px; background: #f0f2f5;">
            <div class="d-flex mb-3">
                <div class="bg-white p-2 rounded-3 shadow-sm small border" style="max-width: 80%;">
                    Halo! Saya iDorm AI. Kamu bisa tanya jadwal fasilitas, lapor kerusakan, atau minta bantuan booking. Ada
                    yang bisa dibantu?
                </div>
            </div>
        </div>

        {{-- Footer Chat (Input) --}}
        <div class="card-footer bg-white p-3 border-top">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control border-0 bg-light"
                    placeholder="Tulis pesan kamu..." style="border-radius: 10px 0 0 10px;">
                <button class="btn btn-primary" id="send-btn" style="border-radius: 0 10px 10px 0;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- 3. Script Logika Chatbot --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatToggle = document.getElementById('chat-toggle');
            const chatContainer = document.getElementById('chat-container');
            const closeChat = document.getElementById('close-chat');
            const sendBtn = document.getElementById('send-btn');
            const userInput = document.getElementById('user-input');
            const chatBox = document.getElementById('chat-box');

            // Buka/Tutup Chat
            chatToggle.addEventListener('click', () => {
                chatContainer.classList.toggle('d-none');
                userInput.focus();
            });

            closeChat.addEventListener('click', () => {
                chatContainer.classList.add('d-none');
            });

            // Kirim Pesan saat Klik Tombol atau Tekan Enter
            sendBtn.addEventListener('click', sendMessage);
            userInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendMessage();
            });

            async function sendMessage() {
                const message = userInput.value.trim();
                if (!message) return;

                // 1. Tampilkan pesan user ke layar
                appendMessage('user', message);
                userInput.value = '';

                // 2. Tampilkan indikator "Typing..."
                const typingId = 'typing-' + Date.now();
                appendTypingIndicator(typingId);

                try {
                    // 3. Kirim ke Laravel Controller
                    const response = await fetch("{{ route('chatbot.send') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message: message
                        })
                    });

                    const result = await response.json();

                    // Hapus indikator typing
                    document.getElementById(typingId).remove();

                    // 4. Tampilkan balasan bot
                    if (result.status === 'success' || result.status === 'incomplete' || result.status ===
                        'error') {
                        appendMessage('bot', result.data.bot_reply);
                    } else {
                        appendMessage('bot', 'Maaf, sistem sedang sibuk.');
                    }

                } catch (error) {
                    if (document.getElementById(typingId)) document.getElementById(typingId).remove();
                    appendMessage('bot', 'Gagal terhubung ke server AI. Pastikan server Python aktif!');
                    console.error(error);
                }
            }

            function appendMessage(sender, text) {
                const msgDiv = document.createElement('div');
                msgDiv.className = sender === 'user' ? 'd-flex justify-content-end mb-3' : 'd-flex mb-3';

                const innerDiv = document.createElement('div');
                innerDiv.className = sender === 'user' ?
                    'bg-primary text-white p-2 rounded-3 shadow-sm small' :
                    'bg-white text-dark p-2 rounded-3 shadow-sm small border';
                innerDiv.style.maxWidth = '80%';
                innerDiv.innerHTML = text;

                msgDiv.appendChild(innerDiv);
                chatBox.appendChild(msgDiv);
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            function appendTypingIndicator(id) {
                const typingDiv = document.createElement('div');
                typingDiv.id = id;
                typingDiv.className = 'd-flex mb-3';
                typingDiv.innerHTML =
                    `<div class="bg-white p-2 rounded-3 shadow-sm small border"><i>AI sedang mengetik...</i></div>`;
                chatBox.appendChild(typingDiv);
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
    </script>

    {{-- Style Tambahan untuk Animasi Chat (Opsional) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endsection
