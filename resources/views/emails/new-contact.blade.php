<!DOCTYPE html>
<html>
<head>
    <title>New Contact Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #10b981; border-b: 1px solid #eee; padding-bottom: 10px;">New Message Received</h2>
    <p>A new message has been submitted via the HMP Hospitality website contact form:</p>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 150px;">Name:</td>
            <td style="padding: 8px 0;">{{ $submission->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Email:</td>
            <td style="padding: 8px 0;"><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Subject:</td>
            <td style="padding: 8px 0;">{{ $submission->subject }}</td>
        </tr>
    </table>
    
    <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 8px; border: 1px solid #eee;">
        <p style="margin: 0; font-weight: bold; margin-bottom: 10px;">Message:</p>
        <p style="margin: 0; white-space: pre-wrap; font-size: 14px; color: #555;">{{ $submission->message }}</p>
    </div>
</body>
</html>