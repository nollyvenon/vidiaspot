@extends('static_pages.base')

@section('title', App\Models\StaticPage::getTitleByKey('privacy_policy', 'en', 'Privacy Policy'))
@section('meta_description', 'Read our privacy policy to understand how we collect, use, and protect your personal information on VidiaSpot Marketplace.')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-user-shield text-success"></i>
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">Privacy Policy</h1>
                            <p class="text-muted mb-0">Last updated: {{ date('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="static-page-content">
                        @php
                            $privacyPage = App\Models\StaticPage::where('page_key', 'privacy_policy')->where('locale', 'en')->where('status', 'active')->first();
                            echo $privacyPage ? $privacyPage->content : '
                            <p>At VidiaSpot Marketplace, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website, use our mobile applications, or participate in our services.</p>
                            
                            <h3>Information We Collect</h3>
                            <p>We may collect and process the following information about you:</p>
                            
                            <h4>Information You Give Us</h4>
                            <ul>
                                <li>Name, email address, phone number, and location information</li>
                                <li>Profile information and preferences</li>
                                <li>Information about your ads, purchases, and sales</li>
                                <li>Payment information for transactions</li>
                                <li>Communication preferences</li>
                            </ul>
                            
                            <h4>Information We Collect Automatically</h4>
                            <ul>
                                <li>IP address and browser information</li>
                                <li>Information about your visits to our site and use of our services</li>
                                <li>Information about your device and how you interact with our apps</li>
                                <li>Geolocation data when you use location-based services</li>
                                <li>Log information such as access times and page views</li>
                            </ul>
                            
                            <h3>How We Use Your Information</h3>
                            <p>We use your information to:</p>
                            <ul>
                                <li>Provide, maintain, and improve our services</li>
                                <li>Process transactions and send related communications</li>
                                <li>Send you technical notices and support messages</li>
                                <li>Facilitate communication between users</li>
                                <li>Personalize your experience on our platform</li>
                                <li>Analyze usage patterns to improve our services</li>
                            </ul>
                            
                            <h3>Location Information</h3>
                            <p>When you use our location-based services for finding products near you or posting farm products, we may collect and use your precise or approximate location data. You can control location sharing via your device settings.</p>
                            
                            <h3>Farm Product Information</h3>
                            <p>When you list farm products, we may collect additional information about your farm including farm location, farming practices, organic certifications, harvest dates, and sustainability metrics. This information helps buyers make informed decisions about farm products.</p>
                            
                            <h3>Data Protection</h3>
                            <p>We implement appropriate technical and organizational measures to ensure a level of security appropriate to the risk, including:</p>
                            <ul>
                                <li>Secure transmission of data using HTTPS/TLS</li>
                                <li>Encryption of sensitive data stored in our databases</li>
                                <li>Regular security audits and updates</li>
                                <li>Access controls limiting who can access personal data</li>
                                <li>Employee training on data protection practices</li>
                            </ul>
                            
                            <h3>Sharing of Information</h3>
                            <p>We may share your information:</p>
                            <ul>
                                <li>With other users when necessary for transactions</li>
                                <li>With third parties for service provision (payment processors, delivery services)</li>
                                <li>With law enforcement when required by law</li>
                                <li>With affiliates and partners for legitimate business purposes</li>
                                <li>When necessary to protect our rights or safety</li>
                            </ul>
                            
                            <h3>Your Rights</h3>
                            <p>You have the right to:</p>
                            <ul>
                                <li>Access the personal information we hold about you</li>
                                <li>Correct inaccurate personal information</li>
                                <li>Delete your personal information in certain circumstances</li>
                                <li>Restrict or object to processing of your information</li>
                                <li>Data portability in certain situations</li>
                                <li>Withdraw consent where we rely on consent</li>
                            </ul>
                            
                            <h3>Cookies and Similar Technologies</h3>
                            <p>We use cookies and similar technologies to enhance user experience, analyze site traffic, and provide personalized content. You can control cookie settings through your browser.</p>
                            
                            <h3>Children\'s Privacy</h3>
                            <p>Our services are not intended for children under 13. We do not knowingly collect personal information from children under 13.</p>
                            
                            <h3>Changes to This Policy</h3>
                            <p>We may update this privacy policy periodically. We will notify you of any material changes through our app or website.</p>
                            
                            <h3>Contact Us</h3>
                            <p>If you have questions about this privacy policy, please contact us at:</p>
                            <ul>
                                <li>Email: <a href="mailto:privacy@vidiaspot.ng">privacy@vidiaspot.ng</a></li>
                                <li>Phone: +234 800 000 0000</li>
                                <li>Address: 1234 Innovation Hub, Victoria Island, Lagos, Nigeria</li>
                            </ul>
                            ';
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection