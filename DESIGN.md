---
name: Sip & Smoke — Orange & Ice
colors:
  brand-orange: '#FF8200'
  brand-blue: '#2C5697'
  brand-ice: '#EAEEF5'
  paper: '#F8FAFD'
  surface-blue: '#F0F7FC'
  surface-warm: '#FFF2E4'
  ink: '#12233A'
  muted: '#57677B'
  line: '#DBE5F0'
typography:
  display: Fraunces
  body: Plus Jakarta Sans
---

# Sip & Smoke — Orange & Ice

A light, warm editorial identity drawn directly from the supplied logo. The site
should feel like a bright neighbourhood juice bar and late-night dessert lounge.
The supplied full-bleed mango-pour hero is the one deep, saturated moment; every
section below it returns to paper and ice.

The same palette, type, radii, spacing and pill controls carry into the Laravel
admin control room, so the people running the venue feel like they are still
inside the brand.

## Palette

| Token | Value | Use |
| --- | --- | --- |
| Brand orange | `#FF8200` | Underlines, marquee, accents, CTA background |
| Brand blue | `#2C5697` | Logo wordmark, headings, secondary ink, active states |
| Brand ice | `#EAEEF5` | Logo background, quiet borders and washes |
| Paper | `#F8FAFD` | Main page background |
| Surface blue | `#F0F7FC` | Juice-bar and footer bands |
| Surface warm | `#FFF2E4` | Contact panel and warm accents |
| Ink | `#12233A` | Primary text, admin sidebar and CTA text on orange |
| Muted | `#57677B` | Supporting copy |
| Line | `#DBE5F0` | Hairlines and card borders |

Orange is used as a field, rule or highlight. Small orange text on white is
avoided for readability; use the deep blue for small text. White on brand blue
and deep ink on brand orange both clear WCAG AA.

The three brand colours are editable in the admin. They are injected as RGB CSS
variables in both layouts, so changing them retints the public site and admin.

## Type

- **Fraunces** (500/600, roman only) for the hero, section headings, product
  names, dialogs and admin page titles. Tight tracking, short line heights, no
  italics.
- **Plus Jakarta Sans** (300–700) for body copy, navigation, buttons, labels,
  captions and admin controls.
- Labels are compact, uppercase and letter-spaced. Body copy stays generous and
  readable.

## Public layout

- Shell: `80rem` maximum, responsive gutters from `1.25rem` to `2.5rem`.
- Header: a floating glass bar inset from the viewport edges, with rounded
  corners and a 20px backdrop blur. The full circular logo sits left, six links
  centre, one phone CTA right. On scroll it lifts, compacts and lets page content
  show through. Below `1120px` the links collapse into a full-screen native
  dialog.
- Hero: one full-bleed landscape mango-pour photograph with a deep brown scrim.
  Cream copy sits over the dark left side; “Stay late.” stays orange. The image,
  eyebrow, headline and CTA labels are editable from admin.
- Section rhythm: white menu panel → ice-blue juice bar → white about → warm
  contact → ice-blue footer. The gallery is a standalone paper page.
- Cards use `1.25rem` radii, hairline borders and restrained shadows. Photography
  carries the colour; avoid additional decorative gradients.

## Public components

- **Brand lockup:** the supplied full circular logo at its native aspect ratio in
  the navbar and footer. Logo, favicon and alt text are editable.
- **Buttons:** pill, uppercase, primary orange with ink text; hover becomes brand
  blue with white text. Secondary is a hairline outlined pill.
- **Product cards:** portrait photo, small category tag, serif name, short
  description, size range and a circular menu arrow. No prices are shown on the
  cards; the menu page carries all pricing. Cards and filters are database-driven.
- **Menu deck:** the current kitchen menu pages stacked as portrait cards. The
  top page can be dragged freely left or right; it flies out that way while the
  next page rises from below, cycling infinitely. Arrow buttons and keyboard
  arrows mirror the drag; clicking the top page opens `/menu` at that sheet.
- **Menu page:** clean Laravel route `/menu`; category sheets come from the
  `menu_pages` table. Up to 9+ pages stack and the deep links use `#page-N`.
  There is no PDF viewer or download.
- **Filters:** accessible pressed buttons with a live result count; product
  categories map to Juice bar, Shakes and Desserts.
- **Gallery page:** `/gallery` with a masthead, live range readout, masonry grid
  (4/3/2 columns) of 24 photographs per page and numbered pagination. Records are
  database-driven and can be batch-uploaded in admin. A dark native-dialog
  lightbox adds arrow-key, button and swipe navigation.

## Admin layout

- Deep ink sidebar with an orange active state, the brand logo and compact
  navigation. It becomes an off-canvas drawer below `900px`.
- Ice/pale paper workspace, white hairline panels, Fraunces headings, orange
  underlines and pill buttons.
- The content editor is a long grouped form with a sticky section index and a
  sticky save bar. Every image field shows its current file and a preview before
  saving.
- Menu, gallery and juice-bar records use dense tables with thumbnails, live
  status toggles, sort order and icon actions.
- Destructive actions require a confirmation. Successful actions raise a
  bottom-right toast; uploads and saves are conversion-aware and immediate.
- Uploads accept JPG/PNG/WebP/AVIF and are converted to compressed WebP with GD.

## Content rules

- Use the supplied menu as the price source. Product cards stay price-free; the
  menu page carries all pricing.
- Keep the single primary action: **Call to Book a Table**. Its label is editable.
- Contact details are editable: 56 Auburn Rd, Auburn NSW 2144,
  `0414 401 462`, `info@sipandsmoke.com.au`. Say “Open 7 days until late” unless
  exact trading hours are confirmed.
- Do not invent testimonials, metrics, history or menu items.
- The original seed data reflects what was available: 9 menu sheets (printed
  pages 1–8 and 10), 89 photographs and 11 juice-bar cards.

## Motion

- GSAP reveals and Lenis smooth scrolling are additive; the page is complete
  without them.
- The menu deck uses Pointer Events with a small drag threshold, so clicks still
  open the menu page. Reduced-motion users get the same page changes without the
  fly-out animation.
- Hero title lines rise in, media eases up, sections fade/slide on first entry.
- Ribbon marquee is slow and pauses on hover. Reduced-motion users get no
  animation, no smooth scroll and no auto-moving ribbon.
- Header lifts and compacts after scroll, gaining a stronger glass shadow.
- Admin respects reduced motion as well; transitions are suppressed, but all
  controls remain fully usable.

## Source of truth

- Public Blade: `resources/views/site/` and `resources/views/layouts/site.blade.php`
- Admin Blade/Livewire: `resources/views/layouts/admin.blade.php`,
  `resources/views/livewire/admin/`
- Public styles: `public/assets/css/theme.css`, `public/assets/css/site.css`
- Admin styles: `public/assets/css/admin.css`
- Interaction: `public/assets/js/app.js`, `public/assets/js/gallery.js`
- Content definition: `app/Support/SiteContent.php`
- Upload pipeline: `app/Services/ImageService.php`
