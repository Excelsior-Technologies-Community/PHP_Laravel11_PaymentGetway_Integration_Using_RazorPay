<!DOCTYPE html>
<html>

<head>
    <title>Payments List</title> <!-- Page title -->

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5"> <!-- Container with top margin -->

        <!-- Card wrapper for payments table -->
        <div class="card shadow-sm"> <!-- Card with small shadow -->
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Payments List</h3> <!-- Card title -->
                <!-- Button to create new payment -->
                <a href="{{ route('payment.form') }}" class="btn btn-primary btn-sm">New Payment</a>
            </div>

            <div class="card-body">

                <!-- Success message alert -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Payments table -->
                <table class="table table-bordered table-hover table-striped"> <!-- Table with borders, hover effect, and striped rows -->
                    <thead class="table-secondary"> <!-- Light gray header -->
                        <tr>
                            <th>ID</th>
                            <th>Amount (INR)</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th> <!-- Column for action buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through payments -->
                        @forelse($payments as $p)
                            <!-- Highlight deleted payments in red -->
                            <tr @if($p->deleted_at) class="table-danger" @endif>
                                <td>{{ $p->id }}</td> <!-- Payment ID -->
                                <td>₹{{ $p->amount }}</td> <!-- Amount -->
                                <td>{{ ucfirst($p->payment_method) }}</td> <!-- Payment method capitalized -->
                                <td>
                                    <!-- Status badge -->
                                    @if($p->status === 'success')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($p->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>

                                <td>
                                    <!-- Restore or Delete button based on deleted_at -->
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
                            <!-- If no payments found -->
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
