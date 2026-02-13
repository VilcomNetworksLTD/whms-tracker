<?php
// app/Http/Controllers/TrackerFormController.php

namespace App\Http\Controllers;

use App\Models\TrackerForm;
use App\Services\TrackerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TrackerFormController extends Controller
{
    protected $trackerService;

    public function __construct(TrackerService $trackerService)
    {
        $this->trackerService = $trackerService;
    }

    /**
     * Display ALL forms (no filtering by user)
     */
    public function index(Request $request)
    {
        try {
            $forms = $this->trackerService->getAll($request);
            
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
            Log::error('Fetch Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch forms.'], 500);
        }
    }

    /**
     * Store a newly created form for the authenticated user
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'client_name' => 'required|string|max:255',
                'sales_person' => 'required|string|max:255',
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
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();
            
            $form = $this->trackerService->create($data);

            return response()->json([
                'success' => true, 
                'message' => 'Tracker form created successfully.', 
                'data' => $form
            ], 201);
        } catch (\Exception $e) {
            Log::error('Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create form.'], 500);
        }
    }

    /**
     * Display ANY form (no ownership check for viewing)
     */
    public function show($id)
    {
        $form = TrackerForm::with('user')->find($id);
            
        if (!$form) {
            return response()->json(['success' => false, 'message' => 'Form not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $form]);
    }

    /**
     * Update ONLY if the form belongs to the authenticated user
     */
    public function update(Request $request, $id)
    {
        try {
            $form = TrackerForm::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$form) {
                return response()->json(['success' => false, 'message' => 'You can only edit your own forms.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'client_name' => 'sometimes|required|string|max:255',
                'sales_person' => 'sometimes|required|string|max:255',
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
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $updatedForm = $this->trackerService->update($form, $validator->validated());

            return response()->json([
                'success' => true, 
                'message' => 'Updated successfully.', 
                'data' => $updatedForm
            ]);
        } catch (\Exception $e) {
            Log::error('Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update form.'], 500);
        }
    }

    /**
     * Delete ONLY if the form belongs to the authenticated user
     */
    public function destroy($id)
    {
        try {
            $form = TrackerForm::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$form) {
                return response()->json(['success' => false, 'message' => 'You can only delete your own forms.'], 403);
            }

            $form->delete();
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    /**
     * Bulk delete ONLY forms that belong to the authenticated user
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:tracker_forms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $userFormsCount = TrackerForm::whereIn('id', $request->ids)
                ->where('user_id', Auth::id())
                ->count();
            
            if ($userFormsCount === 0) {
                return response()->json(['success' => false, 'message' => 'None of the selected forms belong to you.'], 403);
            }
            
            $deleted = TrackerForm::whereIn('id', $request->ids)
                ->where('user_id', Auth::id())
                ->delete();
            
            $skipped = count($request->ids) - $deleted;
            
            $message = $deleted . ' form(s) deleted successfully.';
            if ($skipped > 0) {
                $message .= ' ' . $skipped . ' form(s) were skipped because they don\'t belong to you.';
            }
            
            return response()->json([
                'success' => true, 
                'message' => $message,
                'deleted' => $deleted,
                'skipped' => $skipped
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Bulk delete failed.'], 500);
        }
    }

    /**
     * Mark as completed ONLY if the form belongs to the authenticated user
     */
    public function markAsCompleted(Request $request, $id)
    {
        try {
            $form = TrackerForm::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$form) {
                return response()->json(['success' => false, 'message' => 'You can only update your own forms.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'feedback' => 'required|string',
                'feedback_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $updatedForm = $this->trackerService->markCompleted($form, $validator->validated());

            return response()->json([
                'success' => true, 
                'message' => 'Marked as completed.', 
                'data' => $updatedForm
            ]);
        } catch (\Exception $e) {
            Log::error('Mark Completed Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Operation failed.'], 500);
        }
    }

    /**
     * Get statistics for ALL forms (global stats)
     */
    public function stats()
    {
        try {
            $stats = $this->trackerService->getStats();
            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            Log::error('Stats Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch stats.'], 500);
        }
    }

    /**
     * Export ALL forms (no user filtering)
     */
    public function export(Request $request)
    {
        try {
            $csvData = $this->trackerService->prepareCsvData($request);

            $csvContent = '';
            foreach ($csvData as $row) {
                $csvContent .= implode(',', array_map(function ($value) {
                    return '"' . str_replace('"', '""', (string)$value) . '"';
                }, $row)) . "\n";
            }

            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, 'tracker-forms-' . date('Y-m-d') . '.csv', [
                'Content-Type' => 'text/csv',
            ]);
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Export failed.'], 500);
        }
    }
}