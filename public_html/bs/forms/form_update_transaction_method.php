<form id="update-transaction-method" class="update-form" data-datatable-id="transaction-methods" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Type -->
        <div class="form-group row">
            <label for="method-id" class="col-sm-2 col-form-label">Type</label>
            <div class="col-sm-2">
            <input type="text" name="method-id" id="method-id" class="form-control" readonly>
            </div>
        </div>
    <!-- Description -->
        <div class="form-group row">
            <label for="method-description" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <input type="text" name="method-description" id="method-description" class="form-control" required >
            </div>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
