/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./page-investor.php", "./template-parts/investor/*.php" ],
  theme: {
    extend: {
      colors : {
        'primary': '#0d3e6f',
        'secondary': '#834902',
      },
    },
  },
  plugins: [],
}

