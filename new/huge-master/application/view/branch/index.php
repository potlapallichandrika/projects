<div class="container">
    <h1>BranchController/index</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here?</h3>
        <p>
            This is just a simple CRUD implementation. Creating, reading, updating, and deleting things.
        </p>
        <p>
            <form method="post" action="<?php echo Config::get('URL');?>branch/create">
                <label>Text of new branch:</label>
                <input type="text" name="branch_name" />
                <input type="text" name="branch_code"/>
                <input type="text" name="sections"/>
                <input type="submit" value="Create this branch" autocomplete="off"/>
            </form>
        </p>
        <table class="overview-table">
            <thead>
                <tr>
                    <td>Id</td>
                    <td>branch_name</td>
                    <td>branch_code</td>
                    <td>Sections</td>
                    <td>Edit</td>
                    <td>Delete</td>
                </tr>
            </thead>
            <tbody>
            <?php foreach($this->branches as $value) { ?>
                <tr>
                    <td><?= $value->branch_id; ?></td>
                    <td><?= htmlentities($value->branch_name); ?></td>
                    <td><?= isset($value->branch_code) ? htmlentities($value->branch_code) : ''; ?></td>
                    <td><?= isset($value->sections) ? htmlentities($value->sections) : ''; ?></td>
                    <td><a href="<?= Config::get('URL') . 'branch/edit/' . $value->branch_id; ?>">Edit</a></td>
                    <td><a href="<?= Config::get('URL') . 'branch/delete/' . $value->branch_id; ?>" onclick="return confirm('Are you sure you want to delete this branch?')">Delete</a></td>
                    <td>
    
</td>

                </tr>
            <?php } ?>
            </tbody>
        </table>
        
        <?php if (empty($this->branches)) { ?>
            <div>No branches yet. Create some!</div>
        <?php } ?>
            
    </div>
</div>
