<?php

namespace App\Services;

use App\Models\TrackerForm;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrackerService
{
    /**
     * Get paginated and filtered tracker forms - SHOW ALL FORMS (no user filtering)
     */
    public function getAll(Request $request)
    {
        $query = TrackerForm::with('user')->select('tracker_forms.*'); // Eager load user

        // 1. Search (Added sales_person here)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sales_person', 'LIKE', "%{$search}%")
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

        // 5. Filter by user (optional - if you want to filter by specific user)
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // 6. Sorting
        $sortField = $request->get('sort_field', 'date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $validSortFields = ['client_name', 'date', 'payment_method', 'amount_in', 'fees', 'amount_out', 'sales_person'];
        
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('date', 'desc');
        }

        // 7. Pagination
        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Create a new tracker form - AUTOMATICALLY ASSIGN TO CURRENT USER
     */
    public function create(array $data)
    {
        // Auto-calculate amount_out if missing
        if (!isset($data['amount_out']) || $data['amount_out'] === null) {
            $amountIn = (float) $data['amount_in'];
            $fees = (float) ($data['fees'] ?? 0);
            $data['amount_out'] = $amountIn - $fees;
        }

        // Automatically assign the current user's ID
        $data['user_id'] = Auth::id();

        return TrackerForm::create($data);
    }

    /**
     * Update an existing tracker form - ONLY IF USER OWNS IT
     * Note: Authorization check should be done in controller before calling this
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
        return $form->fresh()->load('user');
    }

    /**
     * Mark form as completed - ONLY IF USER OWNS IT
     * Note: Authorization check should be done in controller before calling this
     */
    public function markCompleted(TrackerForm $form, array $data)
    {
        $data['feedback_date'] = $data['feedback_date'] ?? now()->toDateString();
        $form->update($data);
        return $form->fresh()->load('user');
    }

    /**
     * Get Dashboard Stats - GLOBAL STATS (all users)
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

        // Recent transactions - show all users' recent transactions
        $recentTransactions = TrackerForm::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly Data (MySQL specific) - all users
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

        // Payment Method Distribution - all users
        $paymentMethodDistribution = TrackerForm::selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount_in) as total_amount
            ')
            ->groupBy('payment_method')
            ->get();

        // Get current user's personal stats (optional)
        $userStats = null;
        if (Auth::check()) {
            $userAggregates = TrackerForm::where('user_id', Auth::id())
                ->selectRaw('
                    COUNT(*) as total_forms,
                    SUM(amount_in) as total_amount_in,
                    SUM(fees) as total_fees,
                    SUM(amount_out) as total_amount_out,
                    COUNT(CASE WHEN feedback IS NOT NULL THEN 1 END) as completed,
                    COUNT(CASE WHEN feedback IS NULL THEN 1 END) as pending
                ')->first();
            
            $userStats = [
                'total_forms' => $userAggregates->total_forms ?? 0,
                'total_amount_in' => (float) ($userAggregates->total_amount_in ?? 0),
                'total_fees' => (float) ($userAggregates->total_fees ?? 0),
                'total_amount_out' => (float) ($userAggregates->total_amount_out ?? 0),
                'completed' => $userAggregates->completed ?? 0,
                'pending' => $userAggregates->pending ?? 0,
            ];
        }

        return [
            'total_forms' => $aggregates->total_forms ?? 0,
            'total_amount_in' => (float) ($aggregates->total_amount_in ?? 0),
            'total_fees' => (float) ($aggregates->total_fees ?? 0),
            'total_amount_out' => (float) ($aggregates->total_amount_out ?? 0),
            'completed' => $aggregates->completed ?? 0,
            'pending' => $aggregates->pending ?? 0,
            'average_transaction' => ($aggregates->total_forms ?? 0) > 0 
                ? (float) (($aggregates->total_amount_in ?? 0) / $aggregates->total_forms) 
                : 0,
            'recent_transactions' => $recentTransactions,
            'monthly_data' => $monthlyData,
            'payment_method_distribution' => $paymentMethodDistribution,
            'user_stats' => $userStats, // Optional: include user's personal stats
        ];
    }

    /**
     * Get stats for a specific user (for personal dashboard)
     */
    public function getUserStats($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $aggregates = TrackerForm::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_forms,
                SUM(amount_in) as total_amount_in,
                SUM(fees) as total_fees,
                SUM(amount_out) as total_amount_out,
                COUNT(CASE WHEN feedback IS NOT NULL THEN 1 END) as completed,
                COUNT(CASE WHEN feedback IS NULL THEN 1 END) as pending
            ')->first();

        return [
            'total_forms' => $aggregates->total_forms ?? 0,
            'total_amount_in' => (float) ($aggregates->total_amount_in ?? 0),
            'total_fees' => (float) ($aggregates->total_fees ?? 0),
            'total_amount_out' => (float) ($aggregates->total_amount_out ?? 0),
            'completed' => $aggregates->completed ?? 0,
            'pending' => $aggregates->pending ?? 0,
            'average_transaction' => ($aggregates->total_forms ?? 0) > 0 
                ? (float) (($aggregates->total_amount_in ?? 0) / $aggregates->total_forms) 
                : 0,
        ];
    }

    /**
     * Get only the current user's forms (for "My Forms" view)
     */
    public function getMyForms(Request $request)
    {
        $query = TrackerForm::where('user_id', Auth::id());

        // Apply all the same filters as getAll()
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sales_person', 'LIKE', "%{$search}%")
                  ->orWhere('payment_method', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('feedback');
            } elseif ($request->status === 'pending') {
                $query->whereNull('feedback');
            }
        }

        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $sortField = $request->get('sort_field', 'date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $validSortFields = ['client_name', 'date', 'payment_method', 'amount_in', 'fees', 'amount_out', 'sales_person'];
        
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('date', 'desc');
        }

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Prepare CSV Data - EXPORT ALL FORMS (admin/manager export)
     */
    public function prepareCsvData(Request $request)
    {
        $query = TrackerForm::with('user');
        
        // Apply filters
        if ($request->has('search') && $request->search) {
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
        
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('feedback');
            } elseif ($request->status === 'pending') {
                $query->whereNull('feedback');
            }
        }

        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        $forms = $query->orderBy('date', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'ID', 'Client Name', 'Sales Person', 'Date', 'Payment Method', 'Description', 
            'Amount In (KES)', 'Fees (KES)', 'Amount Out (KES)', 'Feedback', 'Feedback Date', 
            'Status', 'Created By', 'User ID', 'Created At'
        ];

        foreach ($forms as $form) {
            $csvData[] = [
                $form->id,
                $form->client_name,
                $form->sales_person,
                $form->date,
                $form->payment_method,
                $form->description ?? '',
                number_format($form->amount_in, 2),
                number_format($form->fees ?? 0, 2),
                number_format($form->amount_out ?? 0, 2),
                $form->feedback ?? '',
                $form->feedback_date,
                $form->feedback ? 'Completed' : 'Pending',
                $form->user->name ?? 'Unknown',
                $form->user_id,
                $form->created_at,
            ];
        }

        return $csvData;
    }

    /**
     * Prepare CSV Data for ONLY the current user's forms
     */
    public function prepareMyCsvData(Request $request)
    {
        $query = TrackerForm::where('user_id', Auth::id());
        
        // Apply same filters as prepareCsvData but only for current user
        if ($request->has('search') && $request->search) {
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
        
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('feedback');
            } elseif ($request->status === 'pending') {
                $query->whereNull('feedback');
            }
        }

        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        $forms = $query->orderBy('date', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'ID', 'Client Name', 'Sales Person', 'Date', 'Payment Method', 'Description', 
            'Amount In (KES)', 'Fees (KES)', 'Amount Out (KES)', 'Feedback', 'Feedback Date', 
            'Status', 'Created At'
        ];

        foreach ($forms as $form) {
            $csvData[] = [
                $form->id,
                $form->client_name,
                $form->sales_person,
                $form->date,
                $form->payment_method,
                $form->description ?? '',
                number_format($form->amount_in, 2),
                number_format($form->fees ?? 0, 2),
                number_format($form->amount_out ?? 0, 2),
                $form->feedback ?? '',
                $form->feedback_date,
                $form->feedback ? 'Completed' : 'Pending',
                $form->created_at,
            ];
        }

        return $csvData;
    }

    /**
     * Check if user owns a form
     */
    public function isOwner(TrackerForm $form, $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        return $form->user_id === $userId;
    }

    /**
     * Get forms count by user
     */
    public function getFormsCountByUser()
    {
        return TrackerForm::select('user_id', DB::raw('count(*) as total'))
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();
    }
}