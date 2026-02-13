<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daily Tracker Summary</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .panel { border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: #f9f9f9; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .description-cell {
            max-width: 250px;
            white-space: normal;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <h2>Daily Tracker Summary - {{ $date instanceof \Carbon\Carbon ? $date->format('F j, Y') : \Carbon\Carbon::parse($date)->format('F j, Y') }}</h2>
    
    <p>Hello <strong>{{ $user->name }}</strong>,</p>
    <p>Here's your summary of all tracker entries:</p>

    @if($trackers->isEmpty())
        <div class="panel">
            No tracker entries were recorded on {{ $date instanceof \Carbon\Carbon ? $date->format('F j, Y') : \Carbon\Carbon::parse($date)->format('F j, Y') }}.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Sales Person</th>
                    <th>Description</th>
                    <th>Amount (In)</th>
                    <th>Fees</th>
                    <th>Amount (Out)</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                    <th>Feedback</th>
                    <th>Feedback Date</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
            @foreach($trackers as $tracker)
               <tr>
                    <td>{{ $tracker->id }}</td>
                    <td>{{ $tracker->client_name }}</td>
                    <td>{{ $tracker->sales_person ?? 'N/A' }}</td>
                    <td class="description-cell">{{ $tracker->description ?? 'No description' }}</td>
                    <td>KES{{ number_format($tracker->amount_in, 2) }}</td>
                    <td>KES{{ number_format($tracker->fees ?? 0, 2) }}</td>
                    <td>KES{{ number_format($tracker->amount_out ?? 0, 2) }}</td>
                    <td>{{ $tracker->payment_method }}</td>
                    <td>{{ $tracker->feedback ?? 'N/A' }}</td>
                    <td>{{ $tracker->feedback_date ? \Carbon\Carbon::parse($tracker->feedback_date)->format('d-m-Y') : 'N/A' }}</td>
                    <td>{{ $tracker->created_at ? \Carbon\Carbon::parse($tracker->created_at)->format('d-m-Y H:i') : 'N/A' }}</td>
                    <td>{{ $tracker->updated_at ? \Carbon\Carbon::parse($tracker->updated_at)->format('d-m-Y H:i') : 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @php
            $totalIn = $trackers->sum('amount_in');
            $totalFees = $trackers->sum('fees');
            $totalOut = $trackers->sum('amount_out');
            $netAmount = $totalIn - $totalFees - $totalOut;
            $completedCount = $trackers->whereNotNull('feedback')->count();
            $pendingCount = $trackers->whereNull('feedback')->count();
            $withDescription = $trackers->whereNotNull('description')->where('description', '!=', '')->count();
            $withoutDescription = $trackers->count() - $withDescription;
        @endphp

        <div class="panel">
            <h3>Summary Statistics</h3>
            <ul>
                <li><strong>Total Records:</strong> {{ $trackers->count() }}</li>
                <li><strong>Completed (with feedback):</strong> {{ $completedCount }}</li>
                <li><strong>Pending (no feedback):</strong> {{ $pendingCount }}</li>
                <li><strong>With Description:</strong> {{ $withDescription }}</li>
                <li><strong>Without Description:</strong> {{ $withoutDescription }}</li>
                <li><strong>Total Amount In:</strong> KES{{ number_format($totalIn, 2) }}</li>
                <li><strong>Total Fees:</strong> KES{{ number_format($totalFees, 2) }}</li>
                <li><strong>Total Amount Out:</strong> KES{{ number_format($totalOut, 2) }}</li>
                <li><strong>Net Amount:</strong> KES{{ number_format($netAmount, 2) }}</li>
            </ul>
        </div>
        
        {{-- Summary by Payment Method --}}
        @php
            $paymentMethods = $trackers->groupBy('payment_method')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount_in')
                ];
            });
        @endphp
        
        @if($paymentMethods->count() > 0)
        <div class="panel">
            <h3>Summary by Payment Method</h3>
            <ul>
                @foreach($paymentMethods as $method => $data)
                    <li><strong>{{ $method }}:</strong> {{ $data['count'] }} entries, KES{{ number_format($data['total'], 2) }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    @endif

    <p><a href="https://webtracker.vilcom-net.co.ke/" class="button">View Dashboard</a></p>

    <p>Thanks,<br>Vilcom CSS Team</p>

    <hr>
    <p style="font-size: small; color: #555;">
        This is an automated daily summary for {{ $date instanceof \Carbon\Carbon ? $date->format('F j, Y') : \Carbon\Carbon::parse($date)->format('F j, Y') }}. 
        Total entries: {{ $trackers->count() }}. 
        Please do not reply to this email.
    </p>
</body>
</html>