<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\CourseOffering;
use Illuminate\Http\Request;

/*
 * الشات هنا شغال بطريقة الـ AJAX Polling، مش WebSocket ولا Pusher ولا Laravel Echo/Broadcasting.
 * يعني المتصفح كل 3 ثواني يسأل السيرفر "في رسائل جديدة أجد من آخر رسالة شفتها؟" عن طريق
 * طلب HTTP عادي (شوف route chat.fetch والـ JS في resources/views/chat/_widget.blade.php).
 * القرار مقصود: أبسط بكثير للشرح في مناقشة تخرج، وما يحتاج تشغيل سيرفر WebSocket إضافي
 * (زي Laravel Reverb) ولا Queue worker. الطريقة دي كافية جداً لعدد المستخدمين المتوقع هنا
 * (شات لمقرر واحد فيه طلاب قسم وسمستر واحد + محاضر واحد، مش آلاف المستخدمين المتزامنين).
 * لو المشروع يوماً كبر لعدد ضخم من المستخدمين، البديل الأنسب وقتها WebSocket لأنه ما يعمل
 * طلب HTTP جديد كل شوي لكل مستخدم، لكن هنا Polling أبسط وأنسب لحجم المشروع.
 *
 * الراوت هنا (chat.*) مشترك بين محاضر وطالب مع بعض، فما قدرنا نحطه تحت role: middleware
 * عادي (بيقبل دور واحد بس). لهذا التحقق من الصلاحية كامل صار جوا الكنترولر في authorizeAccess().
 */
class ChatController extends Controller
{
    /**
     * صفحة الشات: قائمة آخر الرسائل + حقل الإرسال
     */
    public function index(CourseOffering $courseOffering)
    {
        $this->authorizeAccess($courseOffering);

        $courseOffering->load(['course', 'lecturer']);

        // بنجيب آخر 50 رسالة بس عشان الصفحة ما تفتح بطيئة لو تاريخ الشات طويل
        $messages = ChatMessage::with('user')
            ->where('course_offering_id', $courseOffering->id)
            ->orderByDesc('id')
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('chat.show', compact('courseOffering', 'messages'));
    }

    /**
     * جلب الرسائل الجديدة فقط (بعد آخر id وصل عند المستخدم) - هذا اللي بيناديه الـ polling كل 3 ثواني
     */
    public function fetch(Request $request, CourseOffering $courseOffering)
    {
        $this->authorizeAccess($courseOffering);

        $afterId = (int) $request->query('after_id', 0);

        $messages = ChatMessage::with('user')
            ->where('course_offering_id', $courseOffering->id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get();

        return response()->json([
            'messages' => $messages->map(fn (ChatMessage $m) => [
                'id'      => $m->id,
                'name'    => $m->user->name,
                'role'    => $m->user->role,
                'message' => $m->message,
                'time'    => $m->created_at->format('H:i'),
            ]),
        ]);
    }

    /**
     * إرسال رسالة جديدة في شات المقرر
     */
    public function store(Request $request, CourseOffering $courseOffering)
    {
        $this->authorizeAccess($courseOffering);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        ChatMessage::create([
            'course_offering_id' => $courseOffering->id,
            'user_id'            => auth()->id(),
            'message'            => $request->message,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * التحقق من صلاحية الوصول لشات المقرر:
     * المحاضر لازم يكون هو محاضر هذا المقرر بالذات، والطالب لازم يكون من نفس قسم وسمستر المقرر
     */
    private function authorizeAccess(CourseOffering $courseOffering): void
    {
        $user = auth()->user();

        if ($user->role === 'lecturer' && $courseOffering->lecturer_id === $user->id) {
            return;
        }

        if ($user->role === 'student'
            && $courseOffering->department_id === $user->department_id
            && $courseOffering->semester_id === $user->semester_id) {
            return;
        }

        abort(403, 'غير مصرح لك بالوصول إلى شات هذا المقرر.');
    }
}
