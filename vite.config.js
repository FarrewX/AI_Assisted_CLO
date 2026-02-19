import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/management/addcourse.js',
                'resources/js/management/curriculum.js',
                'resources/js/management/email.js',
                'resources/js/management/plo.js',
                'resources/js/management/professor_course.js',
                'resources/js/form.js',
                'resources/js/register.js',
                'resources/js/management/users.js',
                'resources/js/editdoc.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
