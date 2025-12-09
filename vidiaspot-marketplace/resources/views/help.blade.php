@extends('layouts.app')

@section('title', 'Help & FAQ - Vidiaspot Marketplace')
@section('meta_description', 'Frequently asked questions about using Vidiaspot Marketplace. Find answers to common questions about buying, selling, and using our platform.')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Help & FAQ</h1>
            <p class="lead">Find answers to common questions about using Vidiaspot Marketplace.</p>
            
            <div class="accordion mt-5" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                            How do I create an account?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            To create an account, click on the "Sign Up" button in the top navigation bar. Enter your email address, choose a password, and follow the on-screen instructions. You'll need to verify your email address before you can start posting ads.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                            How do I post an ad?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Once you've created an account and logged in, click on the "Post Ad" button in the navigation bar. Fill out the details of your item, add photos, and set your price. You can post ads for free, and they will appear on the site within minutes.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                            How do I buy something on the platform?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Browse through the ads using the search function or category filters. When you find something you want to buy, click on it to see more details. You can contact the seller using the contact information provided in the ad. All transactions are between the buyer and seller directly.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                            Is posting ads free?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, posting basic ads is completely free. We also offer premium ad placement options for sellers who want to reach more potential buyers. These are optional upgrades to highlight your ads.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                            How do I stay safe while buying or selling?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Always meet in safe, public places for transactions. Verify the item before paying. Be wary of deals that seem too good to be true. Never send money without seeing the item in person. Report any suspicious activity to our team immediately.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix">
                            How do I update or delete my ad?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Log into your account and go to "My Ads" in your dashboard. From there, you can edit, update, or delete your active ads. You can also mark items as sold when the transaction is complete.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h3>Still have questions?</h3>
                <p>Can't find the answer you're looking for? Please contact our support team for more assistance.</p>
                <a href="/contact" class="btn btn-success">Contact Support</a>
            </div>
        </div>
    </div>
</div>
@endsection