<form id="update-transaction" class="update-form" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
        <div class="row">
        <!-- DataTables Row Index [Hidden] -->
            <input type="text" name="row-index" id="row-index" hidden>
        <!-- Record ID [Hidden] -->
            <input type="text" name="record-id" id="record-id" hidden>
        <!-- Account ID [Read-only] -->
            <div class="form-group row">
                <label for="account-id-ignore" class="col-sm-2 col-form-label">Account ID</label>
                <div class="col-sm-2">
                    <input type="text" name="account-id-ignore" id="account-id-ignore" class="form-control" readonly>
                </div>
            </div>
        <!-- Account Name -->
            <div class="form-group row">
                <label for="account-id-alpha" class="col-sm-2 col-form-label">Account Name</label>
                <div class="col-sm-8">
                    <?php
                        // This stored procedure uses a WHERE clause to select rows whose `status` column is equal to a specific value. This value is passed as a parameter to the procedure: 'open', 'closed' or '%' = ALL
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_accounts_dropdown(?);
                        ");
                        $stmt->execute(
                            [
                                '%'
                            ]
                        );
                        
                        echo '<select name="account-id-alpha" id="account-id-alpha" class="form-control" required>';
                        echo '<option value="" selected disabled hidden>Select account...</option>';
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                        }
                        echo '</select>';

                        

                        $stmt = null;
                    ?>
                </div>
            </div>
        <!-- Amount -->
            <div class="form-group row">
                <label for="amount" class="col-sm-2 col-form-label">Amount</label>
                <div class="col-sm-4">
                    <input type="text" name="amount" id="amount" class="form-control" required placeholder="Enter transaction amount...">
                </div>
            </div>
        <!-- Type -->
            <div class="form-group row">
                <label for="type" class="col-sm-2 col-form-label">Type</label>
                <div class="col-sm-4">
                    <?php
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_transaction_types_dropdown();
                        ");
                        $stmt->execute();

                        echo '<select name="type" id="type" class="form-control" required>';
                        echo '<option value="" selected disabled hidden>Select transaction type...</option>';
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                        }
                        echo '</select>';

                        $stmt = null;
                        
                    ?>
                </div>                                          
            </div>
        <!-- Sub Type -->
            <div class="form-group row">
                <label for="sub-type" class="col-sm-2 col-form-label">Sub-Type</label>
                <div class="col-sm-4">
                    <?php
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_transaction_types_dropdown();
                        ");
                        $stmt->execute();

                        echo '<select name="sub-type" id="sub-type" class="form-control">';
                        echo '<option value="" selected disabled hidden>Select transaction sub-type...</option>';
                        echo '<option value="">&nbsp;</option>';
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                        }
                        echo '</select>';
                        
                        $stmt = null;

                    ?>
                </div>
            </div>
        <!-- Entity -->
            <div class="form-group row">
                <label for="entity-id" class="col-sm-2 col-form-label">Entity</label>
                <div class="col-sm-4">
                    <?php
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_entities_dropdown();
                        ");
                        $stmt->execute();

                        echo '<select name="entity-id" id="entity-id" class="form-control" required>';
                        echo "<option value='' selected disabled hidden>Select entity...</option>";
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo '<option value="'.$row->entity_id.'">' . $row->entity_description .'</option>';
                        }
                        echo '</select>';
                        
                        $stmt = null;

                    ?> 
                </div>  
            </div>
        <!-- Date -->
            <div class="form-group row">
                <label for="date" class="col-sm-2 col-form-label">Date</label>
                <div class="col-sm-4">
                    <input type="text" name="date" required class="form-control" id="datepicker" required readonly placeholder="Select transaction date..." style="cursor:text; background:white;">
                </div>
            </div>
        <!-- Notes -->
            <div class="form-group row">
                <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                <div class="col-sm-10">
                    <input type="text" name="notes" id="notes" class="form-control" placeholder="Enter note...">
                </div>
            </div>
        </div>  <!-- /.row -->
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <button type="submit" name="update-transaction-submit" id="update-transaction-submit" class="btn btn-success">Update</button>
    </div>
</form>