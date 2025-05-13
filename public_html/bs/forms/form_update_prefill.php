<form id="update-prefill" class="update-form" data-datatable-id="prefills" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Name -->
        <!--
        <div class="form-group row">
            <label for="prefill-name" class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-8">
                <input type="text" name="prefill-name" id="prefill-name" class="form-control" required>
            </div>
        </div>
        -->
    <!-- Entity -->
        <div class="form-group row">
            <?php InputElementEntity ($pdo); ?>
        </div>
    <!-- Account Name -->
        <div class="form-group row">
            <?php InputElementAccountData ($pdo, 2, 'open'); ?>
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
    <!-- Notes -->
        <div class="form-group row">
            <?php InputElementNotes (); ?>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
