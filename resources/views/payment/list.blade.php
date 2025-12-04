<!DOCTYPE html>
<html>

<head>
    <title>Payments List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Payments List</h3>
                <a href="{{ route('payment.form') }}" class="btn btn-primary btn-sm">New Payment</a>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Amount (INR)</th>
                            <th>Method</th>
                            <th>Status</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                            <tr @if($p->deleted_at) class="table-danger" @endif>
                                <td>{{ $p->id }}</td>
                                <td>₹{{ $p->amount }}</td>
                                <td>{{ ucfirst($p->payment_method) }}</td>
                                <td>
                                    @if($p->status === 'success')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($p->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>

                                <td>
                                    @if($p->deleted_at)
                                        <a href="{{ route('payments.restore', $p->id) }}"
                                            class="btn btn-sm btn-success">Restore</a>
                                    @else
                                        <a href="{{ route('payments.delete', $p->id) }}" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this payment?');">
                                            Delete
                                        </a>

                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</body>

</html>