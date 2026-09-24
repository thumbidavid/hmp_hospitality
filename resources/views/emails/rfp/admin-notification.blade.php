<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New RFP Received</title>
</head>
<body style="margin: 0; padding: 24px 12px; background-color: #f7f7f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #292524;">

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 12px; border: 1px solid #e7e5e4; overflow: hidden; margin: 0 auto;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #082018; padding: 36px 24px; text-align: center;">
                <a href="{{ config('app.url') }}" target="_blank" style="display: inline-block; margin-bottom: 18px;">
                    <img src="{{ asset('logo_white.png') }}" alt="HMP Hospitality" height="36" style="height: 36px; border: 0; outline: none; text-decoration: none;" />
                </a>
                <h1 style="color: #ffffff; font-size: 20px; font-weight: 600; margin: 0 0 6px 0; letter-spacing: -0.02em;">New RFP submission received</h1>
                <p style="color: #d6d3d1; font-size: 13px; margin: 0;">
                    Reference number: <span style="font-family: monospace; font-weight: 600; color: #b65e3e;">{{ $rfp->reference_number }}</span>
                </p>
            </td>
        </tr>

        <!-- Terracotta Accent Line -->
        <tr>
            <td height="2" style="background-color: #b65e3e; line-height: 2px; font-size: 2px;">&nbsp;</td>
        </tr>

        <!-- Quick Responder Bar -->
        <tr>
            <td style="background-color: #fafaf9; padding: 12px 24px; border-bottom: 1px solid #e7e5e4;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="left" style="font-size: 12px; color: #78716c;">
                            Submitted on {{ $rfp->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td align="right">
                            <a href="mailto:{{ $rfp->email }}?subject={{ urlencode('Re: HMP Hospitality RFP ' . $rfp->reference_number) }}" style="font-size: 12px; font-weight: 600; color: #b65e3e; text-decoration: none;">
                                Reply to client directly &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Content Area -->
        <tr>
            <td style="padding: 28px 24px;">

                <!-- Section: Client Details -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td style="border-bottom: 1px solid #e7e5e4; padding-bottom: 8px;">
                            <table width="100%">
                                <tr>
                                    <td align="left" style="font-size: 14px; font-weight: 600; color: #1c1917;">Client details</td>
                                    <td align="right">
                                        <span style="font-size: 11px; font-weight: 500; background-color: #fbf2ee; color: #b65e3e; border: 1px solid #f3dcd3; padding: 2px 8px; border-radius: 4px;">
                                            Direct lead
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 12px;">
                            <table width="100%" style="font-size: 12px; line-height: 1.6;">
                                <tr>
                                    <td width="50%" valign="top" style="padding-bottom: 10px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Full name</span>
                                        <strong style="color: #1c1917; font-size: 13px;">{{ $rfp->full_name }}</strong>
                                    </td>
                                    <td width="50%" valign="top" style="padding-bottom: 10px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Company & title</span>
                                        <span style="color: #292524;">{{ $rfp->job_title ?? 'N/A' }} &middot; {{ $rfp->company_name ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" style="padding-bottom: 10px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Email address</span>
                                        <a href="mailto:{{ $rfp->email }}" style="color: #b65e3e; text-decoration: none; font-weight: 500;">{{ $rfp->email }}</a>
                                    </td>
                                    <td width="50%" valign="top" style="padding-bottom: 10px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Phone / WhatsApp</span>
                                        <span style="color: #292524;">{{ $rfp->phone ?? 'Not provided' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Buyer country & type</span>
                                        <span style="color: #292524;">{{ $rfp->buyerCountry?->name ?? 'N/A' }} &middot; {{ $rfp->buyerType?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td width="50%" valign="top">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Preferred contact channel</span>
                                        <span style="color: #292524;">{{ ucfirst(str_replace('_', ' ', $rfp->preferred_communication_method)) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Section: Programme Specifications -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td style="font-size: 14px; font-weight: 600; color: #1c1917; border-bottom: 1px solid #e7e5e4; padding-bottom: 8px;">
                            Programme specifications
                        </td>
                    </tr>
                    <!-- Metric Cards -->
                    <tr>
                        <td style="padding: 14px 0;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #e7e5e4; border-radius: 8px; background-color: #fafaf9; text-align: center;">
                                <tr>
                                    <td width="25%" style="padding: 10px; border-right: 1px solid #e7e5e4;">
                                        <span style="font-size: 10px; color: #a8a29e; display: block;">Attendees</span>
                                        <strong style="font-size: 15px; color: #1c1917;">{{ $rfp->number_of_attendees ?? '-' }}</strong>
                                    </td>
                                    <td width="25%" style="padding: 10px; border-right: 1px solid #e7e5e4;">
                                        <span style="font-size: 10px; color: #a8a29e; display: block;">Rooms</span>
                                        <strong style="font-size: 15px; color: #1c1917;">{{ $rfp->number_of_rooms ?? '-' }}</strong>
                                    </td>
                                    <td width="25%" style="padding: 10px; border-right: 1px solid #e7e5e4;">
                                        <span style="font-size: 10px; color: #a8a29e; display: block;">Room nights</span>
                                        <strong style="font-size: 15px; color: #1c1917;">{{ $rfp->number_of_room_nights ?? '-' }}</strong>
                                    </td>
                                    <td width="25%" style="padding: 10px;">
                                        <span style="font-size: 10px; color: #a8a29e; display: block;">Budget</span>
                                        <strong style="font-size: 15px; color: #b65e3e;">
                                            {{ $rfp->budget_amount ? ($rfp->currency . ' ' . number_format($rfp->budget_amount)) : 'Not specified' }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" style="font-size: 12px; line-height: 1.6;">
                                <tr>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Programme title</span>
                                        <span style="color: #292524; font-weight: 500;">{{ $rfp->programme_name ?? 'Not specified' }}</span>
                                    </td>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Requirement type</span>
                                        <span style="color: #292524; font-weight: 500;">{{ ucwords(str_replace('_', ' ', $rfp->requirement_type)) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Travel dates</span>
                                        <span style="color: #292524;">
                                            {{ $rfp->arrival_date ? \Carbon\Carbon::parse($rfp->arrival_date)->format('d M Y') : 'TBD' }}
                                            – 
                                            {{ $rfp->departure_date ? \Carbon\Carbon::parse($rfp->departure_date)->format('d M Y') : 'TBD' }}
                                            @if($rfp->is_dates_flexible) <em style="color: #78716c;">(Flexible)</em> @endif
                                        </span>
                                    </td>
                                    <td width="50%" valign="top" style="padding-bottom: 8px;">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Preferred destination</span>
                                        <span style="color: #292524;">{{ $rfp->preferred_destination ?? 'Flexible' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" valign="top">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Proposal deadline</span>
                                        <span style="color: #b65e3e; font-weight: 500;">{{ $rfp->proposal_deadline ? \Carbon\Carbon::parse($rfp->proposal_deadline)->format('d M Y') : 'Flexible' }}</span>
                                    </td>
                                    <td width="50%" valign="top">
                                        <span style="color: #a8a29e; display: block; font-size: 11px;">Decision date</span>
                                        <span style="color: #292524;">{{ $rfp->decision_date ? \Carbon\Carbon::parse($rfp->decision_date)->format('d M Y') : 'Flexible' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Section: Shortlisted Properties -->
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
                                            View property &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        </td>
                    </tr>
                </table>
                @endif

                <!-- Section: Requested Agency Services -->
                @if($rfp->agencyServices && $rfp->agencyServices->count() > 0)
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td style="font-size: 14px; font-weight: 600; color: #1c1917; border-bottom: 1px solid #e7e5e4; padding-bottom: 8px;">
                            Requested agency services
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">
                            @foreach($rfp->agencyServices as $service)
                                <span style="display: inline-block; font-size: 11px; padding: 4px 10px; background-color: #fafaf9; border: 1px solid #e7e5e4; border-radius: 6px; margin: 0 4px 6px 0; color: #44403c;">
                                    &check; {{ $service->name }}
                                </span>
                            @endforeach
                        </td>
                    </tr>
                </table>
                @endif

                <!-- Section: Additional Notes -->
                @if($rfp->meeting_room_requirements || $rfp->fnb_requirements || $rfp->transfer_airport_requirements || $rfp->sustainability_requirements || $rfp->additional_requirements)
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td style="font-size: 14px; font-weight: 600; color: #1c1917; border-bottom: 1px solid #e7e5e4; padding-bottom: 8px;">
                            Additional requirements & brief
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">
                            <div style="background-color: #fafaf9; border: 1px solid #e7e5e4; border-radius: 8px; padding: 14px; font-size: 12px; line-height: 1.6; color: #44403c;">
                                @if($rfp->meeting_room_requirements)
                                    <p style="margin: 0 0 6px 0;"><strong>Meeting spaces:</strong> {{ $rfp->meeting_room_requirements }}</p>
                                @endif
                                @if($rfp->fnb_requirements)
                                    <p style="margin: 0 0 6px 0;"><strong>F&B / Dietary:</strong> {{ $rfp->fnb_requirements }}</p>
                                @endif
                                @if($rfp->transfer_airport_requirements)
                                    <p style="margin: 0 0 6px 0;"><strong>Transfers:</strong> {{ $rfp->transfer_airport_requirements }}</p>
                                @endif
                                @if($rfp->sustainability_requirements)
                                    <p style="margin: 0 0 6px 0;"><strong>Sustainability:</strong> {{ $rfp->sustainability_requirements }}</p>
                                @endif
                                @if($rfp->additional_requirements)
                                    <p style="margin: 0;"><strong>Notes:</strong> {{ $rfp->additional_requirements }}</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
                @endif

                <!-- Attachment Link -->
                @if($rfp->attachment_url)
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #e7e5e4; border-radius: 8px; background-color: #fafaf9; padding: 12px 14px;">
                    <tr>
                        <td style="font-size: 12px; color: #1c1917;">
                            &#128206; <strong style="margin-left: 4px;">Attached Tender File / Brief</strong>
                        </td>
                        <td align="right">
                            <a href="{{ $rfp->attachment_url }}" target="_blank" style="font-size: 12px; color: #b65e3e; text-decoration: none; font-weight: 600;">
                                Download file &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
                @endif

            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #fafaf9; border-top: 1px solid #e7e5e4; padding: 20px 24px; text-align: center; font-size: 11px; color: #a8a29e;">
                <p style="margin: 0 0 4px 0; color: #57534e; font-weight: 600;">HMP Hospitality</p>
                <p style="margin: 0 0 8px 0;">6th Floor, MJ1 Business Park, Westlands Road, Nairobi, Kenya</p>
                <p style="margin: 0; color: #a8a29e;">Automated notification generated from <a href="{{ config('app.url') }}" style="color: #78716c; text-decoration: underline;">hmphospitality.co</a></p>
            </td>
        </tr>
    </table>

</body>
</html>