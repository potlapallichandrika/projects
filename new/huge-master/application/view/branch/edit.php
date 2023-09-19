<div class="container">
    <h1>NoteController/edit/:note_id</h1>

    <div class="box">
        <h2>Edit a branch</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <?php if ($this->branches) { ?>
            <form method="post" action="<?php echo Config::get('URL'); ?>branch/editSave">
                <label>Change text of branch:</label>
                <!-- we use htmlentities() here to prevent user input with " etc. break the HTML -->
                <input type="hidden" name="branch_id" value="<?php echo htmlentities($this->branches->branch_id); ?>" />
                <input type="text" name="branch_name" value="<?php echo htmlentities($this->branches->branch_name); ?>" />
                <input type="text" name="branch_code" value="<?php echo htmlentities($this->branches->branch_code); ?>" />
                <input type="text" name="sections" value="<?php echo htmlentities($this->branches->sections); ?>" />
                <input type="submit" value="Change" />
            </form>
        <?php } else { ?>
            <p>This branch does not exist.</p>
        <?php } ?>
    </div>
</div>
