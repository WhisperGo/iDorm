<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>iDorm</title>

    <link rel="shortcut icon" href="{{ asset('hopeui/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/hope-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/customizer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/rtl.min.css') }}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* 1. SIDEBAR PALING DEPAN */
        /* Kita paksa Sidebar punya z-index tertinggi */
        aside.sidebar {
            z-index: 1060 !important;
            /* Di atas Navbar (1050) & Modal */
        }

        /* 2. NAVBAR DI BELAKANG SIDEBAR TAPI DI DEPAN KONTEN */
        /* Ini style default untuk navbar sticky kita */
        .navbar-sticky {
            position: sticky;
            top: 0;
            z-index: 1050;
            /* Di bawah Sidebar (1060), di atas Konten (1) */
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #eee;
        }

        .iq-top-navbar {
            z-index: 1040 !important;
            /* Naikkan jadi 1040 agar setara sidebar */
        }

        /* 2. Pastikan Sidebar tetap di bawah Modal Backdrop (1050) */
        .sidebar,
        .iq-sidebar {
            z-index: 1041 !important;
            /* Sidebar sedikit di atas navbar agar shadow-nya terlihat bagus */
        }

        /* 3. KHUSUS UNTUK MODAL (PENTING) */
        /* Saat modal terbuka, paksa navbar & sidebar turun kelas */
        body.modal-open .iq-top-navbar,
        body.modal-open .sidebar,
        body.modal-open .iq-sidebar {
            z-index: 1000 !important;
            /* Turunkan drastis saat ada modal */
        }

        /* 3. CHATBOT UI CUSTOMIZATION */
        #chat-box::-webkit-scrollbar {
            width: 5px;
        }

        #chat-box::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .bot-msg {
            background: #ffffff;
            color: #333;
            border-radius: 15px 15px 15px 0;
            border: 1px solid #e2e8f0;
        }

        .user-msg {
            background: #3a57e8;
            color: #ffffff;
            border-radius: 15px 15px 0 15px;
        }

        #chat-toggle {
            transition: transform 0.3s ease;
        }

        #chat-toggle:hover {
            transform: scale(1.1);
        }

        ::-ms-reveal {
            display: none;
        }

        /* Opsional: Menyembunyikan tombol 'clear' bawaan (tanda silang) */
        ::-ms-clear {
            display: none;
        }
    </style>

    <script>
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme) {
            document.getElementById('main-html').setAttribute('data-bs-theme', storedTheme);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.getElementById('main-html').setAttribute('data-bs-theme', 'dark');
        }
    </script>
    @yield('styles')
</head>

@push('scripts')

<body class="">
    {{-- <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div> --}}


    @include('layouts.partials.sidebar')

    <main class="main-content">

        @include('layouts.partials.navbar')

        <div class="position-relative">
            <div class="iq-banner">
                @include('layouts.partials.banner')
            </div>

            <div class="container-fluid content-inner mt-5 py-0">
                @yield('content')
            </div>

            @include('layouts.partials.footer')
        </div>

    </main>

    {{-- Tombol Chat Melayang --}}
    <button id="chat-toggle"
        class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        style="position: fixed; bottom: 25px; right: 30px; width: 65px; height: 65px; z-index: 2000; border: 3px solid #fff;">
        <i class="bi bi-robot fs-2 text-white"></i>
    </button>

    {{-- Container Kotak Chat --}}
    <div id="chat-container" class="card shadow-lg d-none animate__animated animate__fadeInUp"
        style="position: fixed; bottom: 70px; right: 30px; width: 380px; height: 500px; z-index: 2000; border-radius: 20px; border: none; overflow: hidden; display: flex; flex-direction: column;">

        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;">
                    <i class="bi bi-robot text-primary fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white">iDorm AI Assistant</h6>
                    <small style="font-size: 0.7rem; opacity: 0.9;">Online | Ready to help</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                {{-- TOMBOL INI HARUS ADA --}}
                <button type="button" class="btn btn-sm btn-link text-white me-2 p-0" id="clear-chat"
                    title="Bersihkan Chat">
                    <i class="bi bi-trash3 fs-5"></i>
                </button>
                <button type="button" class="btn-close btn-close-white" id="close-chat"></button>
            </div>
        </div>

        {{-- Body Chat --}}
        <div id="chat-box" class="card-body overflow-auto p-3 flex-grow-1" style="background: #f4f7fa;">
            <div class="d-flex mb-3">
                <div class="bot-msg p-3 shadow-sm small border" style="max-width: 85%;">
                    Halo! Saya iDorm AI. Saya bisa bantu kamu cek jadwal, lapor kerusakan, atau booking fasilitas. Ada
                    yang ingin kamu tanyakan?
                </div>
            </div>
        </div>

        {{-- Input Footer --}}
        <div class="card-footer bg-white p-3 border-top">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control border-0 bg-light p-2"
                    placeholder="Ketik pesan kamu..." style="border-radius: 12px 0 0 12px; font-size: 0.9rem;">
                <button class="btn btn-primary px-3" id="send-btn" style="border-radius: 0 12px 12px 0;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('hopeui/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hopeui/js/core/external.min.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/widgetcharts.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/dashboard.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/fslightbox.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/setting.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/slider-tabs.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/form-wizard.js') }}"></script>
    <script src="{{ asset('hopeui/vendor/aos/dist/aos.js') }}"></script>
    <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>


    {{-- Script Chatbot & Custom Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Elemen
            const chatToggle = document.getElementById('chat-toggle');
            const chatContainer = document.getElementById('chat-container');
            const closeChat = document.getElementById('close-chat');
            const sendBtn = document.getElementById('send-btn');
            const userInput = document.getElementById('user-input');
            const chatBox = document.getElementById('chat-box');
            const clearChatBtn = document.getElementById('clear-chat');

            // --- 2. FUNGSI LOGIKA UI ---

            function appendMessageToUI(sender, text, isHtml = false) {
                const msgDiv = document.createElement('div');
                msgDiv.className = sender === 'user' ? 'd-flex justify-content-end mb-3' : 'd-flex mb-3';
                const innerDiv = document.createElement('div');
                innerDiv.className = sender === 'user' ? 'user-msg p-3 shadow-sm small' :
                    'bot-msg p-3 shadow-sm small';
                innerDiv.style.maxWidth = '85%';

                if (isHtml) innerDiv.innerHTML = text;
                else innerDiv.textContent = text;

                msgDiv.appendChild(innerDiv);
                chatBox.appendChild(msgDiv);
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            function saveChatHistory(sender, text, isHtml = false) {
                const history = JSON.parse(localStorage.getItem('idorm_chat_history')) || [];
                history.push({
                    sender,
                    text,
                    isHtml
                });
                if (history.length > 20) history.shift();
                localStorage.setItem('idorm_chat_history', JSON.stringify(history));
            }

            function loadChatHistory() {
                const history = JSON.parse(localStorage.getItem('idorm_chat_history')) || [];
                chatBox.innerHTML = '';
                if (history.length > 0) {
                    history.forEach(msg => appendMessageToUI(msg.sender, msg.text, msg.isHtml));
                } else {
                    appendMessageToUI('bot', 'Hello! I am iDorm AI. How can I help you today?');
                }
            }

            function appendTypingIndicator(id) {
                const typingDiv = document.createElement('div');
                typingDiv.id = id;
                typingDiv.className = 'd-flex mb-3';
                typingDiv.innerHTML =
                    `<div class="bot-msg p-2 px-3 shadow-sm small border-0"><i>AI is typing...</i></div>`;
                chatBox.appendChild(typingDiv);
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            // --- 3. FUNGSI KIRIM PESAN & HANDLE RESPONSE ---

            async function sendMessage() {
                const message = userInput.value.trim();
                if (!message) return;

                appendMessageToUI('user', message);
                saveChatHistory('user', message);
                userInput.value = '';

                const typingId = 'typing-' + Date.now();
                appendTypingIndicator(typingId);

                try {
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
                    const typingElem = document.getElementById(typingId);
                    if (typingElem) typingElem.remove();

                    if (result.status === 'success' && result.data.bot_reply) {
                        let botReply = result.data.bot_reply;
                        let intent = result.data.intent;
                        let actionHtml = '';

                        // A. LOGIKA JIKA INTENT = BOOKING REQUEST
                        if (intent === 'booking_request') {
                            const ent = result.data.entities;
                            const room = ent.room ? ent.room.toLowerCase() : '';
                            const bookingUrl =
                                `{{ route('booking.chatbot') }}?room=${ent.room}&date=${ent.date}&start=${ent.start_time}&end=${ent.end_time}`;

                            const needsSelection = ['dapur', 'kitchen', 'mesin cuci', 'laundry', 'sergun',
                                'multi-purpose', 'serba guna'
                            ];

                            let isSpecial = needsSelection.some(item => room.includes(item));
                            let instruction = isSpecial ?
                                "Schedule secured! Click below to <b>select tools/units</b> and finalize." :
                                "Schedule secured! Click below to <b>finalize</b> your booking.";

                            let buttonText = isSpecial ? "Select Tools & Confirm" : "Confirm Booking";

                            actionHtml = `
                        <div class="mt-3 p-2 border-top">
                            <p class="small mb-2 text-muted"><b>Final Step:</b> ${instruction}</p>
                            <a href="${bookingUrl}" class="btn btn-success btn-sm w-100 rounded-pill fw-bold text-white shadow-sm">
                                <i class="bi bi-calendar-check me-1"></i> ${buttonText}
                            </a>
                        </div>
                    `;
                            appendMessageToUI('bot', botReply + actionHtml, true);
                            saveChatHistory('bot', botReply + actionHtml, true);
                        }

                        // B. LOGIKA JIKA INTENT = CHECK AVAILABILITY
                        else if (intent === 'check_availability') {
                            if (result.data.available) {
                                const ent = result.data.entities;
                                const bookingUrl =
                                    `{{ route('booking.chatbot') }}?room=${ent.room}&date=${ent.date}&start=${ent.start_time}&end=${ent.end_time}`;

                                actionHtml = `
                            <div class="mt-3 p-2 border-top">
                                <p class="small mb-2 text-primary"><b>Interested?</b> Secure this slot before someone else does!</p>
                                <a href="${bookingUrl}" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold text-white shadow-sm">
                                    <i class="bi bi-plus-circle me-1"></i> Book This Slot Now
                                </a>
                            </div>
                        `;
                            }
                            appendMessageToUI('bot', botReply + actionHtml, true);
                            saveChatHistory('bot', botReply + actionHtml, true);
                        }

                        // C. BALASAN BIASA (CHAT UMUM)
                        else {
                            appendMessageToUI('bot', botReply);
                            saveChatHistory('bot', botReply);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    const typingElem = document.getElementById(typingId);
                    if (typingElem) typingElem.remove();
                    appendMessageToUI('bot',
                        'Sorry, I am having trouble connecting to my brain. Please try again.');
                }
            }

            // --- 4. EVENT LISTENERS ---

            loadChatHistory();

            chatToggle.addEventListener('click', () => {
                chatContainer.classList.toggle('d-none');
                chatBox.scrollTop = chatBox.scrollHeight;
                userInput.focus();
            });

            closeChat.addEventListener('click', () => chatContainer.classList.add('d-none'));

            clearChatBtn.addEventListener('click', () => {
                if (confirm('Delete all chat history?')) {
                    localStorage.removeItem('idorm_chat_history');
                    loadChatHistory();
                }
            });

            sendBtn.addEventListener('click', sendMessage);
            userInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendMessage();
            });
        });
    </script>
    @stack('scripts')
</body>

{{-- <script>
        document.querySelectorAll('.btn-freeze').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.freeze-form');
                const status = this.getAttribute('title'); // Mengambil teks dari title

                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: `Apakah Anda yakin ingin melakukan "${status}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal',
                    background: '#fff',
                    customClass: {
                        popup: 'format-swal'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit form jika user klik 'Ya'
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const maxLimit = 2; // Batas maksimal
            const checkboxes = document.querySelectorAll('.machine-checkbox');
            const alertBox = document.getElementById('max-selection-alert');

            // Fungsi untuk update tampilan (Solid vs Outline)
            function updateVisuals() {
                checkboxes.forEach(box => {
                    // Cari label temannya
                    const label = document.querySelector(`label[for="${box.id}"]`);

                    // Logika Ganti Warna (FORCE SWAP CLASS)
                    if (box.checked) {
                        // Kalau dicentang: Jadi Solid Biru
                        label.classList.remove('btn-outline-primary');
                        label.classList.add('btn-primary');
                    } else {
                        // Kalau tidak dicentang: Jadi Garis Biru (Outline)
                        label.classList.remove('btn-primary');
                        label.classList.add('btn-outline-primary');
                    }
                });

                // Logika Disable jika sudah 2
                const checkedCount = document.querySelectorAll('.machine-checkbox:checked').length;

                if (checkedCount >= maxLimit) {
                    checkboxes.forEach(box => {
                        if (!box.checked) {
                            box.disabled = true;
                            // Bikin labelnya agak transparan biar kelihatan disabled
                            const label = document.querySelector(`label[for="${box.id}"]`);
                            label.classList.add('opacity-50');
                        }
                    });
                } else {
                    checkboxes.forEach(box => {
                        box.disabled = false;
                        const label = document.querySelector(`label[for="${box.id}"]`);
                        label.classList.remove('opacity-50');
                    });
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // 1. Cek Limit dulu sebelum update visual
                    const checkedCount = document.querySelectorAll('.machine-checkbox:checked')
                        .length;

                    if (checkedCount > maxLimit) {
                        this.checked = false; // Batalkan centangan terakhir

                        // Tampilkan alert
                        alertBox.classList.remove('d-none');
                        setTimeout(() => {
                            alertBox.classList.add('d-none');
                        }, 3000);
                    }

                    // 2. Update Tampilan (Warna & Disable state)
                    updateVisuals();

                    // 3. Hilangkan focus agar outline biru bawaan browser hilang
                    this.blur();
                });
            });

            // Jalankan sekali saat load (untuk handle old input kalau ada error validasi sebelumnya)
            updateVisuals();
        });
    </script> --}}

</html>