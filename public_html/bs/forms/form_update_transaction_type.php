<form id="update-transaction-type" class="update-form" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
        <div class="row">
        <!-- Record ID [Hidden] -->
            <input type="text" name="record-id" id="record-id" hidden>
        <!-- DataTables Row Index [Hidden] -->
            <input type="text" name="dt-row-index" id="dt-row-index" hidden>
        <!-- DOM Row Index [Hidden] -->
            <input type="text" name="dom-row-index" id="dom-row-index" hidden>
        <!-- Type -->
            <div class="form-group row">
                <label for="type" class="col-sm-2 col-form-label">Type</label>
                <div class="col-sm-1">
                <input type="text" name="type" id="type" class="form-control" readonly>
                </div>
            </div>
        <!-- Description -->
            <div class="form-group row">
                <label for="description" class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                    <input type="text" name="description" id="description" class="form-control" required >
                </div>
            </div>
        </div>
        <div class="row">
        </div>
        <div class="row">
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
