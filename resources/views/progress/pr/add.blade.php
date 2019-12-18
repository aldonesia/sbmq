<div class="block block-themed block-transparent mb-0">
  <div class="block-header bg-primary">
      <h3 class="block-title modal-title" id="modelHeading">Add New material</h3>
      <div class="block-options">
          <button type="button" class="btn-block-option close" data-dismiss="modal" aria-label="Close">
              <i class="fa fa-close"></i>
          </button>
      </div>
  </div>
  <div class="block-content">
      <div class="row justify-content-center py-20">
          <div class="col-xl-10">
              <span id="form_result"></span>
              <form method="POST" id="material_form" name="material_form" class="js-validation-bootstrap form-horizontal">
                  @csrf
                  <div class="form-group row">
                      <label class="col-lg-4 col-form-label" for="mt_id">Material Type</label>
                      <div class="col-lg-8">
                          <input type="text" class="form-control" id="mt_id" name="mt_id" placeholder="Material type">
                          {{-- <select class="form-control" name="mt_lvl1">
                            <option>Select Item</option>
                            @foreach ( $mt_lvl1 as $item)
                              <option value="{{ $item->mt_id }}" {{ ( $item->mt_id == 1) ? 'selected' : '' }}> {{ $item->mt_name }} </option>
                            @endforeach
                          </select> --}}
                          <select class="form-control" name="mt_lvl2">
                            <option>Select Item</option>
                            @foreach ( $mt_lvl2 as $item)
                              <option value="{{ $item->mt_id }}" {{ ( $item->mt_id == 1) ? 'selected' : '' }}> {{ $item->mt_name }} </option>
                            @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-4 col-form-label" for="mat_spec">Specification</label>
                      <div class="col-lg-8">
                          <input type="text" class="form-control" id="mat_spec" name="mat_spec" placeholder="Material Specification">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-4 col-form-label" for="mat_thick">Thickness</label>
                      <div class="col-lg-8">
                          <input type="text" class="form-control" id="mat_thick" name="mat_thick" placeholder="Material Thickness">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-4 col-form-label" for="mat_weight">Weight</label>
                      <div class="col-lg-8">
                          <input type="text" class="form-control" id="mat_weight" name="mat_weight" placeholder="Material Weight">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-4 col-form-label" for="purchased_at">Purchased At</label>
                      <div class="col-lg-8">
                          <input type="text" class="form-control" id="purchased_at" name="purchased_at" placeholder="date">
                      </div>
                  </div>
                  <div class="form-group row">
                          <label class="col-lg-4 col-form-label" for="arrived_at">Arrived At</label>
                          <div class="col-lg-8">
                              <input type="text" class="form-control" id="arrived_at" name="arrived_at" placeholder="date">
                          </div>
                      </div>
                  <div class="form-group row">
                          <label class="col-lg-4 col-form-label" for="remark">Remark</label>
                          <div class="col-lg-8">
                              <input type="text" class="form-control" id="remark" name="remark" placeholder="remark">
                          </div>
                      </div>
                  <div class="form-group row">
                      <div class="col-lg-8 ml-auto">
                              <input type="hidden" name="action" id="action" />
                              <input type="hidden" name="hidden_id" id="hidden_id" />
                              <input type="submit" name="action_button" id="action_button" class="btn btn-success" value="Add" />
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                      </div>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>
