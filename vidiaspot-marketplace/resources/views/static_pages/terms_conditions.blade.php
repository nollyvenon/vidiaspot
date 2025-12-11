@extends('static_pages.base')

@section('title', App\Models\StaticPage::getTitleByKey('terms_conditions', 'en', 'Terms and Conditions'))
@section('meta_description', 'Read our terms and conditions for using VidiaSpot Marketplace. Understand your rights and responsibilities as a user of our platform.')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-file-contract text-success"></i>
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">Terms and Conditions</h1>
                            <p class="text-muted mb-0">Last updated: {{ date('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="static-page-content">
                        @php
                            $termsPage = App\Models\StaticPage::where('page_key', 'terms_conditions')->where('locale', 'en')->where('status', 'active')->first();
                            echo $termsPage ? $termsPage->content : '
                            <p>Welcome to VidiaSpot Marketplace. These terms and conditions outline the rules and regulations for the use of VidiaSpot NG\'s website and mobile applications.</p>
                            
                            <h3>Acceptance of Terms</h3>
                            <p>By accessing this platform, you accept these terms and conditions. Do not continue to use VidiaSpot if you do not agree to all provisions of this agreement.</p>
                            
                            <h3>License to Use Platform</h3>
                            <p>Unless otherwise stated, VidiaSpot and its licensors own the intellectual property rights for all material on VidiaSpot. All intellectual property rights are reserved.</p>
                            
                            <h3>User Responsibilities</h3>
                            <h4>Posting Ads</h4>
                            <ul>
                                <li>Users must be at least 18 years old to post ads</li>
                                <li>All content posted must be accurate and truthful</li>
                                <li>Sellers are responsible for the accuracy of farm product information</li>
                                <li>Users must comply with all applicable laws and regulations</li>
                                <li>Farm product sellers must provide accurate information about harvest dates, farming practices, and certifications</li>
                                <li>No illegal, stolen, or prohibited items may be listed</li>
                            </ul>
                            
                            <h4>Buying and Selling</h4>
                            <ul>
                                <li>Users are solely responsible for their transactions</li>
                                <li>Buyers and sellers must comply with local laws regarding sales</li>
                                <li>For farm products, users acknowledge that quality may vary based on natural factors</li>
                                <li>Buyers are responsible for inspecting farm products before finalizing purchases</li>
                                <li>Both parties must communicate respectfully and professionally</li>
                                <li>Users are responsible for secure payment and delivery arrangements</li>
                            </ul>
                            
                            <h3>Farm Product Specific Terms</h3>
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light p-3">
                                        <h4><i class="fas fa-leaf text-success me-2"></i> Freshness Guarantee</h4>
                                        <p>While sellers are encouraged to provide fresh products, natural variation occurs. Buyers should inspect farm products at the time of delivery.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light p-3">
                                        <h4><i class="fas fa-certificate text-success me-2"></i> Certification Claims</h4>
                                        <p>Sellers claiming organic or other certifications must be able to provide proof of certification upon request.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <h3>Intellectual Property Rights</h3>
                            <p>All trademarks, service marks, brand names, logos, and intellectual property on this site remain the property of their respective owners.</p>
                            
                            <h3>Disclaimer and Limitations</h3>
                            <p>The platform is provided "as is" without any warranties. VidiaSpot is not liable for any loss or damages arising from the use of our services.</p>
                            
                            <h3>Product Liability</h3>
                            <p>Sellers are solely responsible for their products and any claims arising from their use. For farm products, buyers acknowledge that natural variations in quality may occur.</p>
                            
                            <h3>Delivery and Shipping</h3>
                            <p>We do not guarantee delivery times. Delivery arrangements are between buyers and sellers. Users are responsible for understanding and complying with local delivery regulations.</p>
                            
                            <h3>Payment Terms</h3>
                            <ul>
                                <li>All payments are between buyers and sellers</li>
                                <li>VidiaSpot is not responsible for payment disputes</li>
                                <li>For farm products, payment terms should be agreed upon before delivery</li>
                                <li>Users should use verified payment methods</li>
                                <li>Sellers must provide valid tax information where required</li>
                            </ul>
                            
                            <h3>Refund and Return Policy</h3>
                            <p>Returns and refunds are governed by agreements between individual buyers and sellers. VidiaSpot does not facilitate returns except where specifically covered by our escrow or protection services.</p>
                            <p>Farm products are generally non-refundable after inspection and acceptance by the buyer, due to their perishable nature.</p>
                            
                            <h3>Prohibited Activities</h3>
                            <ul>
                                <li>Posting false, misleading, or fraudulent information</li>
                                <li>Selling illegal or prohibited items</li>
                                <li>Violating copyright or trademark rights</li>
                                <li>Harassing other users</li>
                                <li>Using the service for commercial spam</li>
                                <li>Misrepresenting farm product origins or certifications</li>
                                <li>Providing false location information for proximity searches</li>
                            </ul>
                            
                            <h3>Account Termination</h3>
                            <p>We may terminate or suspend your account immediately without prior notice for violations of these terms or for other conduct that we determine is harmful to other users or to us.</p>
                            
                            <h3>Governing Law</h3>
                            <p>These terms shall be governed by and construed in accordance with the laws of Nigeria, without regard to its conflict of law provisions.</p>
                            
                            <h3>Dispute Resolution</h3>
                            <p>In the event of disputes between users, we encourage resolution through direct communication. Our mediation services may be requested for farm product disputes.</p>
                            
                            <h3>Changes to Terms</h3>
                            <p>We reserve the right to modify these terms at any time. Continued use of the platform constitutes acceptance of modified terms.</p>
                            
                            <h3>Contact Information</h3>
                            <p>If you have any questions about these Terms and Conditions, please contact us:</p>
                            <ul>
                                <li>Email: <a href="mailto:legal@vidiaspot.ng">legal@vidiaspot.ng</a></li>
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