<form id="update-bank" class="update-form" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
        <div class="row">
        <!-- DataTables Row Index [Hidden] -->
            <input type="text" name="dt-row-index" id="dt-row-index" hidden>
        <!-- DOM Row Index [Hidden] -->
            <input type="text" name="dom-row-index" id="dom-row-index" hidden>
        <!-- Record ID [Hidden] -->
            <input type="text" name="record-id" id="record-id" hidden>
        <!-- Bank ID -->
            <div class="form-group row">
                <label for="bank-id" class="col-sm-3 col-form-label">Bank ID</label>
                <div class="col-sm-2">
                    <input type="text" name="bank-id" id="bank-id" class="form-control" readonly>
                </div>
            </div>
        <!-- Legal Name -->
            <div class="form-group row">
                <label for="legal-name" class="col-sm-3 col-form-label">Legal Name</label>
                <div class="col-sm-5">
                    <input type="text" name="legal-name" id="legal-name" class="form-control" required placeholder="Bank's legal name...">
            </div>
            </div>
        <!-- Trading Name -->
            <div class="form-group row">
                <label for="trading-name" class="col-sm-3 col-form-label">Trading Name</label>
                <div class="col-sm-5">
                    <input type="text" name="trading-name" id="trading-name" class="form-control" required placeholder="Bank's trading name...">
                </div>
            </div>
        </div>  <!-- /.row -->
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
