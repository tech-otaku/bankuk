<form id="update-tax-year" class="update-form" data-datatable-id="tax-years" method="post" enctype="multipart/form-data" role="form">
    <div class="card-body">
    <!-- DataTables Row Index [Hidden] -->
        <input type="text" name="dt-row-index" id="dt-row-index" hidden>
    <!-- DOM Row Index [Hidden] -->
        <input type="text" name="dom-row-index" id="dom-row-index" hidden>
    <!-- Record ID [Hidden] -->
        <input type="text" name="record-id" id="record-id" hidden>
    <!-- Start --> 
        <div class="form-group row">
            <label for="tax-year-start" class="col-sm-2 col-form-label">Start Date</label>
            <div id="start-container" class="col-sm-2">
                <input type="text" name="tax-year-start" id="tax-year-start" class="form-control" required readonly style="cursor:text; background:white;">
            </div>
        </div>
    <!-- End --> 
        <div class="form-group row">
            <label for="tax-year-end" class="col-sm-2 col-form-label">End Date</label>
            <div id="end-container" class="col-sm-2">
                <input type="text" name="tax-year-end" id="tax-year-end" class="form-control" required readonly style="cursor:text; background:white;">
            </div>
        </div>
    <!-- Tax Year--> 
        <div class="form-group row">
            <label for="tax-year" class="col-sm-2 col-form-label">Tax Year</label>
            <div class="col-sm-2">
                <input type="text" name="tax-year" id="tax-year" class="form-control" readonly>
            </div>
        </div>
    </div>  <!-- /.card-body -->
    <div class="card-footer">
        <span id="used-by"></span>
        <!-- NOTE: The form's submit button has been moved to the modal's footer -->
    </div>
</form>
