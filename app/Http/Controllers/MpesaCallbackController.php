<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mpesa;

class MpesaCallbackController extends Controller
{

  public function DevBotNotification($message)
  {
    $botToken = "8035624799:AAFmUZZSqZZVIQpj0T4acv681wP_GjYf-kM";
    $method = "sendMessage";
    $chatIds = [7567614056]; // Add all chat IDs here
    $results = [];

    foreach ($chatIds as $chatId) {
      $parameters = [
        "chat_id" => $chatId,
        "text" => $message,
        "parse_mode" => "html"
      ];

      $url = "https://api.telegram.org/bot$botToken/$method";

      $curld = curl_init();
      curl_setopt($curld, CURLOPT_POST, true);
      curl_setopt($curld, CURLOPT_POSTFIELDS, $parameters);
      curl_setopt($curld, CURLOPT_URL, $url);
      curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
      $output = curl_exec($curld);
      curl_close($curld);

      $results[$chatId] = $output;
    }

    return $results;
  }


  public function handleCallback(Request $request)
  {
    $data = json_decode(file_get_contents('php://input'));
    $this->DevBotNotification('M-PESA Callback Received: ' . json_encode($data));

    $callback = $data->Body->stkCallback ?? null;
    $checkoutId = $callback->CheckoutRequestID ?? null;
    $resultCode = $callback->ResultCode ?? null;
    $resultDesc = $callback->ResultDesc ?? '';
    $metadata = $callback->CallbackMetadata->Item ?? [];

    $transaction = Mpesa::where('checkout_request_id', $checkoutId)
      ->where('status', 'Pending')
      ->orderBy('created_at', 'desc')
      ->first();

    if ($transaction) {
      if ($resultCode == 0) {
        $amount = $metadata[0]->Value ?? 0;
        $receipt = $metadata[1]->Value ?? '';
        $phone = $metadata[4]->Value ?? '';
        $transaction->update([
          'status' => 'Successful',
          'receipt_number' => $receipt,
        ]);
      } else {
        $transaction->update([
          'status' => 'Failed',
          'receipt_number' => '',
        ]);
      }
    }

    return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
  }
}
