<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">New Applicant Notification</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    A new candidate has applied for your job posting.
                </div>
                
                <div class="mb-4">
                    <h5 class="text-primary">Applicant Details</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Name:</strong> {{ $user->name }}
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong> <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Applied Position:</strong> {{ $listing->title }}
                        </li>
                    </ul>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('listings.show', $listing->id) }}" class="btn btn-primary">
                        View Application Details
                    </a>
                </div>
            </div>
            <div class="card-footer text-muted">
                Sent {{ now()->format('F j, Y \a\t g:i a') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>