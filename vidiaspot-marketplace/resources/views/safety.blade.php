@extends('layouts.app')

@section('title', 'Safety Tips - Vidiaspot Marketplace')
@section('meta_description', 'Important safety tips for buying and selling on Vidiaspot Marketplace. Learn how to protect yourself and have safe transactions.')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Safety Tips</h1>
            <p class="lead">Your safety is our top priority. Follow these guidelines to have safe transactions on Vidiaspot Marketplace.</p>
            
            <div class="mt-5">
                <h3>Buying Safety Tips</h3>
                <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Meet in Safe Places</h5>
                        <p>Always meet in public, well-lit places. Avoid going to sellers' homes or private locations, especially alone.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Inspect Before You Buy</h5>
                        <p>Thoroughly examine the item before making payment. Test electronic devices and check the condition of physical items.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Bring a Friend</h5>
                        <p>When possible, bring someone with you, especially when making large purchases or meeting strangers.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Cash is King</h5>
                        <p>Pay with cash or secure payment methods. Avoid wire transfers or online payments to individuals you haven't met.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Trust Your Instincts</h5>
                        <p>If something feels off about the transaction or seller, trust your instincts and walk away.</p>
                    </li>
                </ul>
            </div>
            
            <div class="mt-5">
                <h3>Selling Safety Tips</h3>
                <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Meet in Safe Places</h5>
                        <p>Arrange to meet buyers in public places, or consider meeting in your own neighborhood where you feel most comfortable.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Don't Accept Checks</h5>
                        <p>Only accept cash or secure payment methods. Avoid accepting checks from strangers as they can bounce.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Describe Accurately</h5>
                        <p>Provide honest and accurate descriptions of your items, including any defects or wear. This will prevent disputes later.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Don't Ship Items Initially</h5>
                        <p>Until you're experienced, consider only local sales where you can meet in person. Shipping to strangers carries additional risks.</p>
                    </li>
                    <li class="list-group-item">
                        <h5><i class="fas fa-check-circle text-success me-2"></i> Keep Records</h5>
                        <p>Keep records of sales for your own records, including photos of the item with the buyer if possible.</p>
                    </li>
                </ul>
            </div>
            
            <div class="mt-5">
                <h3>Red Flags to Watch For</h3>
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Too Good to Be True</h5>
                                <p class="card-text">Deals that seem incredibly cheap or sellers offering to pay shipping costs often have hidden motives.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Pressure Tactics</h5>
                                <p class="card-text">Sellers who pressure you to make immediate decisions without allowing inspection or consideration time.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> No Face-to-Face</h5>
                                <p class="card-text">Buyers who insist on shipping only or sellers who won't let you see the actual item.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Payment Requests</h5>
                                <p class="card-text">Requests to pay through unconventional methods or to send money before seeing the item.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h3>Report Suspicious Activity</h3>
                <p>If you encounter suspicious behavior or feel unsafe during a transaction, report it to our team immediately. We take member safety seriously and investigate all reports.</p>
                <a href="/contact" class="btn btn-danger">Report an Issue</a>
            </div>
        </div>
    </div>
</div>
@endsection