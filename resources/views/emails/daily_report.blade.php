<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
        .stats-grid { display: flex; gap: 10px; margin: 20px 0; }
        .stat-box { background: #f3f4f6; padding: 10px; flex: 1; border-radius: 5px; text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tracker Update: {{ $monthName }}</h2>
            <p>Generated at {{ now()->format('H:i A') }}</p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Forms</div>
                <div class="stat-value">{{ $stats['count'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Amount In</div>
                <div class="stat-value">KES {{ number_format($stats['amount_in']) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $stats['completed'] }}</div>
            </div>
        </div>

        <h3>Latest 50 Entries for this Month</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Sales Person</th>
                    <th>Method</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($form->date)->format('d M') }}</td>
                    <td>{{ $form->client_name }}</td>
                    <td>{{ $form->sales_person }}</td>
                    <td>{{ $form->payment_method }}</td>
                    <td>{{ number_format($form->amount_in) }}</td>
                    <td>{{ number_format($form->amount_out) }}</td>
                    <td></td>{{ $form->feedback ? 'green' : 'orange' }}">
                        {{ $form->feedback ? 'Completed' : 'Pending' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            This is an automated report sent from the Web Tracker System.
        </div>
    </div>
</body>
</html>