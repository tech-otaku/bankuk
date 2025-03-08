<form id="update-prefill" class="update-form" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Entity -->
        <div class="form-group row">
            <label for="entity-id" class="col-sm-2 col-form-label">Entity</label>
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
                    echo '<option value="" selected disabled hidden>Account name...</option>';
                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                        echo '<option value="' . $row->account_id_alpha . '">' . $row->_name .' - ' . $row->account_number . ' ['. $row->account_id_alpha . ']' . ($row->status === 'Closed' ? ' CLOSED' : '') . '</option>';
                    }
                    echo '</select>';                        

                    $stmt = null;
                ?>
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
            <label for="sub-type" class="col-sm-2 col-form-label">Sub-Type</label>
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
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
