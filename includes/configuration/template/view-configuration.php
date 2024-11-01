<div class="modal fade" id="wpstack-connect-modal-configuration" tabindex="-1" role="dialog" aria-labelledby="wpstack-connect-modal-configuration">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Connection Management</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form>
		  <div class="form-group">
		    <label for="secret_key">Secret Key</label>
		    <input type="password" class="form-control" id="secret_key" name="secret_key" placeholder="********" value="{{secret_key}}" readonly>
		  </div>
		  <label for="public_key">Public Key</label>
		  	<div class="input-group">
		    	<input type="text" class="form-control public-key" id="public_key" name="public_key" placeholder="xxxxxxxxxxxxxxx" value="{{public_key}}" readonly>
        <div class="input-group-append">
          <button class="btn btn-primary btn-cp-pk" type="button">
            Copy !
          </button>
        </div>
			</div>
		</form>
      </div>
      <div class="modal-footer">
        {{button_connect}}
      </div>
    </div>
  </div>
</div>