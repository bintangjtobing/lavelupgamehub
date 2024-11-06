@extends('welcome')
@section('title', 'Privacy Policy')

@push('css')
@endpush

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Privacy Policy</h1>
    <p>Effective Date: {{ date('F d, Y') }}</p>

    <h2>Introduction</h2>
    <p>At LevelUp Gaming Market, we are committed to protecting your privacy and ensuring that your personal information
        is handled in a safe and responsible manner. This Privacy Policy explains what personal data we collect, how we
        use it, and your rights regarding your information.</p>

    <h2>Information We Collect</h2>
    <ul>
        <li><strong>Personal Identification Information:</strong> Name, email address, phone number, etc., collected
            when you create an account or make a purchase.</li>
        <li><strong>Transaction Data:</strong> Details about purchases you make on our platform.</li>
        <li><strong>Usage Data:</strong> Information on how you interact with our website, including IP address, browser
            type, and pages visited.</li>
    </ul>

    <h2>How We Use Your Information</h2>
    <p>We use your information to:</p>
    <ul>
        <li>Process your orders and deliver purchased products.</li>
        <li>Provide customer support and respond to inquiries.</li>
        <li>Improve our website and services.</li>
        <li>Send you promotional information, if you have opted-in to receive it.</li>
    </ul>

    <h2>Sharing Your Information</h2>
    <p>We may share your data with:</p>
    <ul>
        <li>Service providers who assist with our business operations.</li>
        <li>Law enforcement if required by law or to protect our rights.</li>
    </ul>

    <h2>Your Rights</h2>
    <p>You have the right to access, update, or delete your personal information. Please contact us if you wish to
        exercise these rights.</p>

    <h2>Changes to This Privacy Policy</h2>
    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy
        Policy on this page.</p>

    <h2>Contact Us</h2>
    <p>If you have any questions about this Privacy Policy, please contact us at support@levelupgamingmarket.com.</p>
</div>
@endsection
@push('script')
@endpush