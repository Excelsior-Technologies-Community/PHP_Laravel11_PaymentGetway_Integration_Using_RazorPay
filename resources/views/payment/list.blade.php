<!DOCTYPE html>
<html>

<head>
    <title>Payments List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fb;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .analytics {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }

        .pagination {
            justify-content: center;
            gap: 8px;
        }

        .page-item .page-link {
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            color: #4f46e5;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: .3s;
        }

        .page-item .page-link:hover {
            background: #4f46e5;
            color: white;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            box-shadow: 0 8px 20px rgba(79, 70, 229, .4);
        }

        .page-item.disabled .page-link {
            background: #e9ecef;
            color: #999;
            box-shadow: none;
        }
    </style>

</head>

<body>

    <div class="container mt-5">

        <div class="row mb-4">

            <div class="col-md-4">

                <div class="card analytics shadow">

                    <div class="card-body">

                        <h6>Total Payments</h6>

                        <h2>{{ $totalPayments }}</h2>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card shadow">

                    <div class="card-body">

                        <h6>Successful Payments</h6>

                        <h2 class="text-success">

                            {{ $successPayments }}

                        </h2>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card shadow">

                    <div class="card-body">

                        <h6>Revenue</h6>

                        <h2 class="text-primary">

                            ₹{{ $revenue }}

                        </h2>

                    </div>

                </div>

            </div>

        </div>


        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                <h3 class="mb-0">Payments List</h3>

                <a href="{{ route('payment.form') }}" class="btn btn-primary btn-sm">

                    New Payment

                </a>

            </div>

            <div class="card-body">

                @if(session('success'))

                <div class="alert alert-success">

                    {{ session('success') }}

                </div>

                @endif


                <form method="GET">

                    <div class="row mb-4">

                        <div class="col-md-4">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search payment"
                                value="{{request('search')}}">

                        </div>


                        <div class="col-md-4">

                            <select
                                name="status"
                                class="form-select">

                                <option value="">

                                    All Status

                                </option>

                                <option value="success" {{request('status')=='success'?'selected':''}}>

                                    Success

                                </option>

                                <option value="pending" {{request('status')=='pending'?'selected':''}}>

                                    Pending

                                </option>

                                <option value="failed" {{request('status')=='failed'?'selected':''}}>

                                    Failed

                                </option>

                            </select>

                        </div>


                        <div class="col-md-2">

                            <button class="btn btn-dark w-100">

                                Search

                            </button>

                        </div>

                        <div class="col-md-2">

                            <a href="{{route('payments.list')}}"
                                class="btn btn-secondary w-100">

                                Reset

                            </a>

                        </div>

                    </div>

                </form>



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

                            <td>

                                {{ $p->id }}

                            </td>

                            <td>

                                ₹{{ $p->amount }}

                            </td>

                            <td>

                                {{ ucfirst($p->payment_method) }}

                            </td>

                            <td>

                                @if($p->status === 'success')

                                <span class="badge bg-success">

                                    Success

                                </span>

                                @elseif($p->status === 'pending')

                                <span class="badge bg-warning text-dark">

                                    Pending

                                </span>

                                @else

                                <span class="badge bg-danger">

                                    Failed

                                </span>

                                @endif

                            </td>


                            <td>

                                @if($p->deleted_at)

                                <a href="{{ route('payments.restore',$p->id) }}"
                                    class="btn btn-sm btn-success">

                                    Restore

                                </a>

                                @else

                                @if($p->status === 'success')

                                <a href="{{ route('payments.invoice',$p->id) }}"
                                    class="btn btn-sm btn-info text-white">

                                    Download PDF

                                </a>

                                @endif


                                <a href="{{ route('payments.delete',$p->id) }}"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this payment?');">

                                    Delete

                                </a>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="8" class="text-center">

                                No payments found

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>


                <div class="d-flex justify-content-end mt-4">

                    {{ $payments->onEachSide(1)->links() }}

                </div>

            </div>

        </div>

    </div>

</body>

</html>