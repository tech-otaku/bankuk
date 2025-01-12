<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- <a class="navbar-brand" href="#">Navbar</a> -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <!-- Summary -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_summary.php">Summary</a>
                </li>
            <!-- Monthly Spend -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_monthly_spend.php">Monthly Spend</a>
                </li>
            <!-- Reconcilliation -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_reconcilliation.php">Reconcilliation</a>
                </li>
            <!-- Accounting Periods -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_accounting_periods.php">Accounting Periods</a>
                </li>
            <!-- Accounts -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_accounts.php">Accounts</a>
                </li>
            <!-- Banks -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_banks.php">Banks</a>
                </li>
            <!-- Entities -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_entities.php">Entities</a>
                </li>
            <!-- Pre-fills -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_prefills.php">Pre-fills</a>
                </li>
            <!-- Regular Debits -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_regular_debits.php">Regular Debits</a>
                </li>
            <!-- Regular Debit Types -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_regular_debit_types.php">Regular Debit Types</a>
                </li>
            <!-- Transactions -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_transactions.php">Transactions</a>
                </li>
            <!-- Transaction Types -->
                <li class="nav-item">
                    <a class="nav-link" href="bu_manage_transaction_types.php">Transaction Types</a>
                </li>
            <!-- Add Record Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown-add" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Add Record
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown-add">
                        <li><a class="dropdown-item" href="bu_add_accounting_period.php">Accounting Period</a></li>
                        <li><a class="dropdown-item" href="bu_add_account.php">Account</a></li>
                        <li><a class="dropdown-item" href="bu_add_bank.php">Bank</a></li>
                        <li><a class="dropdown-item" href="bu_add_entity.php">Entity</a></li>
                        <li><a class="dropdown-item" href="bu_add_prefill.php">Pre-fill</a></li>
                        <li><a class="dropdown-item" href="bu_add_regular_debit.php">Regular Debit</a></li>
                        <li><a class="dropdown-item" href="bu_add_regular_debit_type.php">Regular Debit Type</a></li>
                        <li><a class="dropdown-item" href="bu_add_transaction.php">Transaction</a></li>
                        <li><a class="dropdown-item" href="bu_add_transaction_type.php">Transaction Type</a></li>
                    </ul>
                </li>
            <!-- System -->
            <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown-system" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        System
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown-system">
                        <li><a class="dropdown-item" href="bu_view_settings.php">Settings</a></li>
                        <li><a class="dropdown-item" href="bu_password.php">Change Password</a></li>
                    </ul>
                </li>  
            <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <!-- Disabled -->
                <li class="nav-item">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                </li>
            </ul>
        <!-- Search -->
            <!-- 
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            -->
        </div>
    </div>
</nav>
