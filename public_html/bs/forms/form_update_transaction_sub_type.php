<form id="update-transaction-sub-type" class="update-form" data-datatable-id="transaction-sub-types" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Sub-Type -->
        <div class="form-group row">
            <label for="sub-type-id" class="col-sm-2 col-form-label">Sub-Type</label>
            <div class="col-sm-2">
            <input type="text" name="sub-type-id" id="sub-type-id" class="form-control" readonly>
            </div>
        </div>
    <!-- Description -->
        <div class="form-group row">
            <label for="sub-type-description" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <input type="text" name="sub-type-description" id="sub-type-description" class="form-control" required >
            </div>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
