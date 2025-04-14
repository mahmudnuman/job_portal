<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .email-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .email-header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }
        .email-body {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .applicant-info {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h3>New Applicant Notification</h3>
        </div>
        
        <div class="email-body">
            <div class="alert alert-info">
                <strong>New Application:</strong> A candidate has applied for your job posting.
            </div>
            
            <div class="applicant-info">
                <h5 class="mb-3">Applicant Details</h5>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> <a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
                <p><strong>Applied Position:</strong> {{ $listing->title }}</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ url('/listings/' . $listing->id) }}" class="btn btn-primary">
                    View Full Application Details
                </a>
            </div>
            
            <div class="mt-4 text-muted small">
                <p>This notification was sent on {{ now()->format('F j, Y \a\t g:i a') }}</p>
            </div>
        </div>
    </div>
</body>
</html>