<?php

namespace App\Support;

use App\Models\Setting;

class SiteContent
{
    /**
     * Every editable public-site field, grouped for the admin content editor.
     * Defaults are also used as the seeder source.
     *
     * @return array<string, array{label: string, note: string, fields: array<string, array<string, mixed>>}>
     */
    public static function groups(): array
    {
        return [
            'brand' => [
                'label' => 'Brand & SEO',
                'note' => 'Identity, browser metadata and social sharing details.',
                'fields' => [
                    'brand_name' => ['type' => 'text', 'label' => 'Business name', 'default' => 'Sip & Smoke'],
                    'brand_tagline' => ['type' => 'text', 'label' => 'Tagline', 'default' => 'Sip. Smoke. Unwind.'],
                    'brand_logo' => ['type' => 'image', 'label' => 'Logo', 'default' => 'assets/images/brand/logo-full.webp', 'help' => 'Used in the header, footer and mobile menu.'],
                    'brand_logo_alt' => ['type' => 'text', 'label' => 'Logo alt text', 'default' => 'Sip & Smoke'],
                    'brand_favicon' => ['type' => 'image', 'label' => 'Favicon', 'default' => 'assets/images/brand/favicon.png'],
                    'brand_orange' => ['type' => 'color', 'label' => 'Accent colour', 'default' => '#FF8200'],
                    'brand_blue' => ['type' => 'color', 'label' => 'Deep brand colour', 'default' => '#2C5697'],
                    'brand_ice' => ['type' => 'color', 'label' => 'Ice background', 'default' => '#EAEEF5'],
                    'seo_home_title' => ['type' => 'text', 'label' => 'Homepage title', 'default' => 'Sip & Smoke | Juice Bar, Desserts & Lounge · Auburn'],
                    'seo_home_description' => ['type' => 'textarea', 'label' => 'Homepage description', 'default' => 'Fresh juices, loaded shakes, desserts and shisha at Sip & Smoke, 56 Auburn Rd. Open 7 days until late. Call 0414 401 462 to book a table.'],
                    'seo_gallery_title' => ['type' => 'text', 'label' => 'Gallery title', 'default' => 'Gallery | Sip & Smoke · Auburn'],
                    'seo_gallery_description' => ['type' => 'textarea', 'label' => 'Gallery description', 'default' => 'All 89 photographs of Sip & Smoke, Auburn — stone-baked pizza, loaded shakes, desserts, the courtyard and the late-night lounge.'],
                    'seo_menu_title' => ['type' => 'text', 'label' => 'Menu title', 'default' => 'The Menu | Sip & Smoke · Auburn'],
                    'seo_menu_description' => ['type' => 'textarea', 'label' => 'Menu description', 'default' => 'The Sip & Smoke kitchen menu — burgers, wraps, pasta, Indo-Chinese, wings, pizzas, loaded fries, mains and sandwiches at 56 Auburn Rd, Auburn.'],
                    'seo_og_image' => ['type' => 'image', 'label' => 'Social share image', 'default' => 'assets/images/brand/heroimage.png', 'help' => 'Falls back to the hero image on the homepage.'],
                ],
            ],
            'contact' => [
                'label' => 'Contact & location',
                'note' => 'Phone, email, address, opening hours and map settings.',
                'fields' => [
                    'contact_phone' => ['type' => 'text', 'label' => 'Phone number (display)', 'default' => '0414 401 462'],
                    'contact_phone_href' => ['type' => 'text', 'label' => 'Phone link', 'default' => 'tel:+61414401462', 'help' => 'Use the international format, e.g. tel:+61414401462.'],
                    'contact_email' => ['type' => 'email', 'label' => 'Email address', 'default' => 'info@sipandsmoke.com.au'],
                    'contact_address_1' => ['type' => 'text', 'label' => 'Address line 1', 'default' => '56 Auburn Rd'],
                    'contact_address_2' => ['type' => 'text', 'label' => 'Address line 2', 'default' => 'Auburn NSW 2144'],
                    'contact_address_full' => ['type' => 'text', 'label' => 'Full address', 'default' => '56 Auburn Rd, Auburn NSW 2144'],
                    'contact_map_url' => ['type' => 'url', 'label' => 'Map / directions URL', 'default' => 'https://www.google.com/maps/search/?api=1&query=56+Auburn+Rd,+Auburn+NSW+2144'],
                    'contact_hours' => ['type' => 'text', 'label' => 'Short opening hours', 'default' => 'Open 7 days · until late'],
                    'contact_hours_days' => ['type' => 'text', 'label' => 'Opening days', 'default' => '7 days a week'],
                    'contact_hours_time' => ['type' => 'text', 'label' => 'Closing note', 'default' => 'until late'],
                    'contact_cta' => ['type' => 'text', 'label' => 'Call-to-action label', 'default' => 'Call to Book a Table'],
                    'contact_mobile_note' => ['type' => 'textarea', 'label' => 'Mobile menu contact note', 'default' => '56 Auburn Rd, Auburn NSW 2144'."\n".'Open 7 days · until late'],
                ],
            ],
            'navigation' => [
                'label' => 'Navigation',
                'note' => 'Header, mobile and footer navigation labels.',
                'fields' => [
                    'nav_home' => ['type' => 'text', 'label' => 'Home label', 'default' => 'HOME'],
                    'nav_menu' => ['type' => 'text', 'label' => 'Menu label', 'default' => 'THE MENU'],
                    'nav_juice' => ['type' => 'text', 'label' => 'Juice bar label', 'default' => 'JUICE BAR'],
                    'nav_about' => ['type' => 'text', 'label' => 'About label', 'default' => 'ABOUT US'],
                    'nav_contact' => ['type' => 'text', 'label' => 'Contact label', 'default' => 'CONTACT US'],
                    'nav_gallery' => ['type' => 'text', 'label' => 'Gallery label', 'default' => 'GALLERY'],
                    'footer_explore' => ['type' => 'text', 'label' => 'Footer explore heading', 'default' => 'Explore'],
                    'footer_visit' => ['type' => 'text', 'label' => 'Footer visit heading', 'default' => 'Visit'],
                ],
            ],
            'hero' => [
                'label' => 'Home hero',
                'note' => 'The first screen on the homepage.',
                'fields' => [
                    'home_hero_eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'Auburn · Juice bar · Dessert lounge'],
                    'home_hero_title_1' => ['type' => 'text', 'label' => 'Title line 1', 'default' => 'Sip slowly.'],
                    'home_hero_title_2' => ['type' => 'text', 'label' => 'Highlighted title line', 'default' => 'Stay late.'],
                    'home_hero_lede' => ['type' => 'textarea', 'label' => 'Intro copy', 'default' => 'Fresh-blended juices, loaded shakes and warm dessert-bar classics in the heart of Auburn. Pull up a seat.'],
                    'home_hero_cta' => ['type' => 'text', 'label' => 'Primary button label', 'default' => 'Call to Book a Table'],
                    'home_hero_secondary' => ['type' => 'text', 'label' => 'Secondary button label', 'default' => 'View the menu'],
                    'home_hero_meta_open' => ['type' => 'text', 'label' => 'Opening meta', 'default' => 'Open 7 days'],
                    'home_hero_meta_address' => ['type' => 'text', 'label' => 'Address meta', 'default' => '56 Auburn Rd, Auburn'],
                    'home_hero_image' => ['type' => 'image', 'label' => 'Hero image', 'default' => 'assets/images/brand/heroimage.png', 'help' => 'Landscape images work best. Uploads are converted to WebP.'],
                    'home_hero_alt' => ['type' => 'text', 'label' => 'Hero image alt text', 'default' => 'Mango juice being poured into a glass with fresh mango pieces on a marble counter'],
                    'home_ribbon' => ['type' => 'textarea', 'label' => 'Scrolling ribbon items', 'default' => "Fresh pressed\nLoaded shakes\nDessert bar\nShisha courtyard\nOpen 7 days", 'help' => 'One item per line.'],
                ],
            ],
            'home_sections' => [
                'label' => 'Home sections',
                'note' => 'Menu, juice bar and about copy blocks.',
                'fields' => [
                    'home_menu_eyebrow' => ['type' => 'text', 'label' => 'Menu eyebrow', 'default' => 'The menu'],
                    'home_menu_title_lead' => ['type' => 'text', 'label' => 'Menu title lead', 'default' => 'A whole menu,'],
                    'home_menu_title_mark' => ['type' => 'text', 'label' => 'Menu highlighted title', 'default' => 'one click away.'],
                    'home_menu_note' => ['type' => 'textarea', 'label' => 'Menu section note', 'default' => 'The current kitchen menu, page by page — burgers, wraps, pasta, Indo-Chinese plates, wings, pizzas, loaded fries, mains and sandwiches.'],
                    'home_menu_kicker' => ['type' => 'text', 'label' => 'Menu card kicker', 'default' => 'From the kitchen'],
                    'home_menu_title' => ['type' => 'text', 'label' => 'Menu card title', 'default' => 'Good food. Better vibes.'],
                    'home_menu_copy' => ['type' => 'textarea', 'label' => 'Menu card copy', 'default' => 'Smash burgers, loaded wraps, pasta, Indo-Chinese plates, wings and pizzas — cooked to order in the heart of Auburn.'],
                    'home_menu_cta' => ['type' => 'text', 'label' => 'Menu button label', 'default' => 'View the full menu'],
                    'home_menu_index' => ['type' => 'textarea', 'label' => 'Menu index lines', 'default' => "Burgers & wraps\nPasta & Indo-Chinese\nWings, pizzas & fries\nMains & sandwiches", 'help' => 'One line per row. Numbers are added automatically.'],
                    'home_menu_hint' => ['type' => 'text', 'label' => 'Deck hint', 'default' => 'Drag the page, tap to open, or use the arrow keys'],
                    'home_juice_eyebrow' => ['type' => 'text', 'label' => 'Juice bar eyebrow', 'default' => 'Juice bar'],
                    'home_juice_title_lead' => ['type' => 'text', 'label' => 'Juice bar title lead', 'default' => 'Poured fresh.'],
                    'home_juice_title_mark' => ['type' => 'text', 'label' => 'Juice bar highlighted title', 'default' => 'Made to order.'],
                    'home_juice_note' => ['type' => 'textarea', 'label' => 'Juice bar section note', 'default' => 'A first look at the juice bar. Fresh blends, shakes and dessert-bar favourites, made to order.'],
                    'home_filter_all' => ['type' => 'text', 'label' => '“All” filter label', 'default' => 'All'],
                    'home_filter_juice' => ['type' => 'text', 'label' => 'Juice filter label', 'default' => 'Juice bar'],
                    'home_filter_shakes' => ['type' => 'text', 'label' => 'Shakes filter label', 'default' => 'Shakes'],
                    'home_filter_desserts' => ['type' => 'text', 'label' => 'Desserts filter label', 'default' => 'Desserts'],
                    'home_count_label' => ['type' => 'text', 'label' => 'Item count suffix', 'default' => 'items'],
                    'home_about_eyebrow' => ['type' => 'text', 'label' => 'About eyebrow', 'default' => 'About us'],
                    'home_about_title_lead' => ['type' => 'text', 'label' => 'About title lead', 'default' => 'A neighbourhood spot for'],
                    'home_about_title_mark' => ['type' => 'text', 'label' => 'About highlighted title', 'default' => 'late plans.'],
                    'home_about_lead_1' => ['type' => 'textarea', 'label' => 'About paragraph 1', 'default' => "Sip & Smoke is a lively late-night hangout on Auburn Road. There's a juice bar for fresh blends, a counter for shakes and dessert-bar classics, and a relaxed outdoor courtyard for shisha."],
                    'home_about_lead_2' => ['type' => 'textarea', 'label' => 'About paragraph 2', 'default' => 'Everything starts with the drink: tropical fruit, creamy avocado, sugarcane and rich chocolate, blended to order. Stay for a plate, share a dessert, or settle into the courtyard with friends.'],
                    'home_about_image' => ['type' => 'image', 'label' => 'About image', 'default' => 'assets/images/brand/about-lounge.webp'],
                    'home_about_image_alt' => ['type' => 'text', 'label' => 'About image alt text', 'default' => 'The purple-lit lounge at Sip & Smoke with guests gathered around tables'],
                    'home_about_note_mark' => ['type' => 'text', 'label' => 'Image note', 'default' => 'Sip. Smoke. Unwind.'],
                    'home_about_note_small' => ['type' => 'text', 'label' => 'Image note subline', 'default' => 'Open 7 days · until late'],
                    'home_fact_1_label' => ['type' => 'text', 'label' => 'Fact 1 label', 'default' => 'Find us'],
                    'home_fact_1_value' => ['type' => 'text', 'label' => 'Fact 1 value', 'default' => '56 Auburn Rd, Auburn NSW 2144'],
                    'home_fact_2_label' => ['type' => 'text', 'label' => 'Fact 2 label', 'default' => 'Open'],
                    'home_fact_2_value' => ['type' => 'text', 'label' => 'Fact 2 value', 'default' => '7 days · until late'],
                    'home_fact_3_label' => ['type' => 'text', 'label' => 'Fact 3 label', 'default' => 'The room'],
                    'home_fact_3_value' => ['type' => 'text', 'label' => 'Fact 3 value', 'default' => 'Dine-in & shisha courtyard'],
                    'home_fact_4_label' => ['type' => 'text', 'label' => 'Fact 4 label', 'default' => 'Call'],
                    'home_fact_4_value' => ['type' => 'text', 'label' => 'Fact 4 value', 'default' => '0414 401 462'],
                ],
            ],
            'contact_section' => [
                'label' => 'Home contact',
                'note' => 'The closing contact panel on the homepage.',
                'fields' => [
                    'home_contact_eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'Contact us'],
                    'home_contact_title_1' => ['type' => 'text', 'label' => 'Title line 1', 'default' => 'Come sit'],
                    'home_contact_title_2' => ['type' => 'text', 'label' => 'Title line 2', 'default' => 'with us.'],
                    'home_contact_lede' => ['type' => 'textarea', 'label' => 'Intro copy', 'default' => 'Booking a table or planning a late one? Call us directly. For everything else, the details are right here.'],
                    'home_contact_note' => ['type' => 'text', 'label' => 'Call note', 'default' => '0414 401 462 · open 7 days until late'],
                    'home_contact_call_label' => ['type' => 'text', 'label' => 'Call label', 'default' => 'Call'],
                    'home_contact_email_label' => ['type' => 'text', 'label' => 'Email label', 'default' => 'Email'],
                    'home_contact_visit_label' => ['type' => 'text', 'label' => 'Visit label', 'default' => 'Visit'],
                    'home_contact_open_label' => ['type' => 'text', 'label' => 'Open label', 'default' => 'Open'],
                    'home_contact_map_label' => ['type' => 'text', 'label' => 'Directions label', 'default' => 'Get directions'],
                ],
            ],
            'menu_page' => [
                'label' => 'Menu page',
                'note' => 'Heading and supporting copy on the dedicated menu page.',
                'fields' => [
                    'menu_page_eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'The menu'],
                    'menu_page_title_lead' => ['type' => 'text', 'label' => 'Title lead', 'default' => 'The'],
                    'menu_page_title_mark' => ['type' => 'text', 'label' => 'Highlighted title', 'default' => 'kitchen menu.'],
                    'menu_page_note' => ['type' => 'textarea', 'label' => 'Supporting note', 'default' => 'Burgers, wraps, pasta, Indo-Chinese plates, wings, pizzas, loaded fries, mains and sandwiches. Every size and price is exactly as printed.'],
                    'menu_page_toolbar_left' => ['type' => 'text', 'label' => 'Toolbar left label', 'default' => 'Page by page'],
                    'menu_page_toolbar_right' => ['type' => 'text', 'label' => 'Toolbar right label', 'default' => 'Prices in AUD'],
                ],
            ],
            'gallery_page' => [
                'label' => 'Gallery page',
                'note' => 'Heading, count labels and fallback copy for the gallery.',
                'fields' => [
                    'gallery_eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'Gallery'],
                    'gallery_title_lead' => ['type' => 'text', 'label' => 'Title lead', 'default' => 'A look'],
                    'gallery_title_mark' => ['type' => 'text', 'label' => 'Highlighted title', 'default' => 'inside.'],
                    'gallery_note' => ['type' => 'textarea', 'label' => 'Supporting note', 'default' => 'Every frame from the shoot: 89 photographs of the courtyard, the plates, the drinks and the late-night crowd. Browse 24 at a time and open any photo full size.'],
                    'gallery_count_label' => ['type' => 'text', 'label' => 'Collection label', 'default' => 'Kitchen · Bar · Lounge'],
                    'gallery_noscript' => ['type' => 'text', 'label' => 'No-JavaScript note', 'default' => 'Pagination needs JavaScript. All photographs are shown above.'],
                ],
            ],
            'footer' => [
                'label' => 'Footer',
                'note' => 'Footer description and legal line.',
                'fields' => [
                    'footer_blurb' => ['type' => 'textarea', 'label' => 'Footer description', 'default' => 'A juice bar, dessert lounge and shisha courtyard in Auburn. Open seven days, until late.'],
                    'footer_copyright' => ['type' => 'text', 'label' => 'Copyright line', 'default' => '© 2026 Sip & Smoke · Auburn'],
                    'footer_tagline' => ['type' => 'text', 'label' => 'Footer tagline', 'default' => 'Sip. Smoke. Unwind.'],
                ],
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function fields(): array
    {
        $fields = [];

        foreach (self::groups() as $group) {
            foreach ($group['fields'] as $key => $field) {
                $fields[$key] = $field;
            }
        }

        return $fields;
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return array_map(fn (array $field) => $field['default'], self::fields());
    }

    public static function get(string $key, mixed $fallback = null): mixed
    {
        return Setting::get($key, $fallback ?? (self::defaults()[$key] ?? null));
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        return array_merge(self::defaults(), Setting::values());
    }

    public static function imageFields(): array
    {
        return array_keys(array_filter(self::fields(), fn (array $field) => ($field['type'] ?? null) === 'image'));
    }
}
