@extends('layouts.guest')

@section('title', 'Housekeeping & Bantuan')

@push('styles')
    <style>
        .guest-request-page {
            --request-accent: #dfa974;
            --request-accent-dark: #bd8044;
            --request-ink: #1f2328;
            --request-muted: #73777f;
            --request-border: #ece8e2;
            min-height: 80vh;
            padding: 62px 0 90px;
            background: #f7f6f3;
        }

        .request-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            padding: 30px 34px;
            border-radius: 22px;
            background: linear-gradient(125deg, #1e2428 0%, #30383d 62%, #4c4338 100%);
            box-shadow: 0 18px 38px rgba(26, 29, 31, .14);
            color: #fff;
        }

        .request-hero::after {
            position: absolute;
            top: -90px;
            right: -45px;
            width: 250px;
            height: 250px;
            border: 1px solid rgba(223, 169, 116, .22);
            border-radius: 50%;
            box-shadow: 0 0 0 35px rgba(223, 169, 116, .04), 0 0 0 70px rgba(223, 169, 116, .025);
            content: '';
        }

        .request-hero-content { position: relative; z-index: 1; }
        .request-room-pill { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 999px; background: rgba(255, 255, 255, .09); color: #f2cfaa; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .request-hero h1 { margin: 13px 0 7px; color: #fff; font-size: 34px; }
        .request-hero p { margin: 0; color: rgba(255, 255, 255, .68); }
        .request-hero-link { display: inline-flex; align-items: center; gap: 7px; padding: 10px 14px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 11px; background: rgba(255, 255, 255, .08); color: #fff; font-size: 13px; font-weight: 600; transition: .2s ease; }
        .request-hero-link:hover { border-color: #fff; background: #fff; color: #272e32; }

        .request-section-intro { margin-bottom: 20px; }
        .request-section-intro span { display: block; margin-bottom: 5px; color: #ad7136; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .request-section-intro h2 { margin: 0 0 5px; color: var(--request-ink); font-size: 25px; }
        .request-section-intro p { margin: 0; color: var(--request-muted); }
        .request-choice { position: relative; width: 100%; height: 100%; min-height: 165px; padding: 20px; overflow: hidden; border: 1px solid var(--request-border); border-radius: 17px; background: #fff; color: inherit; cursor: pointer; text-align: left; box-shadow: 0 8px 24px rgba(34, 34, 34, .045); transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .request-choice:hover { transform: translateY(-3px); border-color: #dfc3a4; box-shadow: 0 14px 28px rgba(34, 34, 34, .085); }
        .request-choice.is-selected { border-color: var(--request-accent); background: linear-gradient(145deg, #fff, #fffaf4); box-shadow: 0 12px 28px rgba(190, 132, 71, .13); }
        .request-choice-icon { display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; margin-bottom: 15px; border-radius: 13px; background: #fff2e4; color: #b67639; font-size: 20px; transition: .2s ease; }
        .request-choice.is-selected .request-choice-icon { background: var(--request-accent); color: #fff; }
        .request-choice strong { display: block; margin-bottom: 5px; color: var(--request-ink); font-size: 15px; line-height: 1.35; }
        .request-choice small { display: block; color: #817d77; font-size: 11px; line-height: 1.55; }
        .request-choice-check { position: absolute; top: 13px; right: 13px; display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--request-accent); color: #fff; opacity: 0; transform: scale(.7); transition: .2s ease; }
        .request-choice.is-selected .request-choice-check { opacity: 1; transform: scale(1); }

        .request-form-panel { position: sticky; top: 24px; overflow: hidden; border: 1px solid var(--request-border); border-radius: 20px; background: #fff; box-shadow: 0 14px 38px rgba(32, 34, 35, .09); }
        .request-form-header { display: flex; align-items: center; gap: 11px; padding: 20px 22px; border-bottom: 1px solid #eeeae5; }
        .request-form-header-icon { display: flex; align-items: center; justify-content: center; width: 41px; height: 41px; border-radius: 12px; background: #fff2e4; color: #b67639; font-size: 18px; }
        .request-form-header h4 { margin: 0; color: var(--request-ink); font-size: 19px; }
        .request-form-empty { padding: 50px 24px; color: #918c86; text-align: center; }
        .request-form-empty i { display: flex; align-items: center; justify-content: center; width: 65px; height: 65px; margin: 0 auto 14px; border-radius: 19px; background: #f5f2ed; color: #c2a98e; font-size: 27px; }
        .request-form-empty strong { display: block; margin-bottom: 5px; color: #58544f; }
        .request-form-body { padding: 21px 22px 23px; }
        .selected-request-summary { display: flex; align-items: center; gap: 11px; padding: 13px; margin-bottom: 18px; border-radius: 13px; background: #f7f4ef; }
        .selected-request-icon { display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 11px; background: #fff; color: #b67639; font-size: 16px; }
        .selected-request-summary small { display: block; margin-bottom: 2px; color: #918a82; font-size: 9px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
        .selected-request-summary strong { display: block; color: var(--request-ink); font-size: 13px; line-height: 1.4; }
        .request-field { margin-bottom: 16px; }
        .request-label { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; color: #514e4a; font-size: 12px; font-weight: 700; }
        .request-input { width: 100%; min-height: 43px; padding: 9px 11px; border: 1px solid #e4dcd3; border-radius: 10px; background: #fff; color: #45423f; font-size: 12px; outline: 0; }
        textarea.request-input { min-height: 98px; line-height: 1.55; resize: vertical; }
        .request-input:focus { border-color: var(--request-accent); box-shadow: 0 0 0 3px rgba(223, 169, 116, .11); }
        .priority-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .priority-option input { position: absolute; opacity: 0; pointer-events: none; }
        .priority-option label { display: block; margin: 0; padding: 10px; border: 1px solid #e7e0d8; border-radius: 10px; background: #fff; color: #6e6963; cursor: pointer; font-size: 11px; font-weight: 700; text-align: center; transition: .17s ease; }
        .priority-option label i { margin-right: 4px; }
        .priority-option input:checked + label { border-color: var(--priority-color); background: color-mix(in srgb, var(--priority-color) 9%, white); color: var(--priority-color); box-shadow: 0 0 0 2px color-mix(in srgb, var(--priority-color) 12%, transparent); }
        .priority-hint { display: block; margin-top: 7px; color: #98918a; font-size: 10px; line-height: 1.45; }
        .submit-request-button { width: 100%; min-height: 48px; border: 0; border-radius: 12px; background: var(--request-accent); color: #fff; cursor: pointer; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; transition: .18s ease; }
        .submit-request-button:hover { background: var(--request-accent-dark); transform: translateY(-1px); }
        .request-response-note { margin: 11px 0 0; color: #8d8882; font-size: 10px; line-height: 1.5; text-align: center; }

        .request-history { margin-top: 36px; overflow: hidden; border: 1px solid var(--request-border); border-radius: 20px; background: #fff; box-shadow: 0 9px 30px rgba(32, 34, 35, .055); }
        .request-history-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 23px; border-bottom: 1px solid #eeeae5; }
        .request-history-header span { display: block; margin-bottom: 3px; color: #ad7136; font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .request-history-header h3 { margin: 0; color: var(--request-ink); font-size: 21px; }
        .history-count { padding: 6px 10px; border-radius: 999px; background: #f3efe9; color: #777169; font-size: 11px; font-weight: 700; }
        .request-history-list { padding: 2px 22px; }
        .request-history-item { display: flex; align-items: center; gap: 14px; padding: 16px 0; border-bottom: 1px solid #f0ede8; color: inherit; transition: .17s ease; }
        .request-history-item:last-child { border-bottom: 0; }
        .request-history-item:hover { padding-right: 5px; padding-left: 5px; color: inherit; }
        .history-icon { display: flex; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 13px; background: #f5f1ec; color: #b37840; font-size: 17px; flex-shrink: 0; }
        .history-detail { min-width: 0; flex: 1; }
        .history-detail strong { display: block; margin-bottom: 4px; overflow: hidden; color: var(--request-ink); font-size: 13px; text-overflow: ellipsis; white-space: nowrap; }
        .history-detail small { display: block; color: #918b84; font-size: 10px; }
        .history-status { flex-shrink: 0; }
        .history-chevron { color: #aaa39b; }
        .request-history-empty { padding: 45px 20px; color: #928d87; text-align: center; }
        .request-history-empty i { display: block; margin-bottom: 10px; color: #c2a98e; font-size: 30px; }
        .request-history-footer { padding: 14px 22px; border-top: 1px solid #eeeae5; }
        .mobile-request-bar { display: none; }

        @media (max-width: 991.98px) {
            .request-form-panel { position: static; margin-top: 12px; }
            .mobile-request-bar { position: fixed; right: 12px; bottom: 12px; left: 12px; z-index: 99; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid rgba(255, 255, 255, .12); border-radius: 14px; background: #262d31; box-shadow: 0 12px 30px rgba(22, 26, 28, .3); color: #fff; transform: translateY(130%); transition: .25s ease; }
            .mobile-request-bar.show { transform: translateY(0); }
            .mobile-request-bar strong { display: block; max-width: 195px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .mobile-request-bar small { display: block; color: rgba(255, 255, 255, .63); }
            .mobile-request-bar button { flex: 0 0 auto; padding: 9px 12px; border: 0; border-radius: 9px; background: var(--request-accent); color: #fff; font-size: 11px; font-weight: 800; }
        }

        @media (max-width: 575.98px) {
            .guest-request-page { padding-top: 38px; }
            .request-hero { padding: 24px 20px; border-radius: 17px; }
            .request-hero h1 { font-size: 27px; }
            .request-choice { min-height: 155px; padding: 17px; }
            .request-history-item { align-items: flex-start; }
            .history-status { margin-left: auto; }
        }
    </style>
@endpush

@section('content')
    @php
        $requestOptions = [
            ['housekeeping', 'Pembersihan Kamar', 'Mohon kamar dibersihkan dan dirapikan.', 'fa-bed'],
            ['amenity', 'Handuk Bersih', 'Minta penggantian atau tambahan handuk.', 'fa-shopping-basket'],
            ['amenity', 'Perlengkapan Mandi', 'Sabun, sampo, sikat gigi, atau toiletries.', 'fa-tint'],
            ['amenity', 'Air Minum', 'Minta tambahan air mineral ke kamar.', 'fa-glass'],
            ['housekeeping', 'Ganti Sprei', 'Penggantian sprei atau sarung bantal.', 'fa-refresh'],
            ['assistance', 'Bantuan Receptionist', 'Butuh bantuan atau informasi dari petugas.', 'fa-comments'],
            ['other', 'Lapor Masalah Kamar', 'Laporkan fasilitas kamar yang bermasalah.', 'fa-wrench'],
            ['other', 'Kebutuhan Lain', 'Sampaikan kebutuhan lain selama menginap.', 'fa-bell'],
        ];
        $typeIcons = ['housekeeping' => 'fa-bed', 'amenity' => 'fa-shopping-basket', 'assistance' => 'fa-comments', 'other' => 'fa-bell'];
    @endphp

    <section class="guest-request-page">
        <div class="container">
            <div class="request-hero">
                <div class="request-hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <span class="request-room-pill"><i class="fa fa-bed"></i>Kamar {{ $access->room->room_number }}</span>
                        <h1>Housekeeping &amp; Bantuan</h1>
                        <p>Sampaikan kebutuhan kamar Anda, tim kami siap membantu selama masa menginap.</p>
                    </div>
                    <a href="{{ route('room-service.home') }}" class="request-hero-link mt-3 mt-md-0"><i class="fa fa-th-large"></i>Portal Kamar</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong><i class="fa fa-exclamation-circle mr-1"></i>Permintaan belum dapat dikirim.</strong>
                    <ul class="mb-0 mt-2 pl-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="request-section-intro"><span>Layanan cepat</span><h2>Apa yang Anda butuhkan?</h2><p>Pilih salah satu kebutuhan, lalu lengkapi detailnya sebelum dikirim.</p></div>
                    <div class="row">
                        @foreach ($requestOptions as [$type, $title, $description, $icon])
                            @php
                                $isInitiallySelected = old('title') === $title;
                            @endphp
                            <div class="col-sm-6 col-xl-4 mb-3">
                                <button
                                    type="button"
                                    class="request-choice {{ $isInitiallySelected ? 'is-selected' : '' }}"
                                    data-request-option
                                    data-request-type="{{ $type }}"
                                    data-request-title="{{ $title }}"
                                    data-request-description="{{ $description }}"
                                    data-request-icon="{{ $icon }}"
                                >
                                    <span class="request-choice-icon"><i class="fa {{ $icon }}"></i></span>
                                    <strong>{{ $title }}</strong>
                                    <small>{{ $description }}</small>
                                    <span class="request-choice-check"><i class="fa fa-check"></i></span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside id="request-form-panel" class="request-form-panel">
                        <div class="request-form-header"><span class="request-form-header-icon"><i class="fa fa-paper-plane"></i></span><div><h4>Detail Permintaan</h4><small class="text-muted">Untuk Kamar {{ $access->room->room_number }}</small></div></div>
                        <div id="request-form-empty" class="request-form-empty"><i class="fa fa-hand-pointer-o"></i><strong>Pilih kebutuhan terlebih dahulu</strong><span>Tekan salah satu pilihan agar formulir dapat dilengkapi.</span></div>

                        <form
                            id="guest-request-form"
                            method="POST"
                            action="{{ route('room-service.requests.store') }}"
                            data-initial-type="{{ old('type') }}"
                            data-initial-title="{{ old('title') }}"
                            data-confirm="Permintaan akan langsung masuk ke antrean Receptionist."
                            data-confirm-title="Kirim permintaan?"
                            data-confirm-button="Ya, Kirim Permintaan"
                            hidden
                        >
                            @csrf
                            <input id="request-type" type="hidden" name="type" value="{{ old('type') }}">
                            <div class="request-form-body">
                                <div class="selected-request-summary"><span class="selected-request-icon"><i id="selected-request-icon" class="fa fa-bell"></i></span><div><small>Permintaan terpilih</small><strong id="selected-request-name">-</strong></div></div>
                                <div class="request-field">
                                    <label class="request-label" for="request-title">Judul kebutuhan <span class="text-danger">Wajib</span></label>
                                    <input id="request-title" name="title" value="{{ old('title') }}" maxlength="255" class="request-input" required>
                                </div>
                                <div class="request-field">
                                    <label class="request-label" for="request-description">Detail kebutuhan <span class="text-muted">Opsional</span></label>
                                    <textarea id="request-description" name="description" maxlength="2000" class="request-input" placeholder="Jelaskan jumlah, waktu, lokasi, atau kebutuhan khusus lainnya">{{ old('description') }}</textarea>
                                </div>
                                <div class="request-field">
                                    <span class="request-label">Tingkat prioritas</span>
                                    <div class="priority-options">
                                        @foreach ([['low', 'Rendah', 'fa-angle-down', '#7d8a9f'], ['normal', 'Normal', 'fa-minus', '#4e8f7d'], ['high', 'Tinggi', 'fa-angle-up', '#d68a25'], ['urgent', 'Mendesak', 'fa-exclamation', '#d45b4d']] as [$value, $label, $icon, $color])
                                            <div class="priority-option" style="--priority-color:{{ $color }}"><input id="priority-{{ $value }}" type="radio" name="priority" value="{{ $value }}" @checked(old('priority', 'normal') === $value)><label for="priority-{{ $value }}"><i class="fa {{ $icon }}"></i>{{ $label }}</label></div>
                                        @endforeach
                                    </div>
                                    <small class="priority-hint"><i class="fa fa-info-circle mr-1"></i>Gunakan prioritas Mendesak hanya untuk kebutuhan yang harus segera ditangani.</small>
                                </div>
                                <button type="submit" class="submit-request-button"><i class="fa fa-paper-plane mr-1"></i>Kirim Permintaan</button>
                                <p class="request-response-note"><i class="fa fa-shield mr-1"></i>Status permintaan dapat dipantau melalui riwayat di bawah halaman ini.</p>
                            </div>
                        </form>
                    </aside>
                </div>
            </div>

            <section class="request-history">
                <div class="request-history-header"><div><span>Pantau progres</span><h3>Riwayat Permintaan</h3></div><span class="history-count">{{ $requests->total() }} permintaan</span></div>
                @if ($requests->isNotEmpty())
                    <div class="request-history-list">
                        @foreach ($requests as $item)
                            <a href="{{ route('room-service.requests.show', $item) }}" class="request-history-item">
                                <span class="history-icon"><i class="fa {{ $typeIcons[$item->type] ?? 'fa-bell' }}"></i></span>
                                <div class="history-detail"><strong>{{ $item->title }}</strong><small>{{ $item->request_code }} &middot; {{ $item->requested_at->diffForHumans() }} &middot; Prioritas {{ str($item->priority)->title() }}</small></div>
                                <span class="badge badge-{{ $item->status->badgeClass() }} history-status">{{ $item->status->label() }}</span>
                                <i class="fa fa-chevron-right history-chevron"></i>
                            </a>
                        @endforeach
                    </div>
                    @if ($requests->hasPages())<div class="request-history-footer">{{ $requests->links() }}</div>@endif
                @else
                    <div class="request-history-empty"><i class="fa fa-check-circle-o"></i><strong>Belum ada permintaan</strong><p class="mb-0 mt-1">Kebutuhan yang dikirim akan muncul dan dapat dipantau di sini.</p></div>
                @endif
            </section>

            <div id="mobile-request-bar" class="mobile-request-bar">
                <div><strong id="mobile-request-name">Permintaan</strong><small>Lengkapi detail sebelum dikirim</small></div>
                <button id="continue-request-button" type="button"><i class="fa fa-pencil mr-1"></i>Lanjut</button>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const options = Array.from(document.querySelectorAll('[data-request-option]'));
            const form = document.getElementById('guest-request-form');
            const emptyState = document.getElementById('request-form-empty');
            const typeInput = document.getElementById('request-type');
            const titleInput = document.getElementById('request-title');
            const descriptionInput = document.getElementById('request-description');
            const selectedName = document.getElementById('selected-request-name');
            const selectedIcon = document.getElementById('selected-request-icon');
            const mobileBar = document.getElementById('mobile-request-bar');
            const mobileName = document.getElementById('mobile-request-name');
            let selectedOption = null;

            function selectRequest(option, preserveValues) {
                selectedOption = option;
                options.forEach((item) => item.classList.toggle('is-selected', item === option));
                typeInput.value = option.dataset.requestType;
                if (!preserveValues) {
                    titleInput.value = option.dataset.requestTitle;
                    descriptionInput.value = '';
                }
                selectedName.textContent = option.dataset.requestTitle;
                selectedIcon.className = 'fa ' + option.dataset.requestIcon;
                emptyState.style.display = 'none';
                form.hidden = false;
                mobileName.textContent = option.dataset.requestTitle;
                mobileBar.classList.add('show');
            }

            options.forEach((option) => option.addEventListener('click', () => selectRequest(option, selectedOption === option)));
            document.getElementById('continue-request-button').addEventListener('click', function () {
                document.getElementById('request-form-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => titleInput.focus(), 450);
            });

            const initialOption = options.find((option) => option.dataset.requestTitle === form.dataset.initialTitle)
                || options.find((option) => option.dataset.requestType === form.dataset.initialType);
            if (initialOption) selectRequest(initialOption, true);
        });
    </script>
@endpush
