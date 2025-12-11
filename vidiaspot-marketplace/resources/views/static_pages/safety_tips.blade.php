@extends('static_pages.base')

@section('title', App\Models\StaticPage::getTitleByKey('safety_tips', 'en', 'Safety Tips'))
@section('meta_description', 'Learn about safety tips for buying and selling on VidiaSpot Marketplace. Stay safe with our guidelines for online and offline transactions.')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-shield-alt text-success"></i>
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">Safety Tips</h1>
                            <p class="text-muted mb-0">Stay safe while buying and selling</p>
                        </div>
                    </div>
                    
                    <div class="static-page-content">
                        @php
                            $safetyPage = App\Models\StaticPage::where('page_key', 'safety_tips')->where('locale', 'en')->where('status', 'active')->first();
                            echo $safetyPage ? $safetyPage->content : '
                            <h2>Staying Safe on VidiaSpot Marketplace</h2>
                            <p>At VidiaSpot, your safety is our priority. Follow these tips to have a secure and positive experience when buying or selling.</p>
                            
                            <h3>Online Safety</h3>
                            <ul>
                                <li>Always verify account information before engaging in transactions</li>
                                <li>Use the official messaging system in the app/website to communicate</li>
                                <li>Never share personal financial information via messages</li>
                                <li>Be wary of deals that seem too good to be true</li>
                                <li>Check seller ratings and reviews before purchasing</li>
                                <li>Use secure payment methods provided on the platform</li>
                            </ul>
                            
                            <h3>In-Person Meeting Safety</h3>
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light p-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-3 mt-1 fa-lg"></i>
                                            <div>
                                                <h5>Safe Meeting Places</h5>
                                                <p>Meet in well-lit public places, preferably during daylight hours. Avoid meeting at isolated locations.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light p-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-3 mt-1 fa-lg"></i>
                                            <div>
                                                <h5>Bring Someone Along</h5>
                                                <p>When possible, bring a friend or family member to meetups for added security.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3>For Sellers</h3>
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-user-secret text-success me-2"></i> Protect Your Information</h5>
                                            <p>Do not provide your home address or personal phone number until you feel comfortable with the buyer.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-lock text-success me-2"></i> Verify Buyers</h5>
                                            <p>Ask buyers for their contact information and verify their identity before arranging delivery.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-truck text-success me-2"></i> Safe Delivery Practices</h5>
                                            <p>For deliveries, use secure payment methods and verify the recipient identity.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-mobile-alt text-success me-2"></i> Use App Features</h5>
                                            <p>Utilize the app features for secure transactions and communication.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3>For Buyers</h3>
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-search text-success me-2"></i> Verify Sellers</h5>
                                            <p>Always check seller ratings and profiles before making purchases. Look for verified badges.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-money-check-alt text-success me-2"></i> Secure Payments</h5>
                                            <p>Use secure payment methods provided on the platform instead of cash payments.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-question-circle text-success me-2"></i> Ask Questions</h5>
                                            <p>Before purchasing, ask detailed questions about the product condition and history.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fas fa-clipboard-check text-success me-2"></i> Inspect Before Buying</h5>
                                            <p>When possible, inspect products in person before finalizing the purchase.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3>Farm Product Safety</h3>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-leaf me-2"></i> Special Safety Measures for Farm Products</h5>
                                <ul class="mb-0">
                                    <li>Verify the farm location and practices when buying organic products</li>
                                    <li>Check the harvest date and freshness days for farm products</li>
                                    <li>Ask about post-harvest handling and storage conditions</li>
                                    <li>For farm tours, verify the farmer identity and schedule during daylight hours</li>
                                    <li>Check food handling certifications for dairy, meat and poultry products</li>
                                    <li>Verify transportation and delivery methods for perishable farm products</li>
                                </ul>
                            </div>
                            
                            <h3>Reporting Unsafe Behavior</h3>
                            <p>If you encounter any suspicious activity or feel unsafe during a transaction:</p>
                            <ul>
                                <li>Use the "Report" button on any ad or profile to alert our team</li>
                                <li>Contact our customer support at <a href="mailto:support@vidiaspot.ng">support@vidiaspot.ng</a></li>
                                <li>Call our safety line at <strong>+234 800 000 0000</strong></li>
                                <li>Block and report users who engage in unsafe behavior</li>
                            </ul>
                            
                            <div class="card border-0 bg-light p-4 mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle text-warning fa-3x me-3"></i>
                                    <div>
                                        <h5>Emergency Information</h5>
                                        <p class="mb-1">If you feel threatened or unsafe:</p>
                                        <ul class="mb-0">
                                            <li>Call local emergency services (Police: 112, Ambulance: 112)</li>
                                            <li>Contact our customer support immediately</li>
                                            <li>Provide details about the incident for investigation</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            ';
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection