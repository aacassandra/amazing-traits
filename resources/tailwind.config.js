/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
  daisyui: {
      themes: [
          {
              light: {

                  "primary": "#0060a8",

                  "secondary": "#004881",

                  "accent": "#ef4444",

                  "neutral": "#3D4451",

                  "base-100": "#ffffff",

                  "info": "#3ABFF8",

                  "success": "#2dd4bf",

                  "warning": "#FBBD23",

                  "error": "#FF1700",
              },
              dark: {

                  "primary": "#0060a8",

                  "secondary": "#004881",

                  "accent": "#ef4444",

                  "neutral": "#a1a4a7",

                  "base-100": "#3d4451",

                  "info": "#3ABFF8",

                  "success": "#2dd4bf",

                  "warning": "#FBBD23",

                  "error": "#FF1700",
              }
          }
      ]
  },
}
