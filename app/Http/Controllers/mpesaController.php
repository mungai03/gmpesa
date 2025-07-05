<?php

namespace App\Http\Controllers;

use App\Models\Mpesa;
use Illuminate\Http\Request;
use MpesaSdk\StkPush;

class mpesaController extends Controller
{
    //
    public function stkpush(){
        return view('mpesastkpushView');
    }
    public function stkpush_init(Request $request){
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

        Mpesa::create([
            'phone_number' => $phone_number,
            'amount' => $amount,
            'reference' => $reference,
            'description' => $description,
            'status' => 'Pending',
        ]);

        // Initiate the STK push
        $stkpush->initiate($phone_number, $amount, $reference, $description);

        // Return view with popup showing STK push has been initiated and awaiting verification

        // Get the current transaction
        $currentTransaction = Mpesa::where('reference', $reference)->first();

        // Get all transactions ordered by most recent first
        $allTransactions = Mpesa::orderBy('created_at', 'desc')->get();

        return view('mpesa-loading', compact('currentTransaction', 'allTransactions'));
    }

    /**
     * Delete a single transaction
     */
    public function deleteTransaction($id)
    {
        try {
            $transaction = Mpesa::findOrFail($id);

            // Prevent deletion of currently processing transactions
            if ($transaction->status === 'Pending' || $transaction->status === 'Processing') {
                return redirect()->back()->with('error', 'Cannot delete a transaction that is currently being processed.');
            }

            $transaction->delete();

            return redirect()->back()->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete transaction. Please try again.');
        }
    }

    /**
     * Delete multiple transactions (bulk delete)
     */
    public function bulkDeleteTransactions(Request $request)
    {
        try {
            $transactionIds = $request->input('transaction_ids', []);

            if (empty($transactionIds)) {
                return redirect()->back()->with('error', 'No transactions selected for deletion.');
            }

            // Get transactions and check if any are currently processing
            $transactions = Mpesa::whereIn('id', $transactionIds)->get();
            $processingTransactions = $transactions->whereIn('status', ['Pending', 'Processing']);

            if ($processingTransactions->count() > 0) {
                return redirect()->back()->with('error', 'Cannot delete transactions that are currently being processed.');
            }

            // Delete the transactions
            $deletedCount = Mpesa::whereIn('id', $transactionIds)->delete();

            return redirect()->back()->with('success', "Successfully deleted {$deletedCount} transaction(s).");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete transactions. Please try again.');
        }
    }

}
