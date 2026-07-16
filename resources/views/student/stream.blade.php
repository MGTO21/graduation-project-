@extends('layouts.student')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        بث مباشر: {{ $courseOffering->course->name }}
    </h2>

    <a href="{{ route('student.dashboard') }}" class="btn-ghost">
        مغادرة القاعة
    </a>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    المحاضر: {{ $courseOffering->lecturer->name }}
</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- منطقة عرض البث --}}
    <div class="lg:col-span-2 card">

        <div class="relative w-full h-[460px] bg-ink rounded-sm overflow-hidden flex items-center justify-center">

            <div id="studentStatus" class="text-center z-10 p-6 max-w-sm">
                <div class="animate-spin inline-block w-8 h-8 border-4 border-gold border-t-transparent rounded-full mb-4"></div>
                <p class="text-sm text-white/80">جاري الاتصال بقاعة البث...</p>
            </div>

            <video id="remoteVideo" autoplay playsinline controls class="hidden w-full h-full object-cover"></video>

        </div>

    </div>

    {{-- شات المقرر بجانب الفيديو --}}
    <div class="lg:col-span-1">
        @include('chat._widget', ['courseOffering' => $courseOffering, 'messages' => $messages])
    </div>

</div>

<script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
<script>
/*
 * الطالب هنا "receive-only": ما بيبعت صوت ولا صورة، بس بيستقبل بث المحاضر.
 * الخطوات: 1) نعمل Peer ID عشوائي للطالب نفسه (مش محتاج نعرفه مسبقاً).
 * 2) نتصل بقناة بيانات (Data Connection) على meeting_id بتاع المحاضر عشان نسجل حضورنا
 *    في القاعة (لو المحاضر بدأ الكاميرا بعدنا، هو أصلاً بيحتفظ بقايمة المتصلين ويكلمنا).
 * 3) بعدين نستنى حدث 'call' يوصل من المحاضر (يعني هو بدأ يبعت الفيديو فعلاً) ونرد عليه
 *    بـ call.answer() من غير ما نبعت أي stream من عندنا.
 */
const studentStatus = document.getElementById('studentStatus');
const remoteVideo = document.getElementById('remoteVideo');

const peer = new Peer();

peer.on('open', () => {
    const conn = peer.connect(@json($lecture->meeting_id));

    conn.on('open', () => {
        studentStatus.querySelector('p').innerText = 'تم تسجيل حضورك، بانتظار بث المحاضر...';
    });

    conn.on('error', () => {
        studentStatus.innerHTML = '<p class="text-sm text-danger">تعذر الاتصال بقاعة المحاضر. تأكد أن المحاضر بدأ البث ثم أعد تحميل الصفحة.</p>';
    });
});

peer.on('call', (call) => {
    call.answer(); // نرد بوضع استقبال فقط، من غير ما نفعّل كاميرا أو مايك الطالب

    call.on('stream', (remoteStream) => {
        studentStatus.style.display = 'none';
        remoteVideo.classList.remove('hidden');
        remoteVideo.srcObject = remoteStream;

        remoteVideo.play().catch(() => {
            // بعض المتصفحات تحظر التشغيل التلقائي للصوت، شريط التحكم هيبان للطالب يشغّله يدوياً
        });
    });
});

peer.on('error', (err) => {
    studentStatus.innerHTML = '<p class="text-sm text-danger">خطأ في الاتصال: ' + err.type + '</p>';
});
</script>

@endsection
