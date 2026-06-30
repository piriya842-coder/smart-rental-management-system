@extends('layouts.student')

@section('title', 'Contract Agreement • Smart Rental')

@section('content')
@php
    $gold    = '#c92a2a';
    $cream   = '#fffafa';
    $choco   = '#4a2c2a';
    $soft    = '#fdf2f2';
    $redDark = '#a61e1e';

    $contractNo = $contract->contract_no ?? ('CTR-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT));
@endphp

<div class="max-w-5xl mx-auto">
    @if(session('success'))
        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 font-semibold shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-semibold shadow-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
            <div class="font-extrabold mb-2">Please fix the following:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="font-semibold">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-[32px] border border-[rgba(201,42,42,.08)] bg-white/95 shadow-[0_16px_50px_rgba(0,0,0,0.08)]">

        <!-- HEADER -->
        <div class="border-b border-[rgba(201,42,42,.08)] bg-gradient-to-br from-white via-white to-[#fffafa] px-7 py-8 md:px-10 md:py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[rgba(201,42,42,.10)] bg-white/80 px-4 py-2 text-[11px] font-black tracking-[0.2em] uppercase text-black/45 shadow-sm">
                        <span class="inline-block h-2 w-2 rounded-full" style="background: {{ $gold }};"></span>
                        Smart Rental Agreement
                    </div>

                    <h1 class="mt-4 text-4xl md:text-5xl font-extrabold leading-tight tracking-tight" style="color: {{ $choco }};">
                        Contract {{ $contractNo }}
                    </h1>

                    <p class="mt-4 text-[16px] leading-8 text-black/60">
                        Please review this tenancy agreement carefully. Once signed, your acceptance will be securely recorded in the Smart Rental system.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('student.contracts.index') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-[rgba(201,42,42,.10)] bg-white px-6 py-3 font-extrabold text-black/75 shadow-sm hover:bg-[#fff5f5] transition">
                        Back
                    </a>

                    @if($contract->is_signed)
                        <a href="{{ route('student.contracts.pdf', $contract) }}"
                           class="inline-flex items-center justify-center rounded-2xl px-6 py-3 font-extrabold text-white shadow-sm hover:brightness-95 transition"
                           style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="border-b border-[rgba(201,42,42,.08)] bg-[#fffafa] px-7 py-7 md:px-10">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-[24px] border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
                    <div class="text-xs font-black tracking-[0.14em] uppercase text-black/40">Status</div>
                    <div class="mt-4">
                        @if($contract->is_signed)
                            <span class="inline-flex rounded-full px-4 py-2 text-sm font-extrabold bg-green-100 text-green-700">
                                Signed
                            </span>
                        @else
                            <span class="inline-flex rounded-full px-4 py-2 text-sm font-extrabold bg-yellow-100 text-yellow-700">
                                Pending Signature
                            </span>
                        @endif
                    </div>
                </div>

                <div class="rounded-[24px] border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
                    <div class="text-xs font-black tracking-[0.14em] uppercase text-black/40">Rental Period</div>
                    <div class="mt-4 text-[15px] font-bold leading-7 text-black/75">
                        {{ optional($contract->start_date)->format('d M Y') ?? '-' }}<br>
                        to {{ optional($contract->end_date)->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="rounded-[24px] border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
                    <div class="text-xs font-black tracking-[0.14em] uppercase text-black/40">Monthly Rent</div>
                    <div class="mt-4 text-3xl font-extrabold tracking-tight" style="color: {{ $choco }};">
                        RM {{ number_format((float)$contract->monthly_rent, 2) }}
                    </div>
                </div>

                <div class="rounded-[24px] border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
                    <div class="text-xs font-black tracking-[0.14em] uppercase text-black/40">Deposit</div>
                    <div class="mt-4 text-3xl font-extrabold tracking-tight" style="color: {{ $gold }};">
                        RM {{ number_format((float)$contract->deposit_amount, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP TABS -->
        <div class="border-b border-[rgba(201,42,42,.08)] bg-white px-7 py-6 md:px-10">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <a href="#section-introduction" class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-4 text-center font-extrabold text-black/75 hover:bg-[#fff5f5] transition">1. Introduction</a>
                <a href="#section-terms" class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-4 text-center font-extrabold text-black/75 hover:bg-[#fff5f5] transition">2. Rental Terms</a>
                <a href="#section-responsibilities" class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-4 text-center font-extrabold text-black/75 hover:bg-[#fff5f5] transition">3. Responsibilities</a>
                <a href="#section-agreement" class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-4 text-center font-extrabold text-black/75 hover:bg-[#fff5f5] transition">4. Agreement</a>
                <a href="#section-signature" class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-4 text-center font-extrabold text-black/75 hover:bg-[#fff5f5] transition">5. Signature</a>
            </div>
        </div>

        <!-- BODY -->
        <div class="space-y-6 px-7 py-7 md:px-10 md:py-8">

            <!-- INTRODUCTION -->
            <section id="section-introduction" class="scroll-mt-28 rounded-[28px] border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
                <div class="border-b border-[rgba(201,42,42,.10)] px-6 py-5" style="background: {{ $soft }};">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl text-white font-extrabold shadow-sm" style="background: {{ $choco }};">1</span>
                        <div>
                            <div class="text-xs uppercase tracking-[0.14em] font-black text-black/45">Step One</div>
                            <h2 class="text-2xl font-extrabold" style="color: {{ $choco }};">Introduction</h2>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <p class="text-[16px] leading-8 text-black/68">
                        This digital contract confirms the tenancy arrangement between the student tenant and the landlord through the Smart Rental system. By proceeding with the signing process, the tenant acknowledges the room details, tenancy period, rental obligations, and listed responsibilities.
                    </p>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-5">
                            <div class="text-sm font-bold text-black/50">Student Tenant</div>
                            <div class="mt-1 text-xl font-extrabold text-black/80">{{ $contract->student->name ?? '-' }}</div>
                        </div>

                        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-5">
                            <div class="text-sm font-bold text-black/50">Landlord</div>
                            <div class="mt-1 text-xl font-extrabold text-black/80">{{ $contract->landlord->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RENTAL TERMS -->
            <section id="section-terms" class="scroll-mt-28 rounded-[28px] border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
                <div class="border-b border-[rgba(201,42,42,.10)] px-6 py-5" style="background: {{ $soft }};">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl text-white font-extrabold shadow-sm" style="background: {{ $gold }};">2</span>
                        <div>
                            <div class="text-xs uppercase tracking-[0.14em] font-black text-black/45">Step Two</div>
                            <h2 class="text-2xl font-extrabold" style="color: {{ $choco }};">Rental Terms</h2>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-6 py-6">
                    <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5">
                        <div class="text-sm font-bold text-black/50">Room Title</div>
                        <div class="mt-1 text-lg font-extrabold text-black/80">{{ $contract->room_title ?? '-' }}</div>
                    </div>

                    <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5">
                        <div class="text-sm font-bold text-black/50">Room Type</div>
                        <div class="mt-1 text-lg font-extrabold text-black/80">{{ $contract->room_type ?? '-' }}</div>
                    </div>

                    <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5">
                        <div class="text-sm font-bold text-black/50">Start Date</div>
                        <div class="mt-1 text-lg font-extrabold text-black/80">{{ optional($contract->start_date)->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5">
                        <div class="text-sm font-bold text-black/50">End Date</div>
                        <div class="mt-1 text-lg font-extrabold text-black/80">{{ optional($contract->end_date)->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5">
                        <div class="text-sm font-bold text-black/50">Monthly Rent</div>
                        <div class="mt-1 text-lg font-extrabold text-black/80">RM {{ number_format((float)$contract->monthly_rent, 2) }}</div>
                    </div>

                    <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5">
                        <div class="text-sm font-bold text-black/50">Deposit Amount</div>
                        <div class="mt-1 text-lg font-extrabold text-black/80">RM {{ number_format((float)$contract->deposit_amount, 2) }}</div>
                    </div>
                </div>
            </section>

            <!-- RESPONSIBILITIES -->
            <section id="section-responsibilities" class="scroll-mt-28 rounded-[28px] border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
                <div class="border-b border-[rgba(201,42,42,.10)] px-6 py-5" style="background: {{ $soft }};">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl text-white font-extrabold shadow-sm" style="background: {{ $choco }};">3</span>
                        <div>
                            <div class="text-xs uppercase tracking-[0.14em] font-black text-black/45">Step Three</div>
                            <h2 class="text-2xl font-extrabold" style="color: {{ $choco }};">Responsibilities</h2>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-6">
                        <ol class="space-y-4 text-[16px] leading-8 text-black/68">
                            <li><span class="font-extrabold text-black/75">1.</span> The tenant must pay monthly rent on or before the due date stated by the landlord.</li>
                            <li><span class="font-extrabold text-black/75">2.</span> The tenant must keep the rented room and shared areas in clean and reasonable condition.</li>
                            <li><span class="font-extrabold text-black/75">3.</span> The tenant is responsible for any damage caused during the tenancy period.</li>
                            <li><span class="font-extrabold text-black/75">4.</span> Illegal activities, disturbances, and unauthorized occupants are strictly prohibited.</li>
                            <li><span class="font-extrabold text-black/75">5.</span> The landlord may deduct repair or replacement costs from the deposit if damage is proven.</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- AGREEMENT -->
            <section id="section-agreement" class="scroll-mt-28 rounded-[28px] border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
                <div class="border-b border-[rgba(201,42,42,.10)] px-6 py-5" style="background: {{ $soft }};">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl text-white font-extrabold shadow-sm" style="background: {{ $gold }};">4</span>
                        <div>
                            <div class="text-xs uppercase tracking-[0.14em] font-black text-black/45">Step Four</div>
                            <h2 class="text-2xl font-extrabold" style="color: {{ $choco }};">Agreement</h2>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="rounded-3xl border border-amber-200 bg-amber-50 px-5 py-5">
                        <div class="text-sm font-extrabold text-amber-800">Important Notice</div>
                        <div class="mt-2 text-[15px] leading-7 text-amber-800/90">
                            By accepting this contract, you confirm that all information is understood and that your digital acceptance will be recorded in the Smart Rental system.
                        </div>
                    </div>
                </div>
            </section>

            <!-- SIGNATURE -->
            <section id="section-signature" class="scroll-mt-28 rounded-[28px] border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
                <div class="border-b border-[rgba(201,42,42,.10)] px-6 py-5" style="background: {{ $soft }};">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl text-white font-extrabold shadow-sm" style="background: {{ $choco }};">5</span>
                        <div>
                            <div class="text-xs uppercase tracking-[0.14em] font-black text-black/45">Step Five</div>
                            <h2 class="text-2xl font-extrabold" style="color: {{ $choco }};">Signature</h2>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    @if($contract->is_signed)
                        <div class="rounded-[24px] border border-green-200 bg-gradient-to-br from-green-50 to-white p-6 shadow-sm">
                            <div class="inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-sm font-extrabold text-green-700">
                                ✅ Contract Signed Successfully
                            </div>

                            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="rounded-2xl border border-green-200 bg-white p-5">
                                    <div class="text-sm font-bold text-black/50">Signed Name</div>
                                    <div class="mt-1 text-lg font-extrabold text-black/80">{{ $contract->signed_name }}</div>
                                </div>

                                <div class="rounded-2xl border border-green-200 bg-white p-5">
                                    <div class="text-sm font-bold text-black/50">Signed Date</div>
                                    <div class="mt-1 text-lg font-extrabold text-black/80">{{ optional($contract->signed_at)->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>

                            @if($contract->signature_data)
                                <div class="mt-5">
                                    <div class="text-sm font-bold text-black/50 mb-2">Saved Signature</div>
                                    <div class="rounded-2xl border border-green-200 bg-white p-4">
                                        <img src="{{ $contract->signature_data }}" alt="Signature" class="max-h-28">
                                    </div>
                                </div>
                            @endif

                            <div class="mt-5">
                                <a href="{{ route('student.contracts.pdf', $contract) }}"
                                   class="inline-flex items-center justify-center rounded-2xl px-6 py-3 font-extrabold text-white shadow-sm hover:brightness-95 transition"
                                   style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                                    Download Signed PDF
                                </a>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('student.contracts.sign', $contract) }}" id="signForm">
                            @csrf

                            <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-5">
                                <label class="flex items-start gap-3">
                                    <input type="checkbox" name="agreed_to_terms" value="1" class="mt-1 rounded border-black/20">
                                    <span class="text-[15px] leading-7 text-black/68">
                                        I confirm that I have fully read and understood this rental agreement. I accept the tenancy terms, payment obligations, and responsibilities stated in this contract.
                                    </span>
                                </label>
                            </div>

                            <div class="mt-5">
                                <label class="block text-sm font-bold text-black/55 mb-2">Type Full Name as Digital Signature</label>
                                <input type="text"
                                       name="signed_name"
                                       value="{{ old('signed_name', $contract->student->name ?? '') }}"
                                       class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-white px-4 py-4 text-[15px] focus:border-[rgba(201,42,42,.20)] focus:ring-0"
                                       placeholder="Type your full legal name">
                            </div>

                            <div class="mt-5">
                                <label class="block text-sm font-bold text-black/55 mb-2">Draw Signature</label>
                                <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-4">
                                    <canvas id="signature-pad" class="w-full rounded-2xl border border-dashed border-[rgba(201,42,42,.15)] bg-[#fffafa]" height="220"></canvas>
                                    <input type="hidden" name="signature_data" id="signature_data">

                                    <div class="mt-4 flex flex-wrap gap-3">
                                        <button type="button"
                                                id="clear-signature"
                                                class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-white px-5 py-3 font-extrabold text-black/75 hover:bg-[#fff5f5] transition">
                                            Clear Signature
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 rounded-3xl border border-dashed border-[rgba(201,42,42,.15)] bg-[#fffafa] p-5">
                                <div class="text-sm font-extrabold text-black/55">Digital Signature Acknowledgement</div>
                                <div class="mt-2 text-sm leading-7 text-black/60">
                                    Clicking <b>Sign Contract</b> means your typed name, signature drawing, and timestamp will be stored as your official digital acceptance in the Smart Rental system.
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                        class="rounded-2xl px-8 py-3.5 font-extrabold text-white shadow-sm hover:brightness-95 transition"
                                        style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                                    Sign Contract
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </section>

        </div>
    </div>
</div>

@if(!$contract->is_signed)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signature-pad');
    const hiddenInput = document.getElementById('signature_data');
    const clearBtn = document.getElementById('clear-signature');
    const form = document.getElementById('signForm');

    if (!canvas || !hiddenInput || !form) return;

    const ctx = canvas.getContext('2d');
    let drawing = false;

    function setupCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();

        canvas.width = rect.width * ratio;
        canvas.height = 220 * ratio;

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#4a2c2a';
        ctx.fillStyle = '#fffafa';
        ctx.fillRect(0, 0, rect.width, 220);
    }

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function startDraw(e) {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function draw(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function stopDraw(e) {
        drawing = false;
        e.preventDefault();
    }

    setupCanvas();
    window.addEventListener('resize', setupCanvas);

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw, { passive: false });

    clearBtn.addEventListener('click', function () {
        setupCanvas();
        hiddenInput.value = '';
    });

    form.addEventListener('submit', function (e) {
        hiddenInput.value = canvas.toDataURL('image/png');

        const blankCanvas = document.createElement('canvas');
        blankCanvas.width = canvas.width;
        blankCanvas.height = canvas.height;
        const blankCtx = blankCanvas.getContext('2d');
        blankCtx.fillStyle = '#fffafa';
        blankCtx.fillRect(0, 0, blankCanvas.width, blankCanvas.height);

        if (canvas.toDataURL('image/png') === blankCanvas.toDataURL('image/png')) {
            e.preventDefault();
            alert('Please draw your signature before submitting.');
        }
    });
});
</script>
@endif
@endsection