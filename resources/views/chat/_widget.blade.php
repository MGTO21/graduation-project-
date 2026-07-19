@php
    // آخر id عندنا وقت ما الصفحة اتفتحت - أول ما الـ polling يشتغل هيسأل عن اللي بعده بس
    $lastId = $messages->last()->id ?? 0;
@endphp

<div class="card flex flex-col h-[560px]"
     id="chat-widget-{{ $courseOffering->id }}"
     data-fetch-url="{{ route('chat.fetch', $courseOffering) }}"
     data-store-url="{{ route('chat.store', $courseOffering) }}"
     data-last-id="{{ $lastId }}"
     data-current-user-id="{{ auth()->id() }}">

    <p class="font-cairo font-bold text-ink mb-3 pb-3 border-b border-line">
        شات المقرر
    </p>

    <div class="chat-messages flex-1 overflow-y-auto space-y-2 pl-1">
        @forelse($messages as $msg)
            @php $isOwn = $msg->user_id === auth()->id(); @endphp

            {{-- رسالتي أنا على جهة، رسائل البقية على الجهة التانية - نفس فكرة أي شات عادي --}}
            <div class="flex {{ $isOwn ? 'justify-start' : 'justify-end' }}" data-msg-id="{{ $msg->id }}">
                <div class="max-w-[80%] rounded-lg px-3 py-2 text-sm {{ $isOwn ? 'bg-gold/10 border border-gold/30 rounded-tl-none' : 'bg-sand border border-line rounded-tr-none' }}">
                    @unless($isOwn)
                        <span class="font-cairo font-bold text-xs {{ $msg->user->role === 'lecturer' ? 'text-gold' : 'text-ink' }}">
                            {{ $msg->user->name }}
                        </span>
                    @endunless
                    <p class="text-body mt-0.5">{{ $msg->message }}</p>
                    <span class="text-muted text-[10px] block mt-1">{{ $msg->created_at->format('H:i') }}</span>
                </div>
            </div>
        @empty
            <p class="text-muted text-xs text-center py-8 chat-empty-state">
                لا توجد رسائل بعد، ابدأ المحادثة
            </p>
        @endforelse
    </div>

    <form class="chat-form flex items-center gap-2 mt-3 pt-3 border-t border-line">
        <input type="text" class="chat-input input-field flex-1" placeholder="اكتب رسالتك..." maxlength="1000" autocomplete="off">
        <button type="submit" class="chat-send-btn btn-gold !px-4">إرسال</button>
    </form>

</div>

<script>
/*
 * فكرة الـ polling باختصار: كل 3 ثواني نبعت طلب GET للسيرفر ونمرر آخر id رسالة شفناه
 * (after_id)، والسيرفر يرجع بس الرسائل الأجد منه. كده ما بنعيد تحميل نفس الرسائل مرة
 * ثانية، وما بنكرر شيء في الشاشة. لو ما فيه رسائل جديدة يرجع array فاضي وما يصير شيء.
 *
 * ملاحظة عن مشكلة تكرار الرسائل اللي كانت بتصير: كان فيه احتمال إن دورة الـ polling
 * التلقائية (كل 3 ثواني) تشتغل في نفس لحظة الجلب الفوري اللي بيصير بعد الإرسال مباشرة،
 * فيصير في طلبين يسألوا عن الرسائل الجديدة بنفس اللحظة تقريباً، ولو السيرفر رد عليهم
 * الاتنين قبل ما أي واحد فيهم يحدّث lastId، هيرجعوا نفس الرسالة الجديدة مرتين فتتكرر
 * على الشاشة. عالجنا الموضوع من جهتين: isPolling يمنع تشغيل طلبين في نفس الوقت،
 * وfindMessageEl بيتأكد إن الرسالة مش موجودة أصلاً في الشاشة قبل ما يضيفها (حماية إضافية
 * حتى لو حصل تداخل بأي شكل تاني). وزودنا تعطيل زر الإرسال أثناء الطلب عشان دبل-كليك
 * أو ضغط Enter مرتين ما يبعت نفس الرسالة كسجل منفصل في القاعدة.
 */
(function () {
    const widget = document.getElementById('chat-widget-{{ $courseOffering->id }}');
    const list = widget.querySelector('.chat-messages');
    const form = widget.querySelector('.chat-form');
    const input = widget.querySelector('.chat-input');
    const sendBtn = widget.querySelector('.chat-send-btn');

    let lastId = parseInt(widget.dataset.lastId, 10) || 0;
    let isPolling = false;
    const currentUserId = parseInt(widget.dataset.currentUserId, 10);
    const fetchUrl = widget.dataset.fetchUrl;
    const storeUrl = widget.dataset.storeUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // نتحقق هل المستخدم أصلاً واقف في آخر الشات قبل ما نضيف رسائل جديدة، عشان ما نسحبه
    // لتحت وهو قاعد يقرأ رسائل قديمة فوق - المسافة 40px هامش بسيط يعتبره "قريب من الآخر"
    function isNearBottom() {
        return list.scrollHeight - list.scrollTop - list.clientHeight < 40;
    }

    function findMessageEl(id) {
        return list.querySelector('[data-msg-id="' + id + '"]');
    }

    function appendMessage(msg) {
        // حماية من التكرار: لو الرسالة دي أصلاً معروضة، ما نضيفها مرة ثانية
        if (findMessageEl(msg.id)) {
            return;
        }

        const wasNearBottom = isNearBottom();

        const emptyState = list.querySelector('.chat-empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        const isOwn = msg.user_id === currentUserId;
        const roleColor = msg.role === 'lecturer' ? 'text-gold' : 'text-ink';

        const row = document.createElement('div');
        row.className = 'flex ' + (isOwn ? 'justify-start' : 'justify-end');
        row.dataset.msgId = msg.id;

        const bubbleClasses = isOwn
            ? 'bg-gold/10 border border-gold/30 rounded-tl-none'
            : 'bg-sand border border-line rounded-tr-none';

        const nameHtml = isOwn
            ? ''
            : `<span class="font-cairo font-bold text-xs ${roleColor}">${escapeHtml(msg.name)}</span>`;

        row.innerHTML = `
            <div class="max-w-[80%] rounded-lg px-3 py-2 text-sm ${bubbleClasses}">
                ${nameHtml}
                <p class="text-body mt-0.5">${escapeHtml(msg.message)}</p>
                <span class="text-muted text-[10px] block mt-1">${escapeHtml(msg.time)}</span>
            </div>
        `;
        list.appendChild(row);

        if (wasNearBottom) {
            list.scrollTop = list.scrollHeight;
        }
    }

    function pollMessages() {
        // منع تشغيل أكتر من طلب في نفس الوقت - هذا هو اللي كان بيسبب تكرار الرسائل
        if (isPolling) {
            return;
        }
        isPolling = true;

        fetch(fetchUrl + '?after_id=' + lastId)
            .then(res => res.json())
            .then(data => {
                data.messages.forEach(msg => {
                    appendMessage(msg);
                    lastId = Math.max(lastId, msg.id);
                });
            })
            .catch(() => {
                // خطأ شبكة عابر، ما نعمل شيء وننتظر المحاولة الجاية بعد 3 ثواني
            })
            .finally(() => {
                isPolling = false;
            });
    }

    // أول ما الصفحة تفتح ننزل لآخر الشات مباشرة
    list.scrollTop = list.scrollHeight;

    setInterval(pollMessages, 3000);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const text = input.value.trim();
        if (! text || sendBtn.disabled) return;

        // نعطّل الزر لحد ما نتأكد الرسالة اتبعتت، عشان دبل-كليك أو Enter مرتين
        // ما يبعتوا نفس الرسالة كسجل مستقل في قاعدة البيانات
        sendBtn.disabled = true;
        input.value = '';

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text }),
        })
        .then(res => res.json())
        .then(() => pollMessages()) // ما ننتظر دورة الـ polling الجاية، نجيب الرسالة فوراً بعد الإرسال
        .finally(() => {
            sendBtn.disabled = false;
        });
    });
})();
</script>
