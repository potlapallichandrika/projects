<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<div class="container">
    <h1>ProfileController/index</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here?</h3>
        <div>
            This controller/action/view shows a list of all users in the system. You could use the underlying code to
            build things that use profile information of one or multiple/all users.
        </div>
        <div>
            
            
            <table class="overview-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Avatar</td>
                    <td>Username</td>
                    <td>User's email</td>
                    <td>Activated ?</td>
                    <td>Link to user's profile</td>
                    <td>Branchname</td>
                    <td>EDIT</td>
                    <td>DELETE</td>
                </tr>
                </thead>
                <?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
                        <td><?= $user->user_id; ?></td>
                        <td class="avatar profileimage avatar-<?= $user->user_id; ?>" data-userid="<?= $user->user_id; ?>">
                            <?php if (isset($user->user_avatar_link)) { ?>
                                <img src="<?= $user->user_avatar_link; ?>" />

                            <?php } ?>
                        </td>
                        <td><?= $user->user_name; ?></td>
                        <td><?= $user->user_email; ?></td>
                        <td><?= ($user->user_active == 0 ? 'No' : 'Yes'); ?></td>
                      
                            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
                        </td>
                        <td></td>
                        <?php //echo '<pre>', print_r($this->branches), die;?>
                        <td><?= $user->branch_name; ?></td>
                         <td><a href="<?= Config::get('URL') . 'profile/edit/' . $user->user_id; ?>">Edit</a></td>
                            <td><a href="<?= Config::get('URL') . 'profile/delete/' . $user->user_id; ?>"onclick="return confirm('Are you sure you want to delete this item?');">Delete</a></td>
                        
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>




<!-- Edit profile image Model starts -->

<div id="profileimageModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload Profile Image</h4>
        </div>
        <div class="modal-body">
          
         
       <form id="uploadForm" method="post" action="<?php echo Config::get('URL');?>profile/upload" enctype="multipart/form-data">
                <input type="file" name="avatar" id="avatarInput">
                 <input type="hidden" id="user_id" value="" name="user_id">
                <button type="button" onclick="uploadImage()">Upload</button>
                
            </form>
            <div id="success_message" style="display:none">
                <p>image uploaded successfully</p>
            </div>
      </div>

    </div>
  </div>
</div>

  
  <!-- Edit profile image Model ends -->


  <script>
    //image upload modal script

     jQuery(document).delegate(".profileimage","click",function(){
            //event.preventDefault();
        console.log("ddd");
        var userid=jQuery(this).attr('data-userid');
        jQuery('#user_id').val(userid);
        jQuery('#profileimageModal').modal('show');

        });

  function uploadImage() {     
    var form = jQuery('#uploadForm');
    var actionUrl = form.attr('action'); 
    var obj = jQuery('#uploadForm');
    /* ADD FILE TO PARAM AJAX */
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
        success: function(dataResp)
        {
               
                //var respObj = JSON.parse(dataResp);
                if(dataResp.status == true) {
                    //console.log("True==");
                    var userId = jQuery('#user_id').val();
                    jQuery(".avatar-"+userId+' img').attr("src", "/"+dataResp.filename);
                    
                     // Display success message
           var successMessage = document.getElementById("success_message");
           successMessage.style.display = "block";
                    //alert("Image Uploaded Successfully");
          // jQuery("#success_message").html(successMessage).fadeIn();

          // // Hide success message after 2 seconds
          // setTimeout(function() {
          //   jQuery("#success_message").fadeOut();
          // }, 2000); 
            
        } else {
                    console.log("False==");
                    setTimeout(function(){
                  alert("Timeout");
                    },3000);
                }
        }
    });
     jQuery.modal.close(); // Close the modal popup
    }

</script>     


<!-- think the user is a student, add the branch to student. if you want to use the joins you can , but the foreign key should be same in both the tables -->