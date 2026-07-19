<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // نتجاهل عمداً أي محاولة لتمرير university_id أو department_id من الفورم -
        // validated() أصلاً ما بترجعهم لأنهم مش موجودين في rules()، فـ fill() ما يقدر يغيرهم
        $user->fill($request->safe()->except('profile_image'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // رفع صورة شخصية جديدة: نحذف القديمة أولاً (لو موجودة) عشان ما نراكم ملفات يتيمة
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->profile_image = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
