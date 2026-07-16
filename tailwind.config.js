import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // ألوان الهوية البصرية - مستوحاة من هوية جامعة النيلين
            colors: {
                cream:   '#FAF7F2', // الخلفية العامة
                gold:    '#C8922A', // اللون الأساسي
                'gold-dark': '#B8841F', // hover الذهبي
                ink:     '#1F2430', // العناوين
                body:    '#3A4150', // النص الأساسي
                muted:   '#8A8F9C', // النص الثانوي
                line:    '#E8E2D6', // الحدود
                sand:    '#F5F1E8', // رأس الجداول
                success: '#3E9B6E',
                danger:  '#D9534F',
                warning: '#D9A62E',
            },
            fontFamily: {
                cairo: ['Cairo', ...defaultTheme.fontFamily.sans],
                tajawal: ['Tajawal', ...defaultTheme.fontFamily.sans],
                sans: ['Tajawal', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
