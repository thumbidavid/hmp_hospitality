<!DOCTYPE html>
<html>
<head>
    <title>New RFP Submitted</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #10b981; border-b: 1px solid #eee; padding-bottom: 10px;">New RFP Submitted [{{ $rfp->reference_number }}]</h2>
    <p>A new Request for Proposal (RFP) has been received via the HMP Hospitality website. Below is a summary of the client's brief:</p>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 180px;">Reference Number:</td>
            <td style="padding: 8px 0;">{{ $rfp->reference_number }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Planner Name:</td>
            <td style="padding: 8px 0;">{{ $rfp->full_name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Company / Org:</td>
            <td style="padding: 8px 0;">{{ $rfp->company_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Planner Email:</td>
            <td style="padding: 8px 0;"><a href="mailto:{{ $rfp->email }}">{{ $rfp->email }}</a></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Planner Phone:</td>
            <td style="padding: 8px 0;">{{ $rfp->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Requirement Type:</td>
            <td style="padding: 8px 0; text-transform: capitalize;">{{ str_replace('_', ' ', $rfp->requirement_type) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Preferred Destination:</td>
            <td style="padding: 8px 0;">{{ $rfp->preferred_destination ?? 'N/A' }}</td>
        </tr>
    </table>
    
    <div style="margin-top: 30px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
        <p style="margin: 0; font-size: 14px; color: #666;">
            Please log in to your HMP Hospitality admin dashboard to review the complete brief, match candidates, and coordinate the proposal pipeline.
        </p>
    </div>
</body>
</html>