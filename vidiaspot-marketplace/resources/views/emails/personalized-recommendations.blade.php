<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Personalized Recommendations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #388e3c;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-top: none;
        }
        .recommendation {
            border: 1px solid #eee;
            margin: 10px 0;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
        }
        .recommendation img {
            max-width: 100px;
            float: left;
            margin-right: 15px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Personalized Recommendations For You</h1>
        <p>Based on your interests and browsing history</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name ?? 'there' }}!</h2>
        <p>We found some items that might interest you:</p>
        
        @if($recommendations && $recommendations->count() > 0)
            @foreach($recommendations->take(5) as $ad)
            <div class="recommendation">
                <img src="{{ $ad->images->first()->url ?? 'https://via.placeholder.com/100x100' }}" alt="{{ $ad->title }}" style="width: 100px; height: 100px; object-fit: cover;">
                <h3><a href="{{ url('/ads/' . $ad->id) }}">{{ $ad->title }}</a></h3>
                <p class="price">₦{{ number_format($ad->price) }}</p>
                <p><small>{{ $ad->location }}</small></p>
            </div>
            @endforeach
        @else
            <p>No personalized recommendations available at this time.</p>
        @endif
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="{{ url('/home') }}" style="background-color: #388e3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View More Recommendations</a>
        </p>
    </div>
    
    <div class="footer">
        <p>You received this email because you opted in for personalized recommendations.</p>
        <p><a href="{{ url('/settings/notifications') }}">Update your notification preferences</a></p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>