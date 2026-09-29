{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Store Billing')</title>
    <style>
        :root {
            --primary-dark: #1e293b;
            --primary-green: #15803d;
            --primary-blue: #2563eb;
            --primary-blue-hover: #1d4ed8;
            --bg-gray: #f3f4f6;
            --border-color: #cbd5e1;
            --text-dark: #334155;
            --text-light: #64748b;
            --alert-bg: #fffbeb;
            --alert-border: #f59e0b;
            --alert-text: #b45309;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: var(--primary-dark);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 { margin: 0; font-size: 1.2rem; font-weight: 600; }
        .header span { font-size: 0.8rem; color: #94a3b8; }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--primary-dark);
        }

        /* Tables */
        .orders-table, .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }

        .orders-table th, .products-table th {
            background-color: #e2e8f0;
            text-align: left;
            padding: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .orders-table td, .products-table td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .orders-table tr:last-child td,
        .products-table tr:last-child td { border-bottom: none; }

        .orders-table tr:hover { background-color: #f8fafc; }

        .text-right { text-align: right; }
        .text-muted { color: var(--text-light); }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary { background-color: var(--primary-blue); color: white; }
        .btn-primary:hover { background-color: var(--primary-blue-hover); }

        .btn-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
        }
        .btn-link:hover { text-decoration: underline; }

        .btn-add {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            width: fit-content;
        }
        .btn-add:hover { background-color: var(--primary-blue-hover); }

        .btn-generate {
            background-color: var(--primary-green);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            text-align: center;
        }
        .btn-generate:hover { background-color: #166534; }

        /* Alerts */
        .alert {
            background-color: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #166534;
            font-size: 0.9rem;
        }

        .alert-box {
            background-color: var(--alert-bg);
            border: 1px solid var(--alert-border);
            border-radius: 4px;
            padding: 15px;
            color: var(--alert-text);
        }

        .alert-box h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .alert-box ul {
            margin: 0;
            padding-left: 20px;
            font-size: 0.85rem;
        }
        .alert-box li { margin-bottom: 5px; }

        /* Right column */
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Forms */
        .form-group { margin-bottom: 15px; }
        .form-row { display: flex; gap: 20px; }
        .form-row .form-group { flex: 1; }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        input[type="text"], input[type="email"], input[type="number"], select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 0.9rem;
            box-sizing: border-box;
            color: var(--text-dark);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-dark);
        }

        /* Payment box */
        .payment-box {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 15px;
            background-color: #f8fafc;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .payment-row.total {
            font-weight: 700;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--border-color);
        }

        .amount-given-input {
            margin-top: 15px;
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
        }

        .balance-return {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 10px;
            text-align: right;
        }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 20px;
            font-size: 0.85rem;
        }
        .pagination-wrapper nav { display: flex; justify-content: center; }

        /* Hide number input arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="header">
        <h1>@yield('header-title', 'Store Billing')</h1>
        <span>@yield('header-subtitle', '')</span>
    </div>

    @yield('content')

    @stack('scripts')
</body>
</html>