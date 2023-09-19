<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<div class="container">
    <h1>ProfileController/edit/:user_id</h1>

    <div class="box">
        <h2>Edit a profile</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
      
        <?php if ($this->user) { ?>
            
            <form method="post" action="<?php echo Config::get('URL'); ?>profile/editsave">
                <!-- we use htmlentities() here to prevent user input with " etc. break the HTML -->
                <input type="hidden" name="user_id" value="<?php echo htmlentities($this->user->user_id); ?>" />
                <input type="text" name="user_name" value="<?php echo htmlentities($this->user->user_name); ?>" />
               <?php echo htmlentities($this->user->user_email); ?>
                

                <?php /*echo '<pre>'; print_r($this->branches); 

        foreach($this->branches as $key => $value) {
        echo $key."===".$value;
        }
                die */?> 
                <select name="branch_id">
                    <option value="" >--Select Branch--</option>
                    <?php foreach ($this->branches as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($this->user->branch_id == $key) echo 'selected'; ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="submit" value="Change" />
            </form>
        <?php } else { ?>
            <p>This user profile does not exist.</p>
        <?php } ?>
    </div>
</div>
