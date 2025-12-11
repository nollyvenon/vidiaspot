@extends('static_pages.base')

@section('title', App\Models\StaticPage::getTitleByKey('faq', 'en', 'Frequently Asked Questions'))
@section('meta_description', 'Find answers to frequently asked questions about VidiaSpot Marketplace. Learn how to buy, sell, and use our farm product features.')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-question-circle text-success"></i>
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">Frequently Asked Questions</h1>
                            <p class="text-muted mb-0">Find answers to common questions</p>
                        </div>
                    </div>
                    
                    <div class="static-page-content">
                        @php
                            $faqPage = App\Models\StaticPage::where('page_key', 'faq')->where('locale', 'en')->where('status', 'active')->first();
                            echo $faqPage ? $faqPage->content : '
                            <h3>General Questions</h3>
                            
                            <div class="accordion" id="generalFaqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            What is VidiaSpot Marketplace?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#generalFaqAccordion">
                                        <div class="accordion-body">
                                            <p>VidiaSpot is a comprehensive marketplace platform that connects buyers and sellers. We specialize in classified ads, food vending, and direct farm-to-consumer sales, allowing you to buy and sell items near you.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            How do I create an account?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#generalFaqAccordion">
                                        <div class="accordion-body">
                                            <p>To create an account:</p>
                                            <ol>
                                                <li>Click the "Sign Up" button at the top right of the homepage</li>
                                                <li>Enter your name, email address, and create a password</li>
                                                <li>Verify your email address using the link sent to your inbox</li>
                                                <li>Complete your profile with location and preferences</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Is there a fee to use VidiaSpot?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#generalFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Posting advertisements is generally free. However, we offer premium features and listings for a fee. Additionally, we charge a small commission on successful transactions for certain categories, including farm products.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="mt-5">Farm Product Questions</h3>
                            
                            <div class="accordion" id="farmFaqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFarmOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFarmOne" aria-expanded="true" aria-controls="collapseFarmOne">
                                            What are Direct Farm Products?
                                        </button>
                                    </h2>
                                    <div id="collapseFarmOne" class="accordion-collapse collapse show" aria-labelledby="headingFarmOne" data-bs-parent="#farmFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Direct Farm Products are items sold directly from local farmers to consumers without intermediaries. This includes vegetables, fruits, dairy, poultry, eggs, and other agricultural products that come directly from the farm to you.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFarmTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFarmTwo" aria-expanded="false" aria-controls="collapseFarmTwo">
                                            How do I know if a product is truly organic?
                                        </button>
                                    </h2>
                                    <div id="collapseFarmTwo" class="accordion-collapse collapse" aria-labelledby="headingFarmTwo" data-bs-parent="#farmFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Look for the "Organic" badge and certification information with the seller\'s profile. Many sellers provide details about their organic certification body (such as NOP, EU Organic, etc.). For further verification, you can contact the seller directly through our messaging system.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFarmThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFarmThree" aria-expanded="false" aria-controls="collapseFarmThree">
                                            How fresh are the farm products?
                                        </button>
                                    </h2>
                                    <div id="collapseFarmThree" class="accordion-collapse collapse" aria-labelledby="headingFarmThree" data-bs-parent="#farmFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Each farm product listing shows the harvest date and freshness days (how many days since harvested). Many products are harvested the same day or within a day of listing, ensuring maximum freshness.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFarmFour">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFarmFour" aria-expanded="false" aria-controls="collapseFarmFour">
                                            Can I visit the farm before buying?
                                        </button>
                                    </h2>
                                    <div id="collapseFarmFour" class="accordion-collapse collapse" aria-labelledby="headingFarmFour" data-bs-parent="#farmFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Many farmers offer farm tour experiences. Look for the "Farm Tour Available" indicator in the product details or farmer profile. You can contact the farmer directly to arrange a visit. Always verify the farmer\'s identity before scheduling.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFarmFive">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFarmFive" aria-expanded="false" aria-controls="collapseFarmFive">
                                            How does the farm product delivery work?
                                        </button>
                                    </h2>
                                    <div id="collapseFarmFive" class="accordion-collapse collapse" aria-labelledby="headingFarmFive" data-bs-parent="#farmFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Delivery options vary by seller. Common options include local delivery within a specified radius, pickup from the farm, or arrangement of shipping for certain products. Each listing will specify the available delivery options and associated costs.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="mt-5">Selling Questions</h3>
                            
                            <div class="accordion" id="sellerFaqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSellOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSellOne" aria-expanded="true" aria-controls="collapseSellOne">
                                            How do I become a farm seller?
                                        </button>
                                    </h2>
                                    <div id="collapseSellOne" class="accordion-collapse collapse show" aria-labelledby="headingSellOne" data-bs-parent="#sellerFaqAccordion">
                                        <div class="accordion-body">
                                            <p>To become a farm seller:</p>
                                            <ol>
                                                <li>Register as a seller account</li>
                                                <li>Complete your farm profile with details about your location and farming practices</li>
                                                <li>Verify your identity and farm information</li>
                                                <li>Add your farm products with detailed information about harvest date, farming methods, and location</li>
                                                <li>Set your prices and delivery options</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSellTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSellTwo" aria-expanded="false" aria-controls="collapseSellTwo">
                                            What information do I need to provide for my products?
                                        </button>
                                    </h2>
                                    <div id="collapseSellTwo" class="accordion-collapse collapse" aria-labelledby="headingSellTwo" data-bs-parent="#sellerFaqAccordion">
                                        <div class="accordion-body">
                                            <p>For farm products, please provide:</p>
                                            <ul>
                                                <li>Detailed product description</li>
                                                <li>Harvest date and freshness days</li>
                                                <li>Farm location and practices</li>
                                                <li>Organic certification (if applicable)</li>
                                                <li>Quality rating and certifications</li>
                                                <li>Delivery options and radius</li>
                                                <li>Storage and handling instructions</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSellThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSellThree" aria-expanded="false" aria-controls="collapseSellThree">
                                            How do I get paid for my sales?
                                        </button>
                                    </h2>
                                    <div id="collapseSellThree" class="accordion-collapse collapse" aria-labelledby="headingSellThree" data-bs-parent="#sellerFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Payment methods vary based on buyer preference and location. You can offer:</p>
                                            <ul>
                                                <li>Cash on delivery</li>
                                                <li>Bank transfer</li>
                                                <li>Mobile money (Paystack)</li>
                                                <li>Escrow service for high-value items</li>
                                            </ul>
                                            <p>We recommend using secure payment methods that protect both you and the buyer.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSellFour">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSellFour" aria-expanded="false" aria-controls="collapseSellFour">
                                            Can I sell both farm products and other items?
                                        </button>
                                    </h2>
                                    <div id="collapseSellFour" class="accordion-collapse collapse" aria-labelledby="headingSellFour" data-bs-parent="#sellerFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Yes, you can sell both farm products and other general items on VidiaSpot. Your farm products will be categorized separately under the "Farm Products" section, while other items will be listed in their appropriate categories. You can toggle between your farm and general product listings in your seller dashboard.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="mt-5">Safety and Security</h3>
                            
                            <div class="accordion" id="safetyFaqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSafetyOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSafetyOne" aria-expanded="true" aria-controls="collapseSafetyOne">
                                            How do I stay safe when meeting buyers/sellers?
                                        </button>
                                    </h2>
                                    <div id="collapseSafetyOne" class="accordion-collapse collapse show" aria-labelledby="headingSafetyOne" data-bs-parent="#safetyFaqAccordion">
                                        <div class="accordion-body">
                                            <p>Follow these safety tips:</p>
                                            <ul>
                                                <li>Meet in well-lit, public places</li>
                                                <li>Inform someone about your meeting details</li>
                                                <li>Use the platform\'s messaging system for all communication</li>
                                                <li>Verify the other party\'s identity before meeting</li>
                                                <li>Bring a friend when possible</li>
                                                <li>Do not share personal financial information</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSafetyTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSafetyTwo" aria-expanded="false" aria-controls="collapseSafetyTwo">
                                            What should I do if I encounter a problem?
                                        </button>
                                    </h2>
                                    <div id="collapseSafetyTwo" class="accordion-collapse collapse" aria-labelledby="headingSafetyTwo" data-bs-parent="#safetyFaqAccordion">
                                        <div class="accordion-body">
                                            <p>If you encounter any problems:</p>
                                            <ol>
                                                <li>Use the "Report" feature on the listing or user profile</li>
                                                <li>Contact our customer support at support@vidiaspot.ng</li>
                                                <li>For urgent safety issues, contact local authorities</li>
                                                <li>Keep all communication records and transaction details</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSafetyThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSafetyThree" aria-expanded="false" aria-controls="collapseSafetyThree">
                                            How do I report unsafe farm practices?
                                        </button>
                                    </h2>
                                    <div id="collapseSafetyThree" class="accordion-collapse collapse" aria-labelledby="headingSafetyThree" data-bs-parent="#safetyFaqAccordion">
                                        <div class="accordion-body">
                                            <p>If you believe a farm has misreported its practices (e.g., claiming organic when not, misusing pesticides, etc.), please report the listing through our reporting system. Include specific details about why you believe the information is incorrect. Our team will investigate and take appropriate action.</p>
                                        </div>
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

<script>
    // Initialize accordions
    document.addEventListener(\'DOMContentLoaded\', function() {
        const accordions = document.querySelectorAll(\'.accordion-collapse\');
        accordions.forEach(accordion => {
            accordion.addEventListener(\'shown.bs.collapse\', function () {
                const icon = this.previousElementSibling.querySelector(\'i\');
                if (icon) {
                    icon.classList.remove(\'fa-plus\');
                    icon.classList.add(\'fa-minus\');
                }
            });
            
            accordion.addEventListener(\'hidden.bs.collapse\', function () {
                const icon = this.previousElementSibling.querySelector(\'i\');
                if (icon) {
                    icon.classList.remove(\'fa-minus\');
                    icon.classList.add(\'fa-plus\');
                }
            });
        });
    });
</script>

@endsection