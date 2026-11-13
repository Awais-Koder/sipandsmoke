/* Optional Tailwind bridge. The static page does not load Tailwind.
   All design values live in assets/css/theme.css. These aliases are here if
   the site later moves to a compiled Tailwind build. */
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        paper: 'rgb(var(--paper) / <alpha-value>)',
        surface: 'rgb(var(--surface) / <alpha-value>)',
        'surface-blue': 'rgb(var(--surface-blue) / <alpha-value>)',
        'surface-warm': 'rgb(var(--surface-warm) / <alpha-value>)',
        ice: 'rgb(var(--ice) / <alpha-value>)',
        ink: 'rgb(var(--ink) / <alpha-value>)',
        muted: 'rgb(var(--muted) / <alpha-value>)',
        line: 'rgb(var(--line) / <alpha-value>)',
        brand: {
          DEFAULT: 'rgb(var(--brand) / <alpha-value>)',
          deep: 'rgb(var(--brand-deep) / <alpha-value>)',
          soft: 'rgb(var(--brand-soft) / <alpha-value>)',
          mist: 'rgb(var(--brand-mist) / <alpha-value>)',
        },
      },
      fontFamily: {
        display: ['var(--font-display)'],
        body: ['var(--font-body)'],
        sans: ['var(--font-body)'],
      },
      maxWidth: {
        shell: 'var(--max-shell)',
      },
      spacing: {
        gutter: 'var(--gutter)',
        section: 'var(--section-space)',
        header: 'var(--header-h)',
      },
      borderRadius: {
        card: 'var(--radius-md)',
        panel: 'var(--radius-lg)',
        pill: 'var(--radius-pill)',
      },
      boxShadow: {
        soft: 'var(--shadow-soft)',
        lift: 'var(--shadow-lift)',
        card: 'var(--shadow-card)',
      },
    },
  },
};
