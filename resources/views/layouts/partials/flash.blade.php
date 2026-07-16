@if(session('success'))
    <div class="flash-success flash-message">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="flash-error flash-message">
        {{ session('error') }}
    </div>
@endif

@if(session('success') || session('error'))
    <script>
        // الرسالة تختفي تلقائياً بعد 4 ثواني عشان ما تضل واقفة في أعلى الصفحة تاخد حيز
        // من غير داعي - المستخدم أصلاً شافها، ما يحتاج يقفلها يدوياً
        document.querySelectorAll('.flash-message').forEach(function (el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.4s ease';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 400);
            }, 4000);
        });
    </script>
@endif
