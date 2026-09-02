<!DOCTYPE html>
<html>
<head>
    <title>HMP Hospitality Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #10b981; border-bottom: 1px solid #eee; padding-bottom: 10px;">{{ $title }}</h2>
    
    @if($imageUrl)
        <img src="{{ $imageUrl }}" style="width: 100%; max-height: 250px; object-cover: cover; border-radius: 8px; margin-bottom: 20px;" alt="Visual update" />
    @endif
    
    <p style="font-size: 16px; leading-height: 1.6;">{{ $excerpt }}</p>
    
    <p style="margin-top: 25px;">
        <a href="{{ $targetUrl }}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 20px; font-weight: bold; font-size: 14px;">
            Explore on Website
        </a>
    </p>
    
    <!-- FOOTER WITH SECURE SIGNED UNSUBSCRIBE LINK -->
    <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 15px; font-size: 11px; color: #999; text-align: center;">
        <p>You received this email because you subscribed to HMP Hospitality updates.</p>
        <p>
            <a href="{{ $unsubscribeUrl }}" style="color: #10b981; text-decoration: underline;">
                Unsubscribe from these emails
            </a>
        </p>
    </div>
</body>
</html>