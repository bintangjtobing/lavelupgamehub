# SEO publishing

LevelUp Market publishes one canonical origin from `SEO_URL`. Production should keep:

```dotenv
SEO_URL=https://levelupgamehub.com
```

Canonical links, Open Graph URLs, structured data, the sitemap, `robots.txt`, and `llms.txt` all use this value. Incoming hosts and query strings do not change canonical URLs.

## Head integration

The shared layout should keep its charset, viewport, CSRF, theme, and analytics markup, remove the old title/description/canonical/Open Graph/Twitter/favicon blocks, and include this once inside `<head>`:

```blade
@include('partials.seo-head')
```

The partial identifies the eight static pages by path. On a product detail page it uses the existing `$item` `CatalogItem` variable. It emits the title, description, robots directive, canonical URL, Open Graph and Twitter large-image tags, icon links, and JSON-LD.

The shared social image uses the original Cloudinary brand artwork configured in `seo.social_image`, at its actual 1640 × 924 PNG dimensions. The earlier generated social card remains unused. The expected icon files are `/favicon.ico`, `/favicon.svg`, `/favicon-32x32.png`, `/favicon-96x96.png`, `/apple-touch-icon.png`, and `/site.webmanifest`.

## Structured data

Every public page describes the LevelUp Market brand as an `Organization`, plus its `WebSite`, current `WebPage`, and breadcrumbs. The organization links only to the Facebook and Instagram profiles already published in the site footer. A product-detail URL is modeled as a `CollectionPage` about a neutral catalog-group `Thing`, because one group can contain many SKUs and the site does not hold a defensible local offer. It makes no price, rating, or official publisher-relationship claim.

## Discovery files

- `/sitemap.xml` is generated from the eight static pages and all locally active catalog items. It omits pagination URLs and `lastmod` because the catalog sync time does not prove that page content changed.
- `/robots.txt` allows crawling and points to the canonical sitemap.
- `/llms.txt` is a factual Markdown directory of the static pages and active catalog. Google explicitly says it does not use `llms.txt` for Search or its generative AI features; this file is provided only as a general machine-readable directory and gives no indexing guarantee.

After deployment, submit `https://levelupgamehub.com/sitemap.xml` in Google Search Console and validate representative pages with Google Rich Results Test and a social sharing debugger. Search engines decide when and whether to recrawl or display metadata.

## References

- [Google: General structured data guidelines](https://developers.google.com/search/docs/appearance/structured-data/sd-policies)
- [Google: Organization structured data](https://developers.google.com/search/docs/appearance/structured-data/organization)
- [Google: Build and submit a sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
- [Google: Generative AI features and `llms.txt`](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide)

## Assets and response integrity

The local logo retains its original 2125 × 750 pixels. OG/Twitter use the original
1640 × 924 CDN artwork. Platform icons are size conversions of the original
1000 × 1000 logo in `public/images/brand/favicon-original.png`; the SVG icon
embeds that same original artwork. PNG fallbacks include 16/32/48/96 pixels, Apple
180, and Android 192/512. ICO includes the 16/32/48 images. These device-specific
exports are not claims that small catalog covers contain additional detail.

Legacy response-minification middleware was removed from the HTTP stack because
it treated JSON-LD as executable JavaScript and obfuscated it into invalid JSON.
Regression tests parse structured data from real rendered page responses.

## Keywords

`config/seo_keywords.php` supplies page-specific keywords and product copy. Meta keywords are included on request but Google ignores them for ranking; useful terms also appear naturally in titles, descriptions and visible product introductions. See [keyword research](keyword-research.md) for sources and limitations.
