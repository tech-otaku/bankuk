<form id="duplicate-transaction" class="duplicate-form" data-datatable-id="transactions" method="post" enctype="multipart/form-data" role="form">
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
            <?php InputElementAccountData ($pdo); ?>
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
    <!-- Date -->
        <div class="form-group row">
            <label for="transaction-date" class="col-sm-2 col-form-label">Date</label>
            <div id="datepicker-container" class="col-sm-2">
                <input type="text" name="transaction-date" id="transaction-date" class="form-control"  required readonly placeholder="Transaction date..." style="cursor:text; background:white;">
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