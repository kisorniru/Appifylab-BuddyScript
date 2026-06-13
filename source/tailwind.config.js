// tailwind.config.js

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.{js,ts,jsx,tsx,html}', // Looks for all .html and .js files in the 'src' folder
    './*.html',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.jsx', // <-- Make sure this points to your React components
    './resources/js/**/*.tsx', // <-- Add if you use TypeScript
  ],

  theme: {
    extend: {},
  },

  plugins: [require('@tailwindcss/forms')],
};
