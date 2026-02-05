<?php

namespace App\Http\Controllers;

use App\Models\TrackerForm;
use App\Services\TrackerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrackerFormController extends Controller
{
    protected $trackerService;

    // Inject the Service
    public function __construct(TrackerService $trackerService)
    {
        $this->trackerService = $trackerService;
    }

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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'client_name' => 'required|string|max:255',
                'sales_person' => 'required|string|max:255', // Validating new column
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

            $form = $this->trackerService->create($validator->validated());

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

    public function show($id)
    {
        $form = TrackerForm::find($id);
        if (!$form) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        return response()->json(['success' => true, 'data' => $form]);
    }

    public function update(Request $request, $id)
    {
        try {
            $form = TrackerForm::find($id);
            if (!$form) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

            $validator = Validator::make($request->all(), [
                'client_name' => 'sometimes|required|string|max:255',
                'sales_person' => 'sometimes|required|string|max:255', // Validating new column
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

    public function destroy($id)
    {
        try {
            $form = TrackerForm::find($id);
            if (!$form) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

            $form->delete();
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:tracker_forms,id',
        ]);

        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        try {
            TrackerForm::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true, 'message' => 'Bulk delete successful.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Bulk delete failed.'], 500);
        }
    }

    public function stats()
    {
        try {
            $stats = $this->trackerService->getStats();
            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch stats.'], 500);
        }
    }

    public function markAsCompleted(Request $request, $id)
    {
        try {
            $form = TrackerForm::find($id);
            if (!$form) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

            $validator = Validator::make($request->all(), [
                'feedback' => 'required|string',
                'feedback_date' => 'nullable|date',
            ]);

            if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

            $updatedForm = $this->trackerService->markCompleted($form, $validator->validated());

            return response()->json(['success' => true, 'message' => 'Marked as completed.', 'data' => $updatedForm]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed.'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $csvData = $this->trackerService->prepareCsvData($request);

            // Convert array to CSV string
            $csvContent = '';
            foreach ($csvData as $row) {
                $csvContent .= implode(',', array_map(function ($value) {
                    // Escape quotes and wrap in quotes
                    return '"' . str_replace('"', '""', (string)$value) . '"';
                }, $row)) . "\n";
            }

            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, 'tracker-forms-' . date('Y-m-d') . '.csv', [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="tracker-forms.csv"',
            ]);
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Export failed.'], 500);
        }
    }
}