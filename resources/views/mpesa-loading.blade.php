<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Transaction Status</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #00c851 0%, #007e33 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .current-transaction {
            background: #f8f9fa;
            border-left: 5px solid #00c851;
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .current-transaction h3 {
            color: #00c851;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #00c851;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-message {
            background: #e8f5e8;
            border: 1px solid #00c851;
            color: #006600;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
        }

        .table-container {
            margin: 20px;
            overflow-x: auto;
        }

        .table-header {
            margin-bottom: 20px;
        }

        .table-header h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .transactions-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .transactions-table tr:hover {
            background-color: #f8f9fa;
        }

        .transactions-table tr.current-row {
            background-color: #e8f5e8;
            border-left: 5px solid #00c851;
        }

        .transactions-table tr.current-row:hover {
            background-color: #d4edda;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #99d6ff;
        }

        .amount {
            font-weight: 600;
            color: #00c851;
        }

        .phone-number {
            font-family: 'Courier New', monospace;
            color: #666;
        }

        .reference {
            font-weight: 500;
            color: #333;
        }

        .date-time {
            color: #666;
            font-size: 0.9rem;
        }

        .current-indicator {
            background: #00c851;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 10px;
        }

        .refresh-notice {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .actions {
            text-align: center;
            white-space: nowrap;
        }

        .delete-form {
            display: inline-block;
            margin: 0;
        }

        .delete-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(255, 107, 107, 0.3);
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 107, 107, 0.4);
        }

        .delete-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(255, 107, 107, 0.3);
        }

        .processing-label {
            color: #666;
            font-style: italic;
            font-size: 0.8rem;
        }

        .bulk-actions {
            margin: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .bulk-delete-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .bulk-delete-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .transactions-table {
                font-size: 0.8rem;
            }

            .transactions-table th,
            .transactions-table td {
                padding: 10px 8px;
            }
        }
    </style>
    
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏦 M-Pesa Transaction Center</h1>
            <p>Real-time transaction monitoring and status updates</p>
        </div>

        @if(session('success'))
        <div style="margin: 20px; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 8px;">
            <strong>✅ Success:</strong> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="margin: 20px; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px;">
            <strong>❌ Error:</strong> {{ session('error') }}
        </div>
        @endif

        @if($currentTransaction)
        <div class="current-transaction">
            <h3><span class="loading-spinner"></span>Current Transaction Processing</h3>
            <div class="status-message">
                <strong>STK Push Initiated Successfully!</strong><br>
                Please check your phone for the M-Pesa prompt and enter your PIN to complete the payment.
                <br><br>
                <strong>Transaction Details:</strong><br>
                📱 Phone: {{ $currentTransaction->phone_number }}<br>
                💰 Amount: KES {{ number_format($currentTransaction->amount, 2) }}<br>
                📝 Reference: {{ $currentTransaction->reference }}<br>
                📄 Description: {{ $currentTransaction->description }}
            </div>
            <p><span class="loading-spinner"></span><strong>Awaiting verification... Waiting for payment confirmation...</strong></p>
        </div>
        @endif

        <div class="table-container">
            <div class="table-header">
                <h2>📊 All M-Pesa Transactions</h2>
                <p>Complete transaction history with real-time status updates</p>
            </div>

            @if($allTransactions && $allTransactions->count() > 1)
            <div class="bulk-actions">
                <strong>Bulk Actions:</strong>
                <label>
                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()"> Select All
                </label>
                <button type="button" id="bulk-delete-btn" class="bulk-delete-btn" style="display: none;" onclick="bulkDelete()">
                    🗑️ Delete Selected (<span id="selected-count">0</span>)
                </button>
            </div>
            @endif

            @if($allTransactions && $allTransactions->count() > 0)
            <table class="transactions-table">
                <thead>
                    <tr>
                        @if($allTransactions && $allTransactions->count() > 1)
                        <th width="40">
                            <input type="checkbox" id="select-all-header" onchange="toggleSelectAll()">
                        </th>
                        @endif
                        <th>ID</th>
                        <th>Reference</th>
                        <th>Phone Number</th>
                        <th>Amount (KES)</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allTransactions as $transaction)
                    <tr class="{{ $currentTransaction && $transaction->id == $currentTransaction->id ? 'current-row' : '' }}">
                        @if($allTransactions && $allTransactions->count() > 1)
                        <td>
                            @if(!$currentTransaction || $transaction->id != $currentTransaction->id)
                                <input type="checkbox" class="transaction-checkbox" value="{{ $transaction->id }}" onchange="updateBulkDeleteButton()">
                            @endif
                        </td>
                        @endif
                        <td>
                            <strong>#{{ $transaction->id }}</strong>
                            @if($currentTransaction && $transaction->id == $currentTransaction->id)
                                <span class="current-indicator">Current</span>
                            @endif
                        </td>
                        <td class="reference">{{ $transaction->reference }}</td>
                        <td class="phone-number">{{ $transaction->phone_number }}</td>
                        <td class="amount">{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>
                            @php
                                $statusClass = 'status-pending';
                                $statusIcon = '⏳';

                                switch(strtolower($transaction->status)) {
                                    case 'completed':
                                    case 'success':
                                    case 'successful':
                                        $statusClass = 'status-completed';
                                        $statusIcon = '✅';
                                        break;
                                    case 'failed':
                                    case 'error':
                                    case 'cancelled':
                                        $statusClass = 'status-failed';
                                        $statusIcon = '❌';
                                        break;
                                    case 'processing':
                                    case 'initiated':
                                        $statusClass = 'status-processing';
                                        $statusIcon = '🔄';
                                        break;
                                    default:
                                        $statusClass = 'status-pending';
                                        $statusIcon = '⏳';
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusIcon }} {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="date-time">
                            {{ $transaction->created_at->format('M d, Y') }}<br>
                            <small>{{ $transaction->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="actions">
                            @if(!$currentTransaction || $transaction->id != $currentTransaction->id)
                                <form action="{{ route('mpesa.delete', $transaction->id) }}" method="POST" class="delete-form" onsubmit="return confirmDelete('{{ $transaction->reference }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" title="Delete Transaction">
                                        🗑️ Delete
                                    </button>
                                </form>
                            @else
                                <span class="processing-label">Processing...</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>No transactions found</h3>
                <p>Your transaction history will appear here once you make your first payment.</p>
            </div>
            @endif
        </div>

        <div class="refresh-notice">
            <p>🔄 This page automatically refreshes every 30 seconds to show the latest transaction status.</p>
            <p>You can also manually refresh your browser to check for updates.</p>
        </div>
    </div>
</body>
</html>
