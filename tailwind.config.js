/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.jsx',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: [
      {
        benka: {
          "primary": "#2563eb",        // Bleu - couleur principale de la société
          "primary-content": "#ffffff",
          "secondary": "#facc15",       // Jaune - couleur secondaire de la société
          "secondary-content": "#1f2937",
          "accent": "#3b82f6",          // Bleu accent au lieu de violet
          "accent-content": "#ffffff",
          "neutral": "#1f2937",
          "neutral-content": "#f3f4f6",
          "base-100": "#ffffff",
          "base-200": "#f8fafc",
          "base-300": "#e2e8f0",
          "base-content": "#1f2937",
          "info": "#3b82f6",            // Bleu info
          "info-content": "#ffffff",
          "success": "#22c55e",         // Vert pour succès/présence
          "success-content": "#ffffff",
          "warning": "#facc15",         // Jaune pour avertissement
          "warning-content": "#1f2937",
          "error": "#ef4444",           // Rouge pour erreur/absence
          "error-content": "#ffffff",
        },
      },
    ],
    darkTheme: false,
    base: true,
    styled: true,
    utils: true,
  },
}
