<?php

namespace App\Http\Controllers;

use App\Models\TrackerForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrackerFormController extends Controller
{
    /**
     * Display a listing of the tracker forms.
     */
    public function index(Request $request)
    {
        try {
            $query = TrackerForm::query();
            
            // Apply filters if provided
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
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
            
            // Apply sorting
            $sortField = $request->get('sort_field', 'date');
            $sortDirection = $request->get('sort_direction', 'desc');
            
            $validSortFields = ['client_name', 'date', 'payment_method', 'amount_in', 'fees', 'amount_out'];
            if (in_array($sortField, $validSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('date', 'desc');
            }
            
            // Paginate results
            $perPage = $request->get('per_page', 10);
            $forms = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $forms->items(),
                'meta' => [
                    'current_page' => $forms->currentPage(),
                    'last_page' => $forms->lastPage(),
                    'per_page' => $forms->perPage(),
                    'total' => $forms->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracker forms.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created tracker form.
     */
    public function store(Request $request)
    {
         Log::info('Register payload:', $request->all());
        try {
            $validator = Validator::make($request->all(), [
                'client_name' => 'required|string|max:255',
                'date' => 'required|date',
                'payment_method' => ['required', 'string', Rule::in(['Cash', 'Bank Transfer', 'Mobile Money', 'Credit Card', 'Other'])],
                'description' => 'nullable|string',
                'amount_in' => 'required|numeric|min:0',
                'fees' => 'nullable|numeric|min:0',
                'amount_out' => 'nullable|numeric|min:0',
                'feedback' => 'nullable|string',
                'feedback_date' => 'nullable|date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Calculate amount_out if not provided
            $validated = $validator->validated();
            if (!isset($validated['amount_out']) || empty($validated['amount_out'])) {
                $amountIn = (float) $validated['amount_in'];
                $fees = (float) ($validated['fees'] ?? 0);
                $validated['amount_out'] = $amountIn - $fees;
            }
            
            $trackerForm = TrackerForm::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tracker form created successfully.',
                'data' => $trackerForm
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tracker form.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tracker form.
     */
    public function show($id)
    {
        try {
            $trackerForm = TrackerForm::find($id);
            
            if (!$trackerForm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracker form not found.'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $trackerForm
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracker form.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tracker form.
     */
    public function update(Request $request, $id)
    {
        try {
            $trackerForm = TrackerForm::find($id);
            
            if (!$trackerForm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracker form not found.'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'client_name' => 'sometimes|required|string|max:255',
                'date' => 'sometimes|required|date',
                'payment_method' => ['sometimes', 'required', 'string', Rule::in(['Cash', 'Bank Transfer', 'Mobile Money', 'Credit Card', 'Other'])],
                'description' => 'nullable|string',
                'amount_in' => 'sometimes|required|numeric|min:0',
                'fees' => 'nullable|numeric|min:0',
                'amount_out' => 'nullable|numeric|min:0',
                'feedback' => 'nullable|string',
                'feedback_date' => 'nullable|date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $validated = $validator->validated();
            
            // Recalculate amount_out if amount_in or fees changed
            if (isset($validated['amount_in']) || isset($validated['fees'])) {
                $amountIn = (float) ($validated['amount_in'] ?? $trackerForm->amount_in);
                $fees = (float) ($validated['fees'] ?? $trackerForm->fees);
                $validated['amount_out'] = $amountIn - $fees;
            }
            
            $trackerForm->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tracker form updated successfully.',
                'data' => $trackerForm
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tracker form.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tracker form.
     */
    public function destroy($id)
    {
        try {
            $trackerForm = TrackerForm::find($id);
            
            if (!$trackerForm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracker form not found.'
                ], 404);
            }
            
            $trackerForm->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tracker form deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tracker form.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics.
     */
    public function stats()
    {
        try {
            $totalForms = TrackerForm::count();
            $totalAmountIn = TrackerForm::sum('amount_in');
            $totalFees = TrackerForm::sum('fees');
            $totalAmountOut = TrackerForm::sum('amount_out');
            $completed = TrackerForm::whereNotNull('feedback')->count();
            $pending = TrackerForm::whereNull('feedback')->count();
            
            // Get recent transactions
            $recentTransactions = TrackerForm::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Get monthly totals for the current year
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
            
            // Get payment method distribution
            $paymentMethodDistribution = TrackerForm::selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount_in) as total_amount
            ')
            ->groupBy('payment_method')
            ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_forms' => $totalForms,
                    'total_amount_in' => (float) $totalAmountIn,
                    'total_fees' => (float) $totalFees,
                    'total_amount_out' => (float) $totalAmountOut,
                    'completed' => $completed,
                    'pending' => $pending,
                    'average_transaction' => $totalForms > 0 ? (float) ($totalAmountIn / $totalForms) : 0,
                    'recent_transactions' => $recentTransactions,
                    'monthly_data' => $monthlyData,
                    'payment_method_distribution' => $paymentMethodDistribution,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a form as completed.
     */
    public function markAsCompleted(Request $request, $id)
    {
        try {
            $trackerForm = TrackerForm::find($id);
            
            if (!$trackerForm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracker form not found.'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'feedback' => 'required|string',
                'feedback_date' => 'nullable|date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $validated = $validator->validated();
            $validated['feedback_date'] = $validated['feedback_date'] ?? now()->toDateString();
            
            $trackerForm->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tracker form marked as completed.',
                'data' => $trackerForm
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark form as completed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete tracker forms.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:tracker_forms,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $deletedCount = TrackerForm::whereIn('id', $request->ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} tracker forms.",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tracker forms.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export tracker forms to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = TrackerForm::query();
            
            // Apply filters if provided
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
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
            
            $forms = $query->orderBy('date', 'desc')->get();
            
            // Prepare CSV content
            $csvData = [];
            $csvData[] = [
                'Client Name',
                'Date',
                'Payment Method',
                'Description',
                'Amount In',
                'Fees',
                'Amount Out',
                'Feedback',
                'Feedback Date',
                'Status',
                'Created At'
            ];
            
            foreach ($forms as $form) {
                $csvData[] = [
                    $form->client_name,
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
            
            // Convert to CSV string
            $csvContent = '';
            foreach ($csvData as $row) {
                $csvContent .= implode(',', array_map(function ($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $row)) . "\n";
            }
            
            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, 'tracker-forms-' . date('Y-m-d') . '.csv', [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="tracker-forms-' . date('Y-m-d') . '.csv"',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export tracker forms.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}