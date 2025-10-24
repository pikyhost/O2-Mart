<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Mail\InquiryConfirmation;
use App\Models\Inquiry;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}
    
    public function debug(): JsonResponse
    {
        try {
            // Test database connection
            $dbTest = DB::select('SELECT 1 as test');
            
            // Test inquiry table structure
            $columns = \Schema::getColumnListing('inquiries');
            
            // Test inquiry types
            $types = Inquiry::TYPES;
            
            // Test mail configuration
            $mailConfig = [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from' => config('mail.from')
            ];
            
            return response()->json([
                'status' => 'success',
                'database' => 'connected',
                'columns' => $columns,
                'inquiry_types' => $types,
                'mail_config' => $mailConfig,
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreInquiryRequest $request): JsonResponse
    {
        // Debug logging
        \Log::info('Inquiry Request Raw Data', [
            'all' => $request->all(),
            'quantity' => $request->input('quantity'),
            'quantities' => $request->input('quantities'),
            'type' => $request->input('type')
        ]);
        
        try {
            $data = $request->validated();
            
            \Log::info('Inquiry Validated Data', [
                'data' => $data,
                'quantity' => $data['quantity'] ?? 'NOT SET'
            ]);
            
            DB::beginTransaction();


            // Remove file fields from data since we handle them with media library
            $inquiryData = collect($data)->except(['car_license_photos', 'part_photos'])->toArray();

            \Log::info('Inquiry Data Before Create', [
                'inquiryData' => $inquiryData,
                'quantity' => $inquiryData['quantity'] ?? 'NOT SET'
            ]);

            $inquiry = Inquiry::create($inquiryData);
            
            \Log::info('Inquiry After Create', [
                'id' => $inquiry->id,
                'quantity' => $inquiry->quantity,
                'type' => $inquiry->type
            ]);
            
            // Handle file uploads with media library

            
            if ($request->hasFile('car_license_photos')) {

                $files = is_array($request->file('car_license_photos')) ? $request->file('car_license_photos') : [$request->file('car_license_photos')];
                foreach ($files as $file) {
                    $inquiry->addMedia($file)->toMediaCollection('car_license_photos');
                }

            }

            if ($request->hasFile('part_photos')) {

                $files = is_array($request->file('part_photos')) ? $request->file('part_photos') : [$request->file('part_photos')];
                foreach ($files as $file) {
                    $inquiry->addMedia($file)->toMediaCollection('part_photos');
                }

            }


            // Send confirmation email

            try {
                $mailInstance = new InquiryConfirmation($inquiry);

                
                Mail::to($inquiry->email)->send($mailInstance);

            } catch (\Exception $e) {
                \Log::error('Email sending failed', [
                    'inquiry_id' => $inquiry->id,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();


            $response = [
                'message' => 'Inquiry submitted successfully',
                'data' => new InquiryResource($inquiry)
            ];

            
            return response()->json($response, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Inquiry creation failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Failed to create inquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
