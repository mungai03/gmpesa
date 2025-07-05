<?php

namespace App\Http\Controllers;

use App\Models\Mpesa;
use Illuminate\Http\Request;
use MpesaSdk\StkPush;

class mpesaController extends Controller
{
  //
  public function stkpush()
  {
    return view('mpesastkpushView');
  }
  public function stkpush_init(Request $request)
  {
    $request->validate([
      'phone_number' => 'required|numeric',
      'amount' => 'required|numeric',
      'reference' => 'required',
      'description' => 'required',
    ]);
    $stkpush = new StkPush();
    $phone_number = $request->phone_number;
    $amount = $request->amount;
    $reference = $request->reference;
    $description = $request->description;

    // Initiate the STK push
    $mpesa_res = $stkpush->initiate($phone_number, $amount, $reference, $description);
    $data = json_decode($mpesa_res, true);

    // Check if the response contains an error
    if (isset($data['errorMessage'])) {
      return response()->json([
        'error' => $data['errorMessage']
      ], 400);
    }

    // If no error, save the transaction to the database
    if (isset($data['ResponseCode']) && $data['ResponseCode'] == '0') {
      $checkoutRequestId = $data['CheckoutRequestID'];
      // Save the transaction details to the database
      Mpesa::create([
        'phone_number' => $phone_number,
        'amount' => $amount,
        'reference' => $reference,
        'description' => $description,
        'checkout_request_id' => $checkoutRequestId,
        'status' => 'Pending',
      ]);
      // Return view with popup showing STK push has been initiated and awaiting verification

      // Get the current transaction
      $currentTransaction = Mpesa::where('reference', $reference)->first();

      // Get all transactions ordered by most recent first
      $allTransactions = Mpesa::orderBy('created_at', 'desc')->get();

      return view('mpesa-loading', compact('currentTransaction', 'allTransactions'));
    } else {
      return response()->json([
        'error' => 'Failed to initiate STK push'
      ], 500);
    }
  }
}
