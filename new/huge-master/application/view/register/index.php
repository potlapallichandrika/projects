<div class="container">
<div style="display:flex" class="row-2-columns">
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <!-- login box on left side -->
    <div class="login-box" style="width: 50%; display: block;">
        <h2>Register a new account</h2>

        <!-- register form -->
        <form  method="post" action="<?php echo Config::get('URL'); ?>register/register_action">
            <!-- the user name input field uses a HTML5 pattern check -->
            <input type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Username (letters/numbers, 2-64 chars)" required />
            <input type="text" name="user_email" placeholder="email address (a real address)" required />
            <input type="text" name="user_email_repeat" placeholder="repeat email address (to prevent typos)" required />
            <input type="password" name="user_password_new" pattern=".{6,}" placeholder="Password (6+ characters)" required autocomplete="off" />
            <input type="password" name="user_password_repeat" pattern=".{6,}" required placeholder="Repeat your password" autocomplete="off" />

            <!-- show the captcha by calling the login/showCaptcha-method in the src attribute of the img tag -->
            <img id="captcha" src="<?php echo Config::get('URL'); ?>register/showCaptcha" />
            <input type="text" name="captcha" placeholder="Please enter above characters" required />

            <!-- quick & dirty captcha reloader -->
            <a href="#" style="display: block; font-size: 11px; margin: 5px 0 15px 0; text-align: center"
               onclick="document.getElementById('captcha').src = '<?php echo Config::get('URL'); ?>register/showCaptcha?' + Math.random(); return false">Reload Captcha</a>
            <input type="submit" class="btn btn-primary submitBtn" value="Register" />
        </form>

    </div>
      <div class="login-box" style="width: 50%; display: block;">
    <h2>Bulk Upload CSV</h2>
    <form enctype="multipart/form-data" id="bulkusers" action="<?php echo Config::get('URL'); ?>register/bulk_upload_action" method="post">
        <input type="file" name="csvfile" id="csvfile" value=""/>
        <button type="button" class="btn btn-primary submitBtn" id="previewButton" style="width: 200px; padding: 10px; margin-top: 20px;" onclick="preview()">Preview</button>
<div id="preview_resp"></div>
         <button type="submit" class="btn btn-primary submitBtn" name="bulkusers"  style="width: 200px; padding: 10px; margin-top: 20px;">Bulk Upload</button>
        
    </form>
    <div id="success_message" style="display:none">
                <!-- <p>csv file is uploaded successfully</p> -->
            </div>
            <!-- <div id="previewTableContainer"></div> -->
</div>
</div>


<div class="container">
    <p style="display: block; font-size: 11px; color: #999;">
        Please note: This captcha will be generated when the img tag requests the captcha-generation
        (= a real image) from YOURURL/register/showcaptcha. As this is a client-side triggered request, a
        $_SESSION["captcha"] dump will not show the captcha characters. The captcha generation
        happens AFTER the request that generates THIS page has been finished.
    </p>
</div>



<script>
  function preview(){
  var form =jQuery('#bulkusers');
  var actionUrl = '/register/preview';
   var obj = jQuery('#bulkusers');
   var formData = new FormData();
   $.each($(obj).find("input[type='file']"), function(i, tag) {
      $.each($(tag)[0].files, function(i, file) {
          formData.append(tag.name, file);
      });
  });
    var params = $(obj).serializeArray();
  $.each(params, function (i, val) {
      formData.append(val.name, val.value);
  });
  jQuery.ajax({
      type: "POST",
      url: actionUrl,
      data: formData, // serializes the form's elements.
      cache: false,
  contentType: false,
  processData: false,
      success: function(response, textStatus, xhr) {
          if (xhr.status === 200) {
            jQuery("#preview_resp").html(response);
              console.log("Upload successful");
               // Display the success message on the page
              //alert("csv file is Uploaded Successfully");
         //      var successMessage = document.getElementById("success_message");
         // successMessage.style.display = "block";
             
          } else {
              console.log("Upload failed. Server returned status code: " + xhr.status);
          }
      },
      error: function(xhr, status, error) {
          console.log("Upload error: " + error);
      }
  });
}

</script>

