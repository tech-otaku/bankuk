<form id="update-entity" class="update-form" data-datatable-id="entities" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Entity ID --> 
        <div class="form-group row">
            <label for="entity-id" class="col-sm-2 col-form-label">Entity ID</label>
            <div class="col-sm-2">
                <input type="text" name="entity-id" id="entity-id" class="form-control" readonly>
            </div>
        </div>
    <!-- Entity Name --> 
        <div class="form-group row">
            <label for="entity-description" class="col-sm-2 col-form-label">Entity</label>
            <div class="col-sm-5">
                <input type="text" name="entity-description" id="entity-description" class="form-control" required>
            </div>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
