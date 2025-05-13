<?php



    $publicHolidays = [
        // 2024
        // https://www.officesimplify.com/bank-holidays-uk-2024
            '2024-01-01', '2024-03-29', '2024-04-01', '2024-05-06', '2024-05-27', '2024-08-26', '2024-12-25', '2024-12-26', 
        // 2025
        // https://www.officesimplify.com/bank-holidays-uk-2025
            '2025-01-01', '2025-04-18', '2025-04-21', '2025-05-05', '2025-05-26', '2025-08-25', '2025-12-25', '2025-12-26',
        // 2026
        // https://www.officesimplify.com/bank-holidays-uk-2026
            '2026-01-01', '2026-04-03', '2026-04-06', '2026-05-04', '2026-05-25', '2026-08-31', '2026-12-25', '2026-12-28',
        // 2027
        // https://www.officesimplify.com/bank-holidays-uk-2027
            '2027-01-01', '2027-03-26', '2027-03-29', '2027-05-03', '2027-05-31', '2027-08-30', '2027-12-27', '2027-12-28',
        // 2028
        // https://www.officesimplify.com/bank-holidays-uk-2028
            '2028-01-03', '2028-04-14', '2028-04-17', '2028-05-01', '2028-05-29', '2028-08-28', '2028-12-25', '2028-12-26',
        // 2029
        // https://www.officesimplify.com/bank-holidays-uk-2029
            '2029-01-01', '2029-03-30', '2029-04-02', '2029-05-07', '2029-05-28', '2029-08-27', '2029-12-25', '2029-12-26',
        // 2030
        // https://www.officesimplify.com/bank-holidays-uk-2030
            '2030-01-01', '2030-04-19', '2030-04-22', '2030-05-06', '2030-05-27', '2030-08-26', '2030-12-25', '2030-12-26',
        // 2031
        // https://www.officesimplify.com/bank-holidays-uk-2031
            '2031-01-01', '2031-04-11', '2031-04-14', '2031-05-05', '2031-05-26', '2031-08-25', '2031-12-25', '2031-12-26',
        // 2032
        // https://www.officesimplify.com/bank-holidays-uk-2032
            '2032-01-01', '2032-03-26', '2032-03-29', '2032-05-03', '2032-05-31', '2032-08-30', '2032-12-27', '2032-12-28',
        // 2033
        // https://www.officesimplify.com/bank-holidays-uk-2033
            '2033-01-03', '2033-04-15', '2033-04-18', '2033-05-02', '2033-05-30', '2033-08-29', '2033-12-26', '2033-12-27',
        // 2034
        // https://www.officesimplify.com/bank-holidays-uk-2034
            '2034-01-02', '2034-04-07', '2034-04-10', '2034-05-01', '2034-05-29', '2034-08-28', '2034-12-25', '2034-12-26',
        // 2035
        // https://www.officesimplify.com/bank-holidays-uk-2035
            '2035-01-01', '2035-03-23', '2035-03-26', '2035-05-07', '2035-05-28', '2035-08-27', '2035-12-25', '2035-12-26',
    ];

    $fmt_currency = new NumberFormatter( 'en_GB', NumberFormatter::CURRENCY );      // Example usage: echo $fmt_currency->formatCurrency($row->amount, "GBP");
    
    $fmt_date = new IntlDateFormatter( "en-GB" ,IntlDateFormatter::FULL, IntlDateFormatter::FULL,'Europe/London',IntlDateFormatter::GREGORIAN , "EEE dd/MM/yyyy");

    function BreadCrumb($page_name, $parent = null) {
        ?>
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item">
                    <a class="text-decoration-none" href="bu_dashboard.php">Dashboard</a>
                </li>
                <?php
                    if ($parent) {
                    ?>
                        <li class="breadcrumb-item">
                            <a class="text-decoration-none" href="<?php echo $parent['url'];?>"><?php echo $parent['title'];?></a>
                        </li>
                    <?php    
                    }
                ?>
                <li class="breadcrumb-item active">
                    <?php echo $page_name; ?>
                </li>
            </ol>
        <?php
    }

    function TaxYears ($pdo) {

    // Get date of earliest transaction
        $stmt = $pdo->prepare("
            SELECT
                bu_transactions.`transaction_date`
            FROM
                bu_transactions
            ORDER BY 
                bu_transactions.`transaction_date` ASC
            LIMIT 1;
        ");
        $stmt->execute();

        $transactions = ["earliest" => new DateTime($stmt->fetchColumn())];
        $stmt = null;

    // Get date of latest transaction
        $stmt = $pdo->prepare("
            SELECT
                bu_transactions.`transaction_date`
            FROM
                bu_transactions
            ORDER BY 
                bu_transactions.`transaction_date` DESC
            LIMIT 1;
        ");
        $stmt->execute();

        $transactions ["latest"] = new DateTime($stmt->fetchColumn());
        $stmt = null;

        //echo $transactions["earliest"]->format('Y');
        //echo $transactions["latest"]->format('Y');

    // Calculate the tax-year of the earliest transaction
        $taxYearEnd = ["earliest" => new DateTime($transactions["earliest"]->format('Y') . '-04-05')];  // The tax-year end date of the transaction's calendar year. This will be 2017-04-05 for transactions in the 2017 calendar year.

        //echo $taxYearEnd["earliest"]->format('Y-m-d');

        //var_dump($taxYearEnd);

        if ($transactions["earliest"] <= $taxYearEnd["earliest"]) {
        //
            $taxYear = ["earliest" => $taxYearEnd["earliest"]->format('Y') - 1];
        } else {
            $taxYear = ["earliest" => $taxYearEnd["earliest"]->format('Y')];

        }

        //echo $taxYear["earliest"] . '<br>';

    // Calculate the tax-year of the latest transaction
    $taxYearEnd = ["latest" => new DateTime($transactions["latest"]->format('Y') . '-04-05')];  // The tax-year end date of the transaction's calendar year. This will be 2017-04-05 for transactions in the 2017 calendar year.

    //echo $taxYearEnd["latest"]->format('Y-m-d');

    //var_dump($taxYearEnd);

    if ($transactions["latest"] <= $taxYearEnd["latest"]) {
    //
        $taxYear["latest"] = $taxYearEnd["latest"]->format('Y');
    } else {
        $taxYear["latest"] = $taxYearEnd["latest"]->format('Y') + 1;

    }
    
    //echo $taxYear["latest"] . "<br>";

    return $taxYears = [
        "earliest" => $taxYear["earliest"],
        "number" => $taxYear["latest"] - $taxYear["earliest"] + 1
    ];

    //return $taxYears

    }

    Function InputElementAccountID ($colSpan=2) {

        ?>

        <label for="account-id-alpha-read-only" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Account ID</label>
        <div class="col-sm-1">
            <input type="text" name="account-id-alpha-read-only" id="account-id-alpha-read-only" class="form-control" readonly>
        </div>

        <?php

    }

    Function InputElementAccountData ($pdo, $colSpan=2, $select='%') {

        ?>

        <label for="account-id-alpha" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Account Name</label>
        <div class="col-sm-8">
            <?php
                // This stored procedure uses a WHERE clause to select rows whose `status` column is equal to a specific value. This value is passed as a parameter to the procedure: 'open', 'closed' or '%' = ALL
                $stmt = $pdo->prepare("
                    CALL 
                        bu_accounts_dropdown(?);
                ");
                $stmt->execute(
                    [
                        $select
                    ]
                );
                
                echo '<select name="account-id-alpha" id="account-id-alpha" class="form-control" required>';
                echo '<option value="" selected disabled hidden>Account name...</option>';
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                }
                echo '</select>';

                $stmt = null;
            ?>
        </div>

        <?php

    }

    Function InputElementAccountStatus ($colSpan=2) {

        ?>

        <label for="status" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Status</label>
        <div class="col-sm-2">
            <?php
                $status = array (
                    "Open",
                    "Closed"
                );

                echo '<select name="status" id="status" class="form-control" required>';
                echo '<option value="" selected disabled hidden>Select status...</option>';
                foreach ($status as $option) {
                    //echo '<option value="' . $option . '">' . $option . '</option>';
                    echo '<option value="'.$option.'" '.($option === 'Open' ? 'selected="selected"' : '').'>'.$option .'</option>';
                }
                echo '</select>';

            ?>
        </div>

        <?php

    }


    Function InputElementBankName ($pdo, $colSpan=2) {

        ?>

        <label for="bank-id" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Bank Name</label>
        <div class="col-sm-5">
            <?php
                $stmt = $pdo->prepare("
                    CALL 
                        bu_banks_dropdown();
                ");
                $stmt->execute();
                
                echo '<select name="bank-id" id="bank-id" class="form-control" required>';
                echo '<option value="" selected disabled hidden>Bank name...</option>';
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo '<option value="' . $row->bank_id . '">' . $row->trading_name. '</option>';
                }
                echo '</select>';

                $stmt = null;
            ?>
        </div>

        <?php

    }


    Function InputElementEntity ($pdo, $colSpan=2) {

        ?>

        <label for="entity-id" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Entity</label>
        <div class="col-sm-8">
            <?php
                $stmt = $pdo->prepare("
                    CALL 
                        bu_entities_dropdown();
                ");
                $stmt->execute();

                echo '<select name="entity-id" id="entity-id" class="form-control" required>';
                echo "<option value='' selected disabled hidden>Entity...</option>";
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo '<option value="'.$row->entity_id.'">' . $row->entity_description .'</option>';
                }
                echo '</select>';
                
                $stmt = null;

            ?> 
        </div>  

        <?php

    }

    Function InputElementTransactionMethod ($pdo, $colSpan=2) {

        ?>

        <label for="method-id" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Method</label>
        <div class="col-sm-4">
            <?php
                $stmt = $pdo->prepare("
                    CALL 
                        bu_transaction_methods_dropdown();
                ");
                $stmt->execute();

                echo '<select name="method-id" id="method-id" class="form-control" required>';
                echo '<option value="" selected disabled hidden>Transaction method...</option>';
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo '<option value="' . $row->method_id . '">' . $row->method_description . '</option>';
                }
                echo '</select>';

                $stmt = null;
                
            ?>
        </div>

        <?php

    }

    Function InputElementTransactionType ($pdo, $colSpan=2) {

        ?>

        <label for="type-id" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Type</label>
        <div class="col-sm-4">
            <?php
                $stmt = $pdo->prepare("
                    CALL 
                        bu_transaction_types_dropdown();
                ");
                $stmt->execute();

                echo '<select name="type-id" id="type-id" class="form-control" required>';
                echo '<option value="" selected disabled hidden>Transaction type...</option>';
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo '<option value="' . $row->type_id . '">' . $row->type_description . '</option>';
                }
                echo '</select>';

                $stmt = null;
                
            ?>
        </div>

        <?php

    }

    Function InputElementTransactionSubType ($pdo, $colSpan=2) {

        ?>

        <label for="sub-type-id" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Sub-Type</label>
        <div class="col-sm-5">
            <?php
                $stmt = $pdo->prepare("
                    CALL 
                        bu_transaction_sub_types_dropdown();
                ");
                $stmt->execute();

                echo '<select name="sub-type-id" id="sub-type-id" class="form-control">';
                echo '<option value="" selected disabled hidden>Transaction sub-type...</option>';
                echo '<option value=" ">&nbsp;</option>';
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    echo '<option value="' . $row->sub_type_id . '">' . $row->sub_type_description . '</option>';
                }
                echo '</select>';
                
                $stmt = null;
            ?>
        </div>

        <?php

    }

    Function InputElementNotes ($colSpan=2) {

        ?>

        <label for="notes" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Notes</label>
        <div class="col-sm-9">
            <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Notes..." style="resize: none;"></textarea>
        </div>

        <?php

    }

    Function InputElementTransactionAmount ($colSpan=2) {

        ?>

        <label for="amount" class="col-sm-<?php echo $colSpan; ?> col-form-label" style="color: red;">Amount</label>
        <div class="col-sm-2">
            <input type="number" name="amount" id="amount" class="form-control" step="0.01" size="20" required placeholder="Amount...">
        </div>

        <?php

    }