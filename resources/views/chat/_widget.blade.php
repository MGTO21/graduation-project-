@php
    // آخر id عندنا وقت ما الصفحة اتفتحت - أول ما الـ polling يشتغل هيسأل عن اللي بعده بس
    $lastId = $messages->last()->id ?? 0;
@endphp

<div class="card flex flex-col h-[560px]"
     id="chat-widget-{{ $courseOffering->id }}"
     data-fetch-url="{{ route('chat.fetch', $courseOffering) }}"
     data-store-url="{{ route('chat.store', $courseOffering) }}"
     data-last-id="{{ $lastId }}">

    <p class="font-cairo font-bold text-ink mb-3 pb-3 border-b border-line">
        شات المقرر
    </p>

    <div class="chat-messages flex-1 overflow-y-auto space-y-3 pl-1">
        @forelse($messages as $msg)
            <div class="text-sm" data-msg-id="{{ $msg->id }}">
                <span class="font-cairo font-bold {{ $msg->user->role === 'lecturer' ? 'text-gold' : 'text-ink' }}">
                    {{ $msg->user->name }}
                </span>
                <span class="text-muted text-xs">{{ $msg->created_at->format('H:i') }}</span>
                <p class="text-body mt-0.5">{{ $msg->message }}</p>
            </div>
        @empty
            <p class="text-muted text-xs text-center py-8">
                لا توجد رسائل بعد، ابدأ المحادثة
            </p>
        @endforelse
    </div>

    <form class="chat-form flex items-center gap-2 mt-3 pt-3 border-t border-line">
        <input type="text" class="chat-input input-field flex-1" placeholder="اكتب رسالتك..." maxlength="1000" autocomplete="off">
        <button type="submit" class="btn-gold !px-4">إرسال</button>
    </form>

</div>

<script>
/*
 * فكرة الـ polling باختصار: كل 3 ثواني نبعت طلب GET للسيرفر ونمرر آخر id رسالة شفناه
 * (after_id)، والسيرفر يرجع بس الرسائل الأجد منه. كده ما بنعيد تحميل نفس الرسائل مرة
 * ثانية، وما بنكرر شيء في الشاشة. لو ما فيه رسائل جديدة يرجع array فاضي وما يصير شيء.
 */
(function () {
    const widget = document.getElementById('chat-widget-{{ $courseOffering->id }}');
    const list = widget.querySelector('.chat-messages');
    const form = widget.querySelector('.chat-form');
    const input = widget.querySelector('.chat-input');

    let lastId = parseInt(widget.dataset.lastId, 10) || 0;
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

    function appendMessage(msg) {
        const wasNearBottom = isNearBottom();

        // أول رسالة تبان بنشيل رسالة "لا توجد رسائل بعد"
        if (list.children.length === 1 && list.children[0].dataset.msgId === undefined) {
            list.innerHTML = '';
        }

        const roleColor = msg.role === 'lecturer' ? 'text-gold' : 'text-ink';

        const div = document.createElement('div');
        div.className = 'text-sm';
        div.dataset.msgId = msg.id;
        div.innerHTML = `
            <span class="font-cairo font-bold ${roleColor}">${escapeHtml(msg.name)}</span>
            <span class="text-muted text-xs">${escapeHtml(msg.time)}</span>
            <p class="text-body mt-0.5">${escapeHtml(msg.message)}</p>
        `;
        list.appendChild(div);

        if (wasNearBottom) {
            list.scrollTop = list.scrollHeight;
        }
    }

    function pollMessages() {
        fetch(fetchUrl + '?after_id=' + lastId)
            .then(res => res.json())
            .then(data => {
                data.messages.forEach(msg => {
                    appendMessage(msg);
                    lastId = msg.id;
                });
            })
            .catch(() => {
                // خطأ شبكة عابر، ما نعمل شيء وننتظر المحاولة الجاية بعد 3 ثواني
            });
    }

    // أول ما الصفحة تفتح ننزل لآخر الشات مباشرة
    list.scrollTop = list.scrollHeight;

    setInterval(pollMessages, 3000);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const text = input.value.trim();
        if (! text) return;

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
        .then(() => pollMessages()); // ما ننتظر دورة الـ polling الجاية، نجيب الرسالة فوراً بعد الإرسال
    });
})();
</script>
