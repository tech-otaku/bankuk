
<form id="update-regular-debit" class="update-form" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
        <div class="row">
        <!-- DataTables Row Index [Hidden] -->
            <input type="text" name="dt-row-index" id="dt-row-index" hidden>
        <!-- DOM Row Index [Hidden] -->
            <input type="text" name="dom-row-index" id="dom-row-index" hidden>
        <!-- Record ID [Hidden] -->
            <input type="text" name="record-id" id="record-id" hidden>
        <!-- Account ID [Read-only] -->
            <div class="form-group row">
                <label for="account-id-ignore" class="col-sm-3 col-form-label">Account ID</label>
                <div class="col-sm-1">
                    <input type="text" name="account-id-ignore" id="account-id-ignore" class="form-control" readonly>
                </div>
            </div>
        <!-- Account Name --> 
            <div class="form-group row">
                <label for="account-id-alpha" class="col-sm-3 col-form-label">Account Name</label>
                <div class="col-sm-8">
                    <?php
                        // This stored procedure uses a WHERE clause to select rows whose `status` column is equal to a specific value. This value is passed as a parameter to the procedure: 'open', 'closed' or '%' = ALL
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_accounts_dropdown(?);
                        ");
                        //var_dump($stmt);
                        $stmt->execute(
                            [
                                'open'
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
            </div>
        <!-- Amount -->
            <div class="form-group row">
                <label for="amount" class="col-sm-3 col-form-label">Amount</label>
                <div class="col-sm-2">
                    <input type="text" name="amount" id="amount" class="form-control" required placeholder="Transaction amount...">
                </div>
            </div>
        <!-- Type -->
        <div class="form-group row">
                <label for="type" class="col-sm-3 col-form-label">Type</label>
                <div class="col-sm-4">
                    <?php
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_transaction_types_dropdown();
                        ");
                        $stmt->execute();

                        echo '<select name="type" id="type" class="form-control" required>';
                        echo '<option value="" selected disabled hidden>Transaction type...</option>';
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
                <label for="sub-type" class="col-sm-3 col-form-label">Sub-Type</label>
                <div class="col-sm-4">
                    <?php
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_transaction_types_dropdown();
                        ");
                        $stmt->execute();

                        echo '<select name="sub-type" id="sub-type" class="form-control">';
                        echo '<option value="" selected disabled hidden>Transaction sub-type...</option>';
                        echo '<option value=" ">&nbsp;</option>';
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo '<option value="' . $row->type . '">' . $row->description . '</option>';
                        }
                        echo '</select>';
                        
                        $stmt = null;

                    ?>
                </div>
            </div>
        <!-- Regular Debit Type -->
            <div class="form-group row">
                <label for="regular-debit-type" class="col-sm-3 col-form-label">Regular Debit Type</label>
                <div class="col-sm-8">
                    <?php
                        $stmt = $pdo->prepare("
                            CALL 
                                bu_regular_debit_types_dropdown();
                        ");
                        $stmt->execute();

                        echo '<select name="regular-debit-type" id="regular-debit-type" class="form-control">';
                        //echo '<option value="">&nbsp;</option>';
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo '<option value="'.$row->type.'">'.$row->description .'</option>';
                        }
                        echo '</select>';

                        $stmt = null;
                    ?>   
                </div>
            </div>
        <!-- Entity -->
        <div class="form-group row">
                <label for="entity-id" class="col-sm-3 col-form-label">Entity</label>
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
            </div>
        <!-- Day -->
            <div class="form-group row">
                <label for="day" class="col-sm-3 col-form-label">Day</label>
                <div class="col-sm-2">
                    <input type="number" min="1" max="31" step="1" name="day" required class="form-control" id="day" onKeyDown="return false">
                    <!-- <input type="text" name="day" id="day" class="form-control" required value=<?php //echo $bu_regular_debit['day']; ?> > -->
                </div>
            </div>
        <!-- Period [Read-only]-->
            <div class="form-group row">
                <label for="period" class="col-sm-3 col-form-label">Period</label>
                <div class="col-sm-1">
                    <input type="text" name="period" id="period" class="form-control" required readonly>
                    <!-- <input type="text" name="day" id="day" class="form-control" required value=<?php //echo $bu_regular_debit['day']; ?> > -->
                </div>
            </div>

        <!-- Last --> 
            <div class="form-group row">
                <label for="last" class="col-sm-3 col-form-label">Last</label>
                <div class="col-sm-2">
                    <input type="text" name="last" id="last" class="form-control" required readonly style="cursor:text; background:white;">
                </div>
            </div>
        <!-- Next --> 
            <div class="form-group row">
                <label for="next" class="col-sm-3 col-form-label">Next</label>
                <div class="col-sm-2">
                    <input type="text" name="next" id="next" class="form-control" required readonly style="cursor:text; background:white;">
                </div>
            </div>
        <!-- Notes -->
            <div class="form-group row">
                <label for="type" class="col-sm-3 col-form-label">Notes</label>
                <div class="col-sm-8">
                    <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Notes..." style="resize: none;"></textarea>
                </div>
            </div>
        </div>  <!-- /.row -->
    </div>  <!-- /.card-body -->
    <!-- The form's submit button has been moved to the modal's footer
    <div class="card-footer">
    </div>
    -->
</form>
