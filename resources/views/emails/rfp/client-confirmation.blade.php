<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We have received your RFP</title>
</head>
<body style="margin: 0; padding: 24px 12px; background-color: #f7f7f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #292524;">

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 12px; border: 1px solid #e7e5e4; overflow: hidden; margin: 0 auto;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #082018; padding: 36px 24px; text-align: center;">
                <a href="{{ config('app.url') }}" target="_blank" style="display: inline-block; margin-bottom: 18px;">
                    <img src="{{ asset('logo_white.png') }}" alt="HMP Hospitality" height="36" style="height: 36px; border: 0; outline: none; text-decoration: none;" />
                </a>
                <h1 style="color: #ffffff; font-size: 20px; font-weight: 600; margin: 0 0 6px 0; letter-spacing: -0.02em;">Thank you for your enquiry</h1>
                <p style="color: #d6d3d1; font-size: 13px; margin: 0;">
                    Reference number: <span style="font-family: monospace; font-weight: 600; color: #b65e3e;">{{ $rfp->reference_number }}</span>
                </p>
            </td>
        </tr>

        <!-- Terracotta Accent Line -->
        <tr>
            <td height="2" style="background-color: #b65e3e; line-height: 2px; font-size: 2px;">&nbsp;</td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 32px 24px;">

                <!-- Executive Greeting -->
                <div style="font-size: 14px; line-height: 1.6; color: #44403c; margin-bottom: 24px;">
                    <p style="margin: 0 0 12px 0; font-size: 15px; font-weight: 600; color: #1c1917;">Dear {{ $rfp->full_name }},</p>
                    <p style="margin: 0 0 12px 0;">
                        We have successfully received your request for proposal. Our team is currently reviewing your programme brief and cross-referencing availability across our portfolio.
                    </p>
                    <p style="margin: 0;">
                        One of our dedicated <strong style="color: #1c1917;">Portfolio Specialists</strong> will personally review your requirements and reach out to you within <strong style="color: #1c1917;">24 business hours</strong> with initial availability, rates, and bespoke recommendations.
                    </p>
                </div>

                <!-- WhatsApp Callout Box -->
                @php
                    $waText = urlencode("Hello HMP Hospitality, I have submitted an RFP (Ref: {$rfp->reference_number}) and would like to connect.");
                    $waUrl = "https://wa.me/254703720000?text={$waText}";
                @endphp
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #fafaf9; border: 1px solid #e7e5e4; border-radius: 12px; margin-bottom: 28px; text-align: center;">
                    <tr>
                        <td style="padding: 22px 18px;">
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #1c1917;">Need immediate assistance or have updates?</h3>
                            <p style="margin: 0 0 16px 0; font-size: 12px; color: #78716c;">Connect directly with our Nairobi desk on WhatsApp.</p>

                            <!-- WhatsApp Button -->
                            <a href="{{ $waUrl }}" target="_blank" style="display: inline-block; background-color: #082018; color: #ffffff; text-decoration: none; font-size: 12px; font-weight: 600; padding: 10px 22px; border-radius: 8px;">
                                Chat with us on WhatsApp (+254 703 720 000) &rarr;
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Summary Overview -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td style="font-size: 14px; font-weight: 600; color: #1c1917; border-bottom: 1px solid #e7e5e4; padding-bottom: 8px;">
                            Your request overview
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 12px;">
                            <table width="100%" style="font-size: 12px; line-height: 1.6;">
                                <tr>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Programme title</span>
                                        <span style="color: #1c1917; font-weight: 500;">{{ $rfp->programme_name ?? 'Not specified' }}</span>
                                    </td>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Requirement type</span>
                                        <span style="color: #1c1917; font-weight: 500;">{{ ucwords(str_replace('_', ' ', $rfp->requirement_type)) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Travel dates</span>
                                        <span style="color: #1c1917;">
                                            {{ $rfp->arrival_date ? \Carbon\Carbon::parse($rfp->arrival_date)->format('d M Y') : 'TBD' }}
                                            –
                                            {{ $rfp->departure_date ? \Carbon\Carbon::parse($rfp->departure_date)->format('d M Y') : 'TBD' }}
                                        </span>
                                    </td>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Preferred destination</span>
                                        <span style="color: #1c1917;">{{ $rfp->preferred_destination ?? 'Flexible' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Delegates & rooms</span>
                                        <span style="color: #1c1917;">{{ $rfp->number_of_attendees ?? '-' }} attendees &middot; {{ $rfp->number_of_rooms ?? '-' }} rooms</span>
                                    </td>
                                    <td width="50%" valign="top">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Proposal deadline</span>
                                        <span style="color: #b65e3e; font-weight: 500;">{{ $rfp->proposal_deadline ? \Carbon\Carbon::parse($rfp->proposal_deadline)->format('d M Y') : 'Flexible' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Shortlisted Properties -->
                @if($rfp->properties && $rfp->properties->count() > 0)
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td style="font-size: 14px; font-weight: 600; color: #1c1917; border-bottom: 1px solid #e7e5e4; padding-bottom: 8px;">
                            Shortlisted properties ({{ $rfp->properties->count() }})
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">
                            @foreach($rfp->properties as $property)
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #e7e5e4; border-radius: 8px; margin-bottom: 8px; padding: 10px;">
                                <tr>
                                    <td width="48" valign="middle">
                                        <img src="{{ $property->featured_image_url ?? 'https://images.unsplash.com/photo-1566073771259-1a873a6a8bed?auto=format&fit=crop&w=100&q=80' }}" width="40" height="40" style="border-radius: 6px; object-fit: cover; display: block; border: 1px solid #e7e5e4;" />
                                    </td>
                                    <td valign="middle" style="padding-left: 12px;">
                                        <div style="font-size: 13px; font-weight: 600; color: #1c1917;">{{ $property->name }}</div>
                                        <div style="font-size: 11px; color: #78716c;">{{ $property->city ?? '' }}{{ $property->city ? ' &middot; ' : '' }}{{ $property->country?->name ?? 'Africa' }}</div>
                                    </td>
                                    <td align="right" valign="middle">
                                        <a href="{{ url('/portfolio/' . $property->slug) }}" target="_blank" style="font-size: 12px; color: #b65e3e; text-decoration: none; font-weight: 500;">
                                            View &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        </td>
                    </tr>
                </table>
                @endif

                <!-- Sign-off -->
                <div style="border-top: 1px solid #f5f5f4; padding-top: 16px; font-size: 13px; line-height: 1.5; color: #78716c;">
                    <p style="margin: 0; font-weight: 600; color: #1c1917;">Warm regards,</p>
                    <p style="margin: 0; color: #44403c;">The Portfolio & Client Relations Team</p>
                    <p style="margin: 0; color: #a8a29e;">HMP Hospitality</p>
                </div>

            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #fafaf9; border-top: 1px solid #e7e5e4; padding: 20px 24px; text-align: center; font-size: 11px; color: #a8a29e;">
                <p style="margin: 0 0 4px 0; color: #57534e; font-weight: 600;">HMP Hospitality</p>
                <p style="margin: 0 0 6px 0;">6th Floor, MJ1 Business Park, Westlands Road, Nairobi, Kenya</p>
                <p style="margin: 0 0 8px 0;">+254 703 720 000 &middot; hello@hmphospitality.co</p>
                <p style="margin: 0;">You are receiving this confirmation because an enquiry was submitted on <a href="{{ config('app.url') }}" style="color: #78716c; text-decoration: underline;">hmphospitality.co</a>.</p>
            </td>
        </tr>
    </table>

</body>
</html>
