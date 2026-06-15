# Hyvä Recently Viewed

A lightweight, Hyvä-native "recently viewed products" module for Magento 2 / Adobe Commerce. Tracks viewed products entirely client-side with localStorage and renders them with Alpine.js -- no server-side session, no customer-data dependency.

## Why this exists

Magento's stock "recently viewed" widget depends on the Reports module and the Luma customer-data / sections JavaScript layer. Hyvä deliberately strips out that Knockout/customer-data stack, so the native widget doesn't render on a Hyvä storefront.

This module fills that gap with an approach that fits Hyvä's architecture:

- **Client-side, not server-side.** Viewed products are stored in the browser's localStorage, so there's no per-request server work and nothing to invalidate in full-page cache.
- **Alpine.js, not Knockout.** Rendering uses the Alpine layer Hyvä already ships, so there's no extra JS framework added to the page.
- **Self-contained.** One ViewModel, one template, one layout file. No dependency on Reports or customer-data.

## Features

- Tracks recently viewed products per browser via localStorage
- De-duplicates by product ID and orders most-recent-first
- Caps the stored list at a set maximum (default: 5)
- Renders on the product page after the related-products block
- No server-side session, observer, or customer-data section
- Exposes full product data (including price) to the template, so you control which fields display

## Requirements

- Magento 2.4.x
- A Hyvä theme
- PHP 8.1+

## Installation

### Manual (app/code)

```bash
# from your Magento root
mkdir -p app/code/Jack/RecentlyViewed
```

Copy the module files into `app/code/Jack/RecentlyViewed`, then:

```bash
bin/magento module:enable Jack_RecentlyViewed
bin/magento setup:upgrade
bin/magento cache:flush
```

For production, also run:

```bash
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```

### Composer (optional, if published)

```bash
composer require jack/module-recently-viewed
bin/magento module:enable Jack_RecentlyViewed
bin/magento setup:upgrade
```

## How it works

A ViewModel (`Jack\RecentlyViewed\ViewModel\CurrentProduct`) resolves the product on the current page and exposes its ID, name, URL, image, and price. The template serializes that product into the page and hands it to a small Alpine component. On load, the component reads the existing list from localStorage, adds the current product to the front, removes any duplicate of the same ID, trims the list to the maximum length, and writes it back. The stored list is rendered as a row of product cards with `x-for`.

The tracking block is attached to `catalog_product_view`, after the related-products block, via layout XML.

## Configuration

The component is intentionally simple. The two values you're most likely to change live in the template's Alpine `x-data`:

- `maxItems` - how many products to keep (default 5)
- the localStorage key - `recently_viewed`

Placement is controlled in `view/frontend/layout/catalog_product_view.xml`; move the block to render the component elsewhere on the page.

## Notes & roadmap

- The ViewModel returns a price field the default template doesn't render - it's exposed so you can add price display without touching PHP. Drop it from the ViewModel if you don't need it.
- `maxItems` and the storage key are currently template-level constants. Promoting them to admin settings via `system.xml` is the obvious next step.
- The component requires JavaScript and has no server-rendered fallback - appropriate for a recently-viewed list, which is inherently per-browser.

## License

MIT - see [LICENSE](LICENSE).
