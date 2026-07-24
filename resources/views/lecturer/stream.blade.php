@extends('layouts.lecturer')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h2 class="page-title">
        بث محاضرة: {{ $courseOffering->course->name }}
    </h2>

    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-danger">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-danger"></span>
            </span>
            البث مباشر الآن
        </span>

        {{-- بتفتح في تاب جديد عشان المحاضر يفضل شغال في صفحة البث وهو بيدير الكويز --}}
        <a href="{{ route('lecturer.quizzes.index', $courseOffering) }}" target="_blank" class="btn-ghost">
            اختبار فوري
        </a>

        <form action="{{ route('lecturer.stream.end', $lecture) }}" method="POST" class="inline">
            @csrf
            <button
                onclick="return confirm('هل أنت متأكد من إنهاء البث؟')"
                class="btn-del">
                إنهاء المحاضرة
            </button>
        </form>

        <a href="{{ route('lecturer.dashboard') }}" class="btn-ghost">
            رجوع للوحة
        </a>
    </div>
</div>

<p class="text-muted text-sm mb-8 pr-3">
    {{ $courseOffering->department->name ?? 'كل الأقسام' }}
    |
    تاريخ المحاضرة: {{ $lecture->lecture_date }}
</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- منطقة البث --}}
    <div class="lg:col-span-2 card">

        <div id="video-box" class="relative w-full h-[460px] bg-ink rounded-sm overflow-hidden flex items-center justify-center">

            <video id="localVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>

            <div id="statusMessage" class="absolute text-white text-sm font-medium bg-ink/80 px-5 py-3 rounded-sm border border-line/20">
                جاري تهيئة قاعة البث...
            </div>

        </div>

        <div class="flex justify-center mt-5">
            <button id="startStreamBtn" class="btn-gold !px-8 !py-3">
                تشغيل الكاميرا ونشر البث للطلاب
            </button>
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
 * فكرة PeerJS باختصار: WebRTC يحتاج طرفين يعرفوا بعض عشان يتفقوا كيف يوصلوا مباشرة لبعض
 * (signaling)، PeerJS بيسهّل الخطوة دي: كل طرف بياخد "Peer ID"، وبعدين تقدر تتصل بأي
 * طرف تاني لو عارف الـ ID بتاعه. هنا احنا مثبتين الـ Peer ID بتاع المحاضر = meeting_id
 * الجاي من قاعدة البيانات (مش نص ثابت زي الكود القديم)، عشان كل محاضرة ليها قاعة بث
 * منفصلة، ومحاضرتين ما يتصادموا مع بعض.
 *
 * ليه سيرفر إشارة محلي مش السحابي المجاني بتاع PeerJS؟ جربنا الافتراضي (0.peerjs.com)
 * وطلع غير مضمون - أحياناً بيرفض الاتصال (server-error) أو تتصادف مشكلة توقيت
 * (peer-unavailable) لأنه سيرفر عام آلاف المشاريع بتستخدمه في نفس الوقت وخارج سيطرتنا
 * تماماً. لمشروع تخرج لازم يكون شغال بثبات وقت المناقشة، فبدّلناه بسيرفر محلي بسيط
 * (حزمة peer من npm) بيتشغل على الجهاز نفسه بأمر: npm run peerjs (شوف TASHGHEEL.md).
 *
 * ملاحظة مهمة: WebRTC يشتغل من غير شهادة SSL بس على localhost. لو جربت من جهازين
 * مختلفين على نفس الشبكة (مش localhost) لازم HTTPS عشان المتصفح يسمح بالكاميرا والاتصال.
 */
const peer = new Peer(@json($lecture->meeting_id), {
    host: 'localhost',
    port: 9000,
    // مكتبة PeerJS نفسها بتركّب رابط الاتصال بالسيرفر من path + key، يعني path: '/'
    // هنا هي الصح (بترجع /peerjs/id بالظبط، لأن key بتاعنا اسمه "peerjs") - جربتها
    // فعلياً وأكدت إن أي path تاني هنا بيكسر الاتصال ويطلع "Could not get an ID"
    path: '/',
    key: 'peerjs',
});

let localStream = null;
// مجموعة (Set) لتخزين معرّفات الطلاب اللي دخلوا القاعة، عشان لو بدأنا الكاميرا بعدهم نقدر نتصل بيهم
const connectedStudents = new Set();

const startStreamBtn = document.getElementById('startStreamBtn');
const localVideo = document.getElementById('localVideo');
const statusMessage = document.getElementById('statusMessage');

peer.on('open', () => {
    statusMessage.innerText = 'القاعة جاهزة. اضغط الزر لتشغيل الكاميرا وبدء البث.';
});

// الاستماع للطلاب الذين يدخلون القاعة للتسجيل (Data Connection)
peer.on('connection', (conn) => {
    conn.on('open', () => {
        connectedStudents.add(conn.peer);

        // لو المحاضر مشغّل البث بالفعل ودخل طالب جديد، نرسل له البث فوراً
        if (localStream) {
            peer.call(conn.peer, localStream);
        }
    });
});

startStreamBtn.addEventListener('click', async () => {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });

        localVideo.srcObject = localStream;
        localVideo.play();
        statusMessage.style.display = 'none';

        // الاتصال بكل الطلاب اللي كانوا ينتظروا في القاعة قبل بدء البث
        connectedStudents.forEach(studentId => peer.call(studentId, localStream));

        startStreamBtn.disabled = true;
        startStreamBtn.textContent = 'البث يعمل الآن';
        startStreamBtn.classList.add('opacity-60', 'cursor-not-allowed');

    } catch (err) {
        alert('يرجى تفعيل صلاحيات الكاميرا والميكروفون في المتصفح لبدء المحاضرة.');
    }
});
</script>

@endsection
