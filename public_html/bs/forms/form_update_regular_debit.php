<form id="update-regular-debit" class="update-form" data-datatable-id="regular-debits" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Account ID Alpha [Read-only] -->
        <div class="form-group row">
            <?php InputElementAccountID (); ?>
        </div>
    <!-- Account Data -->
        <div class="form-group row">
            <?php InputElementAccountData ($pdo, 2, 'open'); ?>
        </div>
    <!-- Amount -->
        <div class="form-group row">
            <?php InputElementTransactionAmount (); ?>
        </div>
    <!-- Entity -->
        <div class="form-group row">
            <?php InputElementEntity ($pdo); ?>
        </div>
    <!-- Type -->
        <div class="form-group row">
            <?php InputElementTransactionType ($pdo); ?>
        </div>
    <!-- Sub Type -->
        <div class="form-group row">
            <?php InputElementTransactionSubType ($pdo); ?>
        </div>
    <!-- Method -->
        <div class="form-group row">
            <?php InputElementTransactionMethod ($pdo); ?>
        </div>
    <!-- Day -->
        <div class="form-group row">
            <label for="day" class="col-sm-2 col-form-label">Day</label>
            <div class="col-sm-2">
                <input type="number" min="1" max="31" step="1" name="day" required class="form-control" id="day" onKeyDown="return false">
                <!-- <input type="text" name="day" id="day" class="form-control" required value=<?php //echo $bu_regular_debit['day']; ?> > -->
            </div>
        </div>
    <!-- Period [Read-only]-->
        <div class="form-group row">
            <label for="period" class="col-sm-2 col-form-label">Period</label>
            <div class="col-sm-2">
                <input type="text" name="period" id="period" class="form-control" required readonly>
                <!-- <input type="text" name="day" id="day" class="form-control" required value=<?php //echo $bu_regular_debit['day']; ?> > -->
            </div>
        </div>
    <!-- Last --> 
        <div class="form-group row">
            <label for="last" class="col-sm-2 col-form-label">Last</label>
            <div class="col-sm-2">
                <input type="text" name="last" id="last" class="form-control" required readonly style="cursor:text; background:white;">
            </div>
        </div>
    <!-- Next --> 
        <div class="form-group row">
            <label for="next" class="col-sm-2 col-form-label">Next</label>
            <div class="col-sm-2">
                <input type="text" name="next" id="next" class="form-control" required readonly style="cursor:text; background:white;">
            </div>
        </div>
    <!-- Notes -->
        <div class="form-group row">
            <?php InputElementNotes (); ?>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
         <!-- The form's submit button has been moved to the modal's footer -->
    </div>
</form>
