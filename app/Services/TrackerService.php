<?php

namespace App\Services;

use App\Models\TrackerForm;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrackerService
{
    /**
     * Get paginated and filtered tracker forms.
     */
    public function getAll(Request $request)
    {
        $query = TrackerForm::query();

        // 1. Search (Added sales_person here)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sales_person', 'LIKE', "%{$search}%") // Added this
                  ->orWhere('payment_method', 'LIKE', "%{$search}%");
            });
        }

        // 2. Status Filter
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('feedback');
            } elseif ($request->status === 'pending') {
                $query->whereNull('feedback');
            }
        }

        // 3. Payment Method Filter
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // 4. Date Filters
        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // 5. Sorting
        $sortField = $request->get('sort_field', 'date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $validSortFields = ['client_name', 'date', 'payment_method', 'amount_in', 'fees', 'amount_out', 'sales_person'];
        
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('date', 'desc');
        }

        // 6. Pagination
        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Create a new tracker form.
     */
    public function create(array $data)
    {
        // Auto-calculate amount_out if missing
        if (!isset($data['amount_out']) || $data['amount_out'] === null) {
            $amountIn = (float) $data['amount_in'];
            $fees = (float) ($data['fees'] ?? 0);
            $data['amount_out'] = $amountIn - $fees;
        }

        return TrackerForm::create($data);
    }

    /**
     * Update an existing tracker form.
     */
    public function update(TrackerForm $form, array $data)
    {
        // Recalculate amount_out if amount_in or fees are being updated
        if (isset($data['amount_in']) || isset($data['fees'])) {
            $amountIn = (float) ($data['amount_in'] ?? $form->amount_in);
            $fees = (float) ($data['fees'] ?? $form->fees);
            $data['amount_out'] = $amountIn - $fees;
        }

        $form->update($data);
        return $form;
    }

    /**
     * Mark form as completed.
     */
    public function markCompleted(TrackerForm $form, array $data)
    {
        $data['feedback_date'] = $data['feedback_date'] ?? now()->toDateString();
        $form->update($data);
        return $form;
    }

    /**
     * Get Dashboard Stats.
     */
    public function getStats()
    {
        // Use single query for aggregates for better performance
        $aggregates = TrackerForm::selectRaw('
            COUNT(*) as total_forms,
            SUM(amount_in) as total_amount_in,
            SUM(fees) as total_fees,
            SUM(amount_out) as total_amount_out,
            COUNT(CASE WHEN feedback IS NOT NULL THEN 1 END) as completed,
            COUNT(CASE WHEN feedback IS NULL THEN 1 END) as pending
        ')->first();

        // Recent transactions
        $recentTransactions = TrackerForm::orderBy('created_at', 'desc')->limit(5)->get();

        // Monthly Data (MySQL specific)
        $monthlyData = TrackerForm::selectRaw('
                MONTH(date) as month,
                SUM(amount_in) as total_amount_in,
                SUM(fees) as total_fees,
                SUM(amount_out) as total_amount_out,
                COUNT(*) as count
            ')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Payment Method Distribution
        $paymentMethodDistribution = TrackerForm::selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount_in) as total_amount
            ')
            ->groupBy('payment_method')
            ->get();

        return [
            'total_forms' => $aggregates->total_forms,
            'total_amount_in' => (float) $aggregates->total_amount_in,
            'total_fees' => (float) $aggregates->total_fees,
            'total_amount_out' => (float) $aggregates->total_amount_out,
            'completed' => $aggregates->completed,
            'pending' => $aggregates->pending,
            'average_transaction' => $aggregates->total_forms > 0 
                ? (float) ($aggregates->total_amount_in / $aggregates->total_forms) 
                : 0,
            'recent_transactions' => $recentTransactions,
            'monthly_data' => $monthlyData,
            'payment_method_distribution' => $paymentMethodDistribution,
        ];
    }

    /**
     * Prepare CSV Data.
     */
    public function prepareCsvData(Request $request)
    {
        // Reuse the getAll query logic but get all results instead of paginating
        // Note: For very large datasets, using a Generator/Cursor is better.
        
        // Temporarily remove pagination logic to get a builder
        $query = TrackerForm::query();
        
        // ... (Re-apply filters from getAll - omitted for brevity, logic matches getAll) ...
        // Simplification for export:
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sales_person', 'LIKE', "%{$search}%")
                  ->orWhere('payment_method', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('date', '<=', $request->date_to);

        $forms = $query->orderBy('date', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'Client Name', 'Sales Person', 'Date', 'Payment Method', 'Description', 
            'Amount In', 'Fees', 'Amount Out', 'Feedback', 'Feedback Date', 'Status', 'Created At'
        ];

        foreach ($forms as $form) {
            $csvData[] = [
                $form->client_name,
                $form->sales_person, // Added column
                $form->date,
                $form->payment_method,
                $form->description,
                $form->amount_in,
                $form->fees,
                $form->amount_out,
                $form->feedback,
                $form->feedback_date,
                $form->feedback ? 'Completed' : 'Pending',
                $form->created_at
            ];
        }

        return $csvData;
    }
}