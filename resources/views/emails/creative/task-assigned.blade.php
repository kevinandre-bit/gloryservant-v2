<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Assigned</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">New Creative Task Assigned</h2>
        
        <p>Hi {{ $assignee->firstname }},</p>
        
        <p>You've been assigned a new creative task:</p>
        
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin: 0 0 10px 0; color: #1e40af;">{{ $task->title }}</h3>
            <p style="margin: 0 0 10px 0;"><strong>Request:</strong> {{ $task->request->title }}</p>
            <p style="margin: 0 0 10px 0;"><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
            @if($task->due_at)
            <p style="margin: 0 0 10px 0;"><strong>Due:</strong> {{ $task->due_at->format('M d, Y') }}</p>
            @endif
            @if($task->description)
            <p style="margin: 0;"><strong>Description:</strong> {{ $task->description }}</p>
            @endif
        </div>
        
        <p>
            <a href="{{ url('/personal/creative/tasks/' . $task->id) }}" 
               style="background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                View Task Details
            </a>
        </p>
        
        <p>Best regards,<br>Creative Team</p>
    </div>
</body>
</html>