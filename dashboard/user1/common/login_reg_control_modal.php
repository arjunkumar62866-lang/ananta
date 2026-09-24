<!-- PRIVATE LOGIN & REGISTRATION ACCESS CONTROL MODAL -->
<div id="loginRegControlModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 105500;">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content" style="background: #ffffff; color: #0f172a; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
      
      <!-- Modal Header -->
      <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 16px 24px; align-items: center;">
        <h5 class="modal-title" style="font-weight: 700; font-size: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px; margin: 0;">
          <i class="icon-lock" style="color: #0284c7;"></i> LOGIN & REGISTRATION CONTROL
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #64748b; opacity: 0.8; outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Auth Form View -->
      <div id="lrcAuthSection" class="modal-body" style="padding: 24px;">
        <div id="lrcAuthAlert" class="alert alert-danger d-none" style="font-size: 13px; border-radius: 8px; margin-bottom: 16px;"></div>
        <form id="lrcLoginForm" onsubmit="submitLrcLogin(event);">
          <div class="form-group" style="margin-bottom: 16px;">
            <label style="font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">ID</label>
            <input type="text" id="lrcAdminId" class="form-control" placeholder="Enter ID" required style="background: #ffffff !important; color: #0f172a !important; border-radius: 8px; border: 1.5px solid #cbd5e1 !important; height: 42px; font-size: 14px;">
          </div>
          <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Password</label>
            <div style="position: relative;">
              <input type="password" id="lrcPassword" class="form-control" placeholder="Enter Password" required style="background: #ffffff !important; color: #0f172a !important; border-radius: 8px; border: 1.5px solid #cbd5e1 !important; height: 42px; font-size: 14px; padding-right: 40px;">
              <button type="button" onclick="toggleLrcPassword();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; font-size: 16px; cursor: pointer; outline: none; padding: 4px;">
                <i id="lrcEyeIcon" class="fa fa-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" id="lrcLoginBtn" class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); border: none; border-radius: 8px; height: 42px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
            Authenticate
          </button>
        </form>
      </div>

      <!-- Control Settings View -->
      <div id="lrcControlSection" class="modal-body d-none" style="padding: 24px;">
        <div id="lrcSaveAlert" class="alert d-none" style="font-size: 13px; border-radius: 8px; margin-bottom: 16px;"></div>
        <form id="lrcSettingsForm" onsubmit="submitLrcSettings(event);">
          <input type="hidden" id="lrcCsrfToken" value="">
          
          <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block;">CURRENT STATUS</label>
            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
              <label class="btn btn-outline-success active" id="lblLrcStatusOn" style="border-radius: 8px 0 0 8px; font-weight: 700; flex: 1; padding: 10px;">
                <input type="radio" name="lrcStatus" id="lrcStatusOn" value="ON" checked onchange="toggleLrcFields();"> ON
              </label>
              <label class="btn btn-outline-danger" id="lblLrcStatusOff" style="border-radius: 0 8px 8px 0; font-weight: 700; flex: 1; padding: 10px;">
                <input type="radio" name="lrcStatus" id="lrcStatusOff" value="OFF" onchange="toggleLrcFields();"> OFF
              </label>
            </div>
          </div>

          <div id="lrcOffConfigGroup" style="display: none;">
            <div class="form-group" style="margin-bottom: 16px;">
              <label style="font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Message Type</label>
              <div style="display: flex; gap: 16px; margin-top: 6px;">
                <label style="font-size: 13px; font-weight: 600; color: #d97706; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                  <input type="radio" name="lrcMessageType" value="WARNING" checked> WARNING
                </label>
                <label style="font-size: 13px; font-weight: 600; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                  <input type="radio" name="lrcMessageType" value="ERROR"> ERROR
                </label>
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
              <label style="font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Message</label>
              <textarea id="lrcMessageText" class="form-control" rows="3" placeholder="Enter notification/error message..." style="background: #ffffff !important; color: #000000 !important; border-radius: 8px; border: 1.5px solid #cbd5e1 !important; font-size: 13.5px; font-weight: 600;"></textarea>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <button type="button" onclick="lrcLogout();" class="btn btn-link text-muted" style="font-size: 13px; text-decoration: none; padding: 0;">Logout</button>
            <button type="submit" id="lrcSaveBtn" class="btn btn-primary" style="background: #0284c7; border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600; font-size: 14px;">
              SAVE
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
function openLrcModal() {
  $('#lrcAuthAlert').addClass('d-none').text('');
  $('#lrcSaveAlert').addClass('d-none').text('');
  $('#loginRegControlModal').modal('show');
}

function submitLrcLogin(e) {
  e.preventDefault();
  var adminId = $('#lrcAdminId').val();
  var password = $('#lrcPassword').val();
  $('#lrcAuthAlert').addClass('d-none').text('');

  $.ajax({
    url: '/dashboard/user1/login_reg_control_action.php?action=login',
    type: 'POST',
    data: { admin_id: adminId, password: password },
    dataType: 'json',
    success: function(res) {
      if (res.status) {
        $('#lrcAuthSection').addClass('d-none');
        $('#lrcControlSection').removeClass('d-none');
        $('#lrcCsrfToken').val(res.csrf_token);
        populateLrcState(res.control);
      } else {
        $('#lrcAuthAlert').removeClass('d-none').text(res.message);
      }
    },
    error: function() {
      $('#lrcAuthAlert').removeClass('d-none').text('Server error while authenticating.');
    }
  });
}

function populateLrcState(control) {
  if (control.status === 'OFF') {
    $('#lrcStatusOff').prop('checked', true).trigger('change');
    $('#lblLrcStatusOff').addClass('active');
    $('#lblLrcStatusOn').removeClass('active');
  } else {
    $('#lrcStatusOn').prop('checked', true).trigger('change');
    $('#lblLrcStatusOn').addClass('active');
    $('#lblLrcStatusOff').removeClass('active');
  }
  
  if (control.message_type === 'ERROR') {
    $('input[name="lrcMessageType"][value="ERROR"]').prop('checked', true);
  } else {
    $('input[name="lrcMessageType"][value="WARNING"]').prop('checked', true);
  }
  
  $('#lrcMessageText').val(control.message_text || '');
  toggleLrcFields();
}

function toggleLrcFields() {
  var status = $('input[name="lrcStatus"]:checked').val();
  if (status === 'OFF') {
    $('#lrcOffConfigGroup').slideDown(150);
  } else {
    $('#lrcOffConfigGroup').slideUp(150);
  }
}

function submitLrcSettings(e) {
  e.preventDefault();
  var status = $('input[name="lrcStatus"]:checked').val();
  var msgType = $('input[name="lrcMessageType"]:checked').val();
  var msgText = $('#lrcMessageText').val();
  var csrf = $('#lrcCsrfToken').val();

  $('#lrcSaveAlert').addClass('d-none').removeClass('alert-success alert-danger').text('');

  $.ajax({
    url: '/dashboard/user1/login_reg_control_action.php?action=save_state',
    type: 'POST',
    data: {
      status: status,
      message_type: msgType,
      message_text: msgText,
      csrf_token: csrf
    },
    dataType: 'json',
    success: function(res) {
      if (res.status) {
        $('#lrcSaveAlert').addClass('alert-success').removeClass('d-none').text(res.message);
        setTimeout(function(){
          $('#loginRegControlModal').modal('hide');
        }, 1200);
      } else {
        $('#lrcSaveAlert').addClass('alert-danger').removeClass('d-none').text(res.message);
      }
    },
    error: function() {
      $('#lrcSaveAlert').addClass('alert-danger').removeClass('d-none').text('Failed to save settings.');
    }
  });
}

function lrcLogout() {
  $.ajax({
    url: '/dashboard/user1/login_reg_control_action.php?action=logout',
    type: 'GET',
    success: function() {
      $('#lrcControlSection').addClass('d-none');
      $('#lrcAuthSection').removeClass('d-none');
      $('#lrcLoginForm')[0].reset();
    }
  });
}

function toggleLrcPassword() {
  var pwdInput = document.getElementById('lrcPassword');
  var eyeIcon = document.getElementById('lrcEyeIcon');
  if (pwdInput.type === 'password') {
    pwdInput.type = 'text';
    eyeIcon.className = 'fa fa-eye-slash';
  } else {
    pwdInput.type = 'password';
    eyeIcon.className = 'fa fa-eye';
  }
}
</script>
