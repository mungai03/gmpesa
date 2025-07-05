<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Transaction Table</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            overflow-x: auto;
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

        @media (max-width: 768px) {
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
    <div class="table-container">

        @if($allTransactions && $allTransactions->count() > 0)
        <table class="transactions-table">
            <thead>
                <tr>
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

    <script>
        function confirmDelete(reference) {
            return confirm('Are you sure you want to delete transaction: ' + reference + '?');
        }
    </script>
</body>
</html>
