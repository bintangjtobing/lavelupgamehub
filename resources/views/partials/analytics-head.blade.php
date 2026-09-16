@php
    $trackingEnabled = config('analytics.enabled')
        && !request()->is('track-order')
        && in_array(request()->getHost(), config('analytics.production_hosts', []), true);
    $analyticsSettings = [
        'enabled' => $trackingEnabled,
        'measurementId' => config('analytics.measurement_id'),
        'debug' => (bool) config('analytics.debug'),
        'successEvent' => in_array(session('analytics_event'), ['generate_lead', 'review_submit'], true)
            ? session('analytics_event') : null,
    ];
@endphp
<script>
    window.levelupAnalyticsConfig = {{ Illuminate\Support\Js::from($analyticsSettings) }};
</script>
@if ($trackingEnabled && preg_match('/^G-[A-Z0-9]+$/', config('analytics.measurement_id', '')))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        // Exclude arbitrary query strings and fragments from analytics URLs.
        let cleanReferrer = '';
        try { cleanReferrer = document.referrer ? new URL(document.referrer).origin + '/' : ''; } catch (_) {}
        const campaign = {};
        const query = new URLSearchParams(location.search);
        ['source', 'medium', 'name', 'id', 'content', 'term'].forEach(key => {
            const value = query.get(key === 'name' ? 'utm_campaign' : 'utm_' + key);
            if (value && /^[a-z0-9_.-]{1,80}$/i.test(value) && !/\d{8,}/.test(value)) {
                campaign['campaign_' + key] = value;
            }
        });
        gtag('config', window.levelupAnalyticsConfig.measurementId, {
            ...campaign,
            page_location: location.origin + location.pathname,
            page_referrer: cleanReferrer,
            debug_mode: window.levelupAnalyticsConfig.debug,
            allow_google_signals: false,
            allow_ad_personalization_signals: false
        });
    </script>
    @if (config('analytics.meta_pixel_id'))
        <script>
            if (!location.search && !location.hash) {
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
            (window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', {{ Illuminate\Support\Js::from(config('analytics.meta_pixel_id')) }});
            fbq('track', 'PageView');
            }
        </script>
    @endif
    @if (config('analytics.clarity_id'))
        <script>
            if (!location.search && !location.hash) {
            (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})
            (window,document,'clarity','script',{{ Illuminate\Support\Js::from(config('analytics.clarity_id')) }});
            }
        </script>
    @endif
@endif
