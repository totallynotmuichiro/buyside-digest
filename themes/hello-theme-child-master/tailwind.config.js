/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./page-investor.php",
    "./template-parts/investor/*.php",
    "./page-landing.php",
    "./template-parts/landing/*.php",
    "./page-largest-fund-buy.php",
    "./template-parts/largest-fund-buy/*.php",
    "./page-portfolio.php",
    "./page-largest-fund-buy-detail.php",
    "./assets/js/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#0d3e6f",
        secondary: "#834902",
        territory: "#ffae00",
      },
      animation: {
        appear: "appear 0.6s forwards ease-out",
      },
      keyframes: {
        appear: {
          "0%": {
            opacity: "0",
            transform: "translateY(1rem)",
            filter: "blur(.5rem)",
          },
          "50%": {
            filter: "blur(0)",
          },
          "100%": {
            opacity: "1",
            transform: "translateY(0)",
            filter: "blur(0)",
          },
        },
      },
    },
  },
  plugins: [],
};
