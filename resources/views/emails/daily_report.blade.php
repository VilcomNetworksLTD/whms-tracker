<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 100%; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
        
        /* Stats Grid */
        .stats-grid { display: flex; gap: 10px; margin: 20px 0; }
        .stat-box { background: #f3f4f6; padding: 10px; flex: 1; border-radius: 5px; text-align: center; }
        .stat-label { font-size: 11px; color: #666; text-transform: uppercase; font-weight: bold; }
        .stat-value { font-size: 15px; font-weight: bold; color: #2563eb; margin-top: 5px; }
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f8f9fa; color: #444; font-weight: bold; }
        
        /* Alternating row colors */
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        /* Column Specifics */
        td.amount { text-align: right; font-family: monospace; white-space: nowrap; }
        th.amount { text-align: right; }
        .small-date { font-size: 10px; color: #888; display: block; margin-top: 2px; }
        .description-cell { font-style: italic; color: #555; }

        .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tracker Update: {{ $monthName }}</h2>
            <p>Generated on {{ now()->setTimezone('Africa/Nairobi')->format('l, d M Y \a\t h:i A') }}</p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Forms</div>
                <div class="stat-value">{{ number_format($stats['count']) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Amount In</div>
                <div class="stat-value">KES {{ number_format($stats['amount_in']) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Fees</div>
                <div class="stat-value">KES {{ number_format($stats['fees']) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Amount Out</div>
                <div class="stat-value">KES {{ number_format($stats['amount_out']) }}</div>
            </div>
        </div>

        <h3>Full Transaction List</h3>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 15%">Client Name</th> <th style="width: 15%">Sales Person</th>  <th style="width: 10%">Date</th>        <th style="width: 10%">Method</th>      <th style="width: 20%">Description</th> <th class="amount" style="width: 10%">In</th>    <th class="amount" style="width: 8%">Fees</th>  <th class="amount" style="width: 10%">Out</th>   <th style="width: 17%">Feedback</th>    </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                <tr>
                    <td>{{ $form->client_name }}</td>
                    <td>{{ $form->sales_person }}</td>
                    <td>{{ \Carbon\Carbon::parse($form->date)->format('d M') }}</td>
                    
                    <td>{{ $form->payment_method }}</td>
                    
                    <td class="description-cell">
                        {{ \Illuminate\Support\Str::limit($form->description, 50) }}
                    </td>
                    
                    <td class="amount" style="color: green;">
                        {{ number_format($form->amount_in) }}
                    </td>
                    
                    <td class="amount" style="color: #d97706;">
                        {{ number_format($form->fees) }}
                    </td>
                    
                    <td class="amount" style="color: #2563eb; font-weight: bold;">
                        {{ number_format($form->amount_out) }}
                    </td>
                    
                    <td>
                        @if($form->feedback)
                            {{ $form->feedback }}
                            @if($form->feedback_date)
                                <span class="small-date">
                                    {{ \Carbon\Carbon::parse($form->feedback_date)->format('d M Y') }}
                                </span>
                            @endif
                        @else
                            <span style="color: #999;">-</span>
                        @endif
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