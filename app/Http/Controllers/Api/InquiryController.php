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
            \Log::info('🔧 DEBUG ENDPOINT CALLED');
            
            // Test database connection
            $dbTest = DB::select('SELECT 1 as test');
            \Log::info('✅ DATABASE CONNECTION OK', ['result' => $dbTest]);
            
            // Test inquiry table structure
            $columns = \Schema::getColumnListing('inquiries');
            \Log::info('📋 INQUIRY TABLE COLUMNS', ['columns' => $columns]);
            
            // Test inquiry types
            $types = Inquiry::TYPES;
            \Log::info('📝 INQUIRY TYPES', ['types' => $types]);
            
            // Test mail configuration
            $mailConfig = [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from' => config('mail.from')
            ];
            \Log::info('📧 MAIL CONFIG', ['config' => $mailConfig]);
            
            return response()->json([
                'status' => 'success',
                'database' => 'connected',
                'columns' => $columns,
                'inquiry_types' => $types,
                'mail_config' => $mailConfig,
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ DEBUG ENDPOINT FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreInquiryRequest $request): JsonResponse
    {
        \Log::info('🔥 INQUIRY REQUEST RECEIVED', [
            'raw_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);
        
        try {
            \Log::info('🔍 STARTING VALIDATION');
            $data = $request->validated();
            \Log::info('✅ VALIDATION PASSED', ['validated_data' => $data]);
            
            DB::beginTransaction();
            \Log::info('🔄 DATABASE TRANSACTION STARTED');

            \Log::info('💾 CREATING INQUIRY IN DATABASE', ['final_data' => $data]);
            $inquiry = Inquiry::create($data);
            
            // Handle file uploads with media library
            if ($request->hasFile('car_license_photos')) {
                \Log::info('📁 UPLOADING CAR LICENSE PHOTOS');
                foreach ($request->file('car_license_photos') as $file) {
                    $inquiry->addMedia($file)->toMediaCollection('car_license_photos');
                }
                \Log::info('✅ CAR LICENSE PHOTOS UPLOADED');
            }

            if ($request->hasFile('part_photos')) {
                \Log::info('📁 UPLOADING PART PHOTOS');
                foreach ($request->file('part_photos') as $file) {
                    $inquiry->addMedia($file)->toMediaCollection('part_photos');
                }
                \Log::info('✅ PART PHOTOS UPLOADED');
            }
            \Log::info('✅ INQUIRY CREATED SUCCESSFULLY', [
                'inquiry_id' => $inquiry->id,
                'inquiry_data' => $inquiry->toArray()
            ]);

            // Send confirmation email
            \Log::info('📧 ATTEMPTING TO SEND EMAIL', ['email' => $inquiry->email]);
            try {
                $mailInstance = new InquiryConfirmation($inquiry);
                \Log::info('📧 MAIL INSTANCE CREATED');
                
                Mail::to($inquiry->email)->send($mailInstance);
                \Log::info('✅ EMAIL SENT SUCCESSFULLY', [
                    'inquiry_id' => $inquiry->id, 
                    'email' => $inquiry->email
                ]);
            } catch (\Exception $e) {
                \Log::error('❌ EMAIL SENDING FAILED', [
                    'inquiry_id' => $inquiry->id,
                    'email' => $inquiry->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            DB::commit();
            \Log::info('✅ DATABASE TRANSACTION COMMITTED');

            $response = [
                'message' => 'Inquiry submitted successfully',
                'data' => new InquiryResource($inquiry)
            ];
            \Log::info('🎉 INQUIRY PROCESS COMPLETED', ['response' => $response]);
            
            return response()->json($response, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ VALIDATION FAILED', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ INQUIRY CREATION FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Failed to create inquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
