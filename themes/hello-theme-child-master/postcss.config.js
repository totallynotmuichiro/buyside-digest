module.exports = {
    plugins: [
      require('tailwindcss'),
      require('autoprefixer'),
      require('postcss-prefix-selector')({
        prefix: '.page-template.page-template-page-investor .bsd-container',
        exclude: ['html', 'body', ':root'],
        transform: function (prefix, selector, prefixedSelector) {
          if (selector.indexOf(prefix) === 0) {
            return selector;
          }
          return prefixedSelector;
        }
      })
    ]
  }