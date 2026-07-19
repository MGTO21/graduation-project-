<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            // ملاحظة: مقصود عدم وجود قواعد لـ university_id أو department_id أو
            // semester_id هنا - المستخدم ما يقدر يغيرهم من صفحة ملفه الشخصي أصلاً،
            // بس الأدمن يقدر من لوحته
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
