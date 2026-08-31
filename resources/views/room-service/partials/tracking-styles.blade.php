<style>
    .tracking-page { --track-accent:#dfa974; --track-accent-dark:#b97839; --track-ink:#20252a; --track-muted:#7b7f84; --track-border:#ebe7e1; min-height:80vh; padding:62px 0 90px; background:#f7f6f3; }
    .tracking-hero { position:relative; overflow:hidden; margin-bottom:24px; padding:30px 34px; border-radius:22px; background:linear-gradient(125deg,#1e2428 0%,#30383d 62%,#4c4338 100%); box-shadow:0 18px 38px rgba(26,29,31,.14); color:#fff; }
    .tracking-hero::after { position:absolute; top:-95px; right:-45px; width:250px; height:250px; border:1px solid rgba(223,169,116,.22); border-radius:50%; box-shadow:0 0 0 35px rgba(223,169,116,.04),0 0 0 70px rgba(223,169,116,.025); content:''; }
    .tracking-hero-content { position:relative; z-index:1; }
    .tracking-room-pill { display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border:1px solid rgba(255,255,255,.16); border-radius:999px; background:rgba(255,255,255,.09); color:#f2cfaa; font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
    .tracking-hero h1 { margin:13px 0 7px; color:#fff; font-size:34px; }
    .tracking-hero p { max-width:650px; margin:0; color:rgba(255,255,255,.68); }
    .tracking-actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:9px; }
    .tracking-action { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:42px; padding:10px 14px; border:1px solid rgba(255,255,255,.16); border-radius:11px; background:rgba(255,255,255,.08); color:#fff; font-size:12px; font-weight:700; transition:.2s ease; }
    .tracking-action:hover { border-color:#fff; background:#fff; color:#272e32; }
    .tracking-action.is-primary { border-color:var(--track-accent); background:var(--track-accent); }
    .tracking-action.is-primary:hover { border-color:#fff; background:#fff; color:#272e32; }
    .tracking-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:24px; }
    .tracking-stat { display:flex; align-items:center; gap:13px; min-width:0; padding:17px 18px; border:1px solid var(--track-border); border-radius:16px; background:#fff; box-shadow:0 7px 22px rgba(32,34,35,.045); }
    .tracking-stat-icon { display:flex; align-items:center; justify-content:center; width:43px; height:43px; border-radius:13px; background:#fff1e2; color:var(--track-accent-dark); font-size:17px; flex:0 0 auto; }
    .tracking-stat:nth-child(2) .tracking-stat-icon { background:#fff5d9; color:#a97817; }
    .tracking-stat:nth-child(3) .tracking-stat-icon { background:#e9f6ef; color:#428060; }
    .tracking-stat:nth-child(4) .tracking-stat-icon { background:#ecf1f6; color:#557089; }
    .tracking-stat small { display:block; margin-bottom:2px; color:#96918b; font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
    .tracking-stat strong { display:block; overflow:hidden; color:var(--track-ink); font:700 19px/1.2 "Lora",serif; text-overflow:ellipsis; white-space:nowrap; }
    .tracking-toolbar { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:17px; }
    .tracking-toolbar-copy span { display:block; margin-bottom:3px; color:#ad7136; font-size:10px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; }
    .tracking-toolbar-copy h2 { margin:0; color:var(--track-ink); font-size:24px; }
    .tracking-filters { display:flex; gap:7px; max-width:100%; overflow-x:auto; padding:3px 1px 6px; scrollbar-width:thin; }
    .tracking-filter { display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border:1px solid #e5dfd7; border-radius:999px; background:#fff; color:#76716b; font-size:11px; font-weight:700; white-space:nowrap; transition:.17s ease; }
    .tracking-filter:hover,.tracking-filter.is-active { border-color:var(--track-accent); background:#fff8f0; color:#a76528; }
    .tracking-list { display:grid; gap:15px; }
    .tracking-card { overflow:hidden; border:1px solid var(--track-border); border-radius:19px; background:#fff; box-shadow:0 9px 28px rgba(32,34,35,.055); transition:transform .18s ease,box-shadow .18s ease; }
    .tracking-card:hover { transform:translateY(-2px); box-shadow:0 14px 34px rgba(32,34,35,.085); }
    .tracking-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; padding:18px 21px 15px; border-bottom:1px solid #f0ede8; }
    .tracking-order-code { display:flex; align-items:center; gap:11px; min-width:0; }
    .tracking-order-icon { display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:13px; background:#fff1e2; color:#b16f32; font-size:17px; flex:0 0 auto; }
    .tracking-order-code small { display:block; margin-bottom:2px; color:#96908a; font-size:9px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; }
    .tracking-order-code strong { display:block; overflow:hidden; color:var(--track-ink); font-size:14px; text-overflow:ellipsis; white-space:nowrap; }
    .tracking-status { flex:0 0 auto; padding:7px 10px; border-radius:999px; font-size:10px; font-weight:800; letter-spacing:.02em; }
    .tracking-card-content { display:grid; grid-template-columns:minmax(0,1.45fr) minmax(330px,1fr); gap:28px; padding:19px 21px 21px; }
    .tracking-main { min-width:0; }
    .tracking-primary-title { margin:0 0 7px; color:var(--track-ink); font-size:17px; }
    .tracking-meta { display:flex; flex-wrap:wrap; gap:7px 14px; color:#8a857f; font-size:11px; line-height:1.5; }
    .tracking-meta span { display:inline-flex; align-items:center; gap:5px; }
    .tracking-meta i { color:#bd8650; }
    .tracking-item-preview { display:flex; flex-wrap:wrap; gap:7px; margin-top:12px; }
    .tracking-item-chip { padding:7px 9px; border-radius:9px; background:#f6f3ee; color:#665f58; font-size:10px; font-weight:600; }
    .tracking-note { margin-top:12px; padding:10px 11px; border-left:3px solid #ddaf7f; border-radius:0 9px 9px 0; background:#fffaf5; color:#7c7167; font-size:10px; line-height:1.55; }
    .tracking-price-row { display:flex; align-items:flex-end; justify-content:space-between; gap:14px; margin-top:16px; }
    .tracking-price small { display:block; color:#99938c; font-size:9px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .tracking-price strong { display:block; margin-top:2px; color:var(--track-ink); font:700 18px/1.2 "Lora",serif; }
    .tracking-detail-link { display:inline-flex; align-items:center; gap:7px; color:#a7662b; font-size:11px; font-weight:800; }
    .tracking-detail-link:hover { color:#75431a; }
    .order-progress { display:grid; grid-template-columns:repeat(var(--progress-steps),minmax(0,1fr)); align-items:start; padding-top:4px; }
    .progress-step { position:relative; min-width:0; text-align:center; }
    .progress-step:not(:last-child)::after { position:absolute; top:14px; left:calc(50% + 16px); right:calc(-50% + 16px); height:2px; background:#e8e3dd; content:''; }
    .progress-step.is-complete:not(:last-child)::after { background:#79a98f; }
    .progress-dot { position:relative; z-index:1; display:flex; align-items:center; justify-content:center; width:29px; height:29px; margin:0 auto 8px; border:2px solid #ded8d1; border-radius:50%; background:#fff; color:#aaa39b; font-size:10px; }
    .progress-step.is-complete .progress-dot { border-color:#69a182; background:#69a182; color:#fff; }
    .progress-step.is-current .progress-dot { border-color:var(--track-accent); background:var(--track-accent); color:#fff; box-shadow:0 0 0 5px rgba(223,169,116,.14); }
    .progress-step strong { display:block; overflow:hidden; color:#8c8781; font-size:9px; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
    .progress-step.is-complete strong,.progress-step.is-current strong { color:#4f544f; }
    .tracking-cancelled { display:flex; align-items:center; gap:10px; padding:13px 14px; border-radius:11px; background:#fff0ef; color:#a54f48; font-size:11px; line-height:1.5; }
    .tracking-cancelled i { font-size:18px; }
    .tracking-pagination { margin-top:22px; }
    .tracking-empty { padding:58px 25px; border:1px dashed #ddd5cb; border-radius:19px; background:#fff; color:#8c8781; text-align:center; }
    .tracking-empty-icon { display:flex; align-items:center; justify-content:center; width:67px; height:67px; margin:0 auto 14px; border-radius:20px; background:#f5f1eb; color:#c1976c; font-size:27px; }
    .tracking-empty h3 { margin:0 0 7px; color:var(--track-ink); font-size:21px; }
    .tracking-empty p { margin:0 0 18px; }
    @media (max-width:991.98px) { .tracking-summary { grid-template-columns:repeat(2,minmax(0,1fr)); } .tracking-toolbar { align-items:flex-start; flex-direction:column; } .tracking-filters { width:100%; } .tracking-card-content { grid-template-columns:1fr; gap:22px; } }
    @media (max-width:575.98px) { .tracking-page { padding-top:38px; } .tracking-hero { padding:24px 20px; border-radius:17px; } .tracking-hero h1 { font-size:27px; } .tracking-actions { width:100%; margin-top:18px; } .tracking-action { flex:1; } .tracking-summary { gap:9px; } .tracking-stat { gap:9px; padding:13px 11px; } .tracking-stat-icon { width:36px; height:36px; border-radius:10px; font-size:14px; } .tracking-stat strong { font-size:15px; } .tracking-card-top,.tracking-card-content { padding-right:16px; padding-left:16px; } .tracking-card-top { align-items:center; } .tracking-order-code strong { max-width:185px; } .tracking-meta { flex-direction:column; gap:5px; } .progress-step strong { font-size:8px; } .progress-dot { width:26px; height:26px; } .progress-step:not(:last-child)::after { top:12px; } }
</style>
