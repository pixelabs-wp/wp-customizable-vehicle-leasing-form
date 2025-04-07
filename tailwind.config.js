/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'alc-',
  content: [
    "./subscription-form.html",
    "./subscription-form.js"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#01c257',
        'primary-dark': '#00a048'
      }
    },
  },
  plugins: [],
} 