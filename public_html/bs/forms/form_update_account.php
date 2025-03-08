<form id="update-account" class="update-form" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Account ID [Alpha] -->
        <div class="form-group row">
            <label for="account-id-alpha" class="col-sm-3 col-form-label">Account ID [Alpha]</label>
            <div class="col-sm-1">
                <input type="text" name="account-id-alpha" id="account-id-alpha" class="form-control" readonly>
            </div>
        </div>
    <!-- Account ID --> 
        <div class="form-group row">
            <label for="account-id" class="col-sm-3 col-form-label">Account ID</label>
            <div class="col-sm-2">
                <input type="text" name="account-id" id="account-id" class="form-control" readonly>
            </div>
        </div>
    <!-- Bank Name -->
        <div class="form-group row">
            <label for="bank-id" class="col-sm-3 col-form-label">Bank Name</label>
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
        </div>
    <!-- Account Name -->
        <div class="form-group row">
            <label for="account-name" class="col-sm-3 col-form-label">Account Name</label>
            <div class="col-sm-8">
                <input type="text" name="account-name" id="account-name" class="form-control" required placeholder="Account name...">
            </div>
        </div>
    <!-- Sort Code -->
        <div class="form-group row">
            <label for="sort-code" class="col-sm-3 col-form-label">Sort Code</label>
            <div class="col-sm-2">
                <input type="text" name="sort-code" id="sort-code" class="form-control" required pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Sort code...">
            </div>
        </div>
    <!-- Account Number -->
        <div class="form-group row">
            <label for="account-number" class="col-sm-3 col-form-label">Account Number</label>
            <div class="col-sm-2">
                <input type="text" name="account-number" id="account-number" class="form-control" required pattern="[A-Z]{4}[0-9]{4}" placeholder="Account number...">
            </div>
        </div>
    <!-- Status -->
        <div class="form-group row">
            <label for="status" class="col-sm-3 col-form-label">Status</label>
            <div class="col-sm-2">
                <?php
                    $status = array (
                        "Open",
                        "Closed"
                    );

                    echo '<select name="status" id="status" class="form-control" required>';
                    echo '<option value="" selected disabled hidden>Select status...</option>';
                    foreach ($status as $option) {
                        echo '<option value="' . $option . '">' . $option . '</option>';
                    }
                    echo '</select>';

                ?>
            </div>
        </div>
    <!-- Notes -->
        <div class="form-group row">
            <label for="notes" class="col-sm-3 col-form-label">Notes</label>
            <div class="col-sm-9">
                <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Notes..." style="resize: none;"></textarea>
            </div>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
