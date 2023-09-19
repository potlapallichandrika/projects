<div class="container">
  <h1>NoteController/index</h1>
  <div class="box">
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <h3>What happens here?</h3>
    <p>
      This is just a simple CRUD implementation. Creating, reading, updating, and deleting things.
    </p>
    <p>
      <form method="post" action="<?php echo Config::get('URL');?>note/create">
        <label>Text of new note: </label>
        <input type="text" name="note_text" />
        <label>Reminder Date: </label>
        <input type="datetime-local" name="reminder_date" />
        <input type="submit" value='Create this note' autocomplete="on" />
      </form> 
    </p>

    <?php if ($this->notes) { ?>
      <table class="display note-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Note</th>
            <th>Reminder Date</th>
            <th>EDIT</th>
            <th>DELETE</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($this->notes as $key => $value) { ?>
            <tr>
              <td><?= $value->note_id; ?></td>
              <td><?= htmlentities($value->note_text); ?></td>
              <td><?= htmlentities($value->reminder_date); ?></td>
              <td><a href="<?= Config::get('URL') . 'note/edit/' . $value->note_id; ?>">Edit</a></td>
              <td><a href="<?= Config::get('URL') . 'note/delete/' . $value->note_id; ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <div>No notes yet. Create some!</div>
    <?php } ?>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.css">
<script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<script>
  $(document).ready(function() {
    $('.display').DataTable({
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'csvHtml5',
          text: 'Import CSV',
          className: 'btn-import-csv'
        }
      ]
    });
  });
</script>
