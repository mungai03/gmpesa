<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mpesa; // Assuming you have an Mpesa model

class MpesaCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Get JSON callback from Safaricom
        $callback = $request->getContent();
        $data = json_decode($callback);

        // Log the callback if needed for testing
        \Log::info('M-PESA Callback: ', (array) $data);

        // Check if result code is 0 (success)
        $resultCode = $data->Body->stkCallback->ResultCode;

        if ($resultCode == 0) {
            $mpesaData = $data->Body->stkCallback->CallbackMetadata->Item;

            $phone = null;
            $amount = null;
            $mpesaReceipt = null;

            foreach ($mpesaData as $item) {
                if ($item->Name == 'PhoneNumber') {
                    $phone = $item->Value;
                } elseif ($item->Name == 'Amount') {
                    $amount = $item->Value;
                } elseif ($item->Name == 'MpesaReceiptNumber') {
                    $mpesaReceipt = $item->Value;
                }
            }

            // Update transaction in DB
            $mpesa = Mpesa::where('phone_number', $phone)
                ->where('status', 'Pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($mpesa) {
                $mpesa->update([
                    'status' => 'Successful',
                    
                ]);
            }
        } else {
            // Optionally log failure or update status
            \Log::warning('M-PESA transaction failed', (array) $data);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}

