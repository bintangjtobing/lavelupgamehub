<?php

namespace App\Services\Analytics;

use Illuminate\Http\Request;

/*
 * Mengenali perayap dan pemindai otomatis dari user agent.
 *
 * Tujuan utamanya kebersihan data, bukan pertahanan. Googlebot menyebut
 * dirinya apa adanya, jadi pengenalan lewat user agent sudah cukup untuk
 * memisahkannya dari pengunjung sungguhan. Sebaliknya, penyerang yang serius
 * akan menyamar sebagai peramban biasa -- jadi kelas ini TIDAK boleh dipakai
 * sebagai lapisan keamanan. Untuk itu ada pembatasan laju dan verifikasi
 * tanda tangan di tempat masing-masing.
 *
 * Bot tetap dicatat, hanya ditandai. Menghapusnya berarti membuang bukti
 * saat menelusuri lonjakan trafik yang aneh.
 */
class BotDetector
{
    /**
     * Kata kunci pada user agent beserta nama yang ditampilkan.
     * Diperiksa berurutan, yang lebih spesifik didahulukan.
     */
    protected const SIGNATURES = [
        'googlebot' => 'Googlebot',
        'google-inspectiontool' => 'Google Inspection',
        'storebot-google' => 'Googlebot Store',
        'adsbot-google' => 'Googlebot Ads',
        'mediapartners-google' => 'Googlebot AdSense',
        'bingbot' => 'Bingbot',
        'yandexbot' => 'YandexBot',
        'duckduckbot' => 'DuckDuckBot',
        'baiduspider' => 'Baidu',
        'applebot' => 'Applebot',
        'petalbot' => 'PetalBot',
        'ahrefsbot' => 'Ahrefs',
        'semrushbot' => 'SemrushBot',
        'mj12bot' => 'Majestic',
        'dotbot' => 'DotBot',
        'gptbot' => 'GPTBot',
        'oai-searchbot' => 'OpenAI Search',
        'chatgpt-user' => 'ChatGPT',
        'claudebot' => 'ClaudeBot',
        'perplexitybot' => 'PerplexityBot',
        'ccbot' => 'Common Crawl',
        'bytespider' => 'ByteSpider',
        'facebookexternalhit' => 'Facebook',
        'facebookcatalog' => 'Facebook Catalog',
        'twitterbot' => 'Twitterbot',
        'linkedinbot' => 'LinkedInBot',
        'whatsapp' => 'WhatsApp',
        'telegrambot' => 'TelegramBot',
        'slackbot' => 'Slackbot',
        'discordbot' => 'Discordbot',
        'pinterest' => 'Pinterest',
        'uptimerobot' => 'UptimeRobot',
        'pingdom' => 'Pingdom',
        'statuscake' => 'StatusCake',
        'lighthouse' => 'Lighthouse',
        'pagespeed' => 'PageSpeed',
        'headlesschrome' => 'Headless Chrome',
        'phantomjs' => 'PhantomJS',
        'puppeteer' => 'Puppeteer',
        'playwright' => 'Playwright',
        'selenium' => 'Selenium',
        'curl/' => 'curl',
        'wget' => 'wget',
        'python-requests' => 'python-requests',
        'python-urllib' => 'python-urllib',
        'go-http-client' => 'Go HTTP',
        'java/' => 'Java',
        'okhttp' => 'OkHttp',
        'axios/' => 'axios',
        'postman' => 'Postman',
        'insomnia' => 'Insomnia',
        'scrapy' => 'Scrapy',
        'httrack' => 'HTTrack',
    ];

    /**
     * Pola umum untuk perayap yang tidak masuk daftar di atas.
     */
    protected const GENERIC = '/\b(bot|crawler|spider|crawl|scraper|monitor|fetcher|archiver|validator|preview)\b/i';

    /**
     * @return array{0: bool, 1: string|null} [apakah bot, nama bot]
     */
    public function inspect(Request $request): array
    {
        $agent = trim((string) $request->userAgent());

        // User agent kosong hampir selalu skrip, bukan peramban.
        if ($agent === '') {
            return [true, 'tanpa user agent'];
        }

        $lower = mb_strtolower($agent);

        foreach (self::SIGNATURES as $needle => $name) {
            if (str_contains($lower, $needle)) {
                return [true, $name];
            }
        }

        if (preg_match(self::GENERIC, $lower)) {
            return [true, 'lainnya'];
        }

        return [false, null];
    }

    public function isBot(Request $request): bool
    {
        return $this->inspect($request)[0];
    }
}
