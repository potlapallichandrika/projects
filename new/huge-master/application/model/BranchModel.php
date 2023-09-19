<?php



/**
 * BranchModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class BranchModel
{
    /**
     * Get all notes (notes are just example data that the user has created)
     * @return array an array with several objects (the results)
     */
    public static function getAllBranches()
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT branch_id, branch_name,branch_code,sections FROM branches";
    $query = $database->prepare($sql);
    $query->execute();

    // fetchAll() is the PDO method that gets all result rows
    return $query->fetchAll();
}


     /**
     * Get a single note
     * @param int $note_id id of the specific note
     * @return object a single object (the result)
     */
public static function getBranch($branch_id)
{
    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "SELECT branch_name, branch_code, sections, branch_id FROM branches WHERE branch_id = :branch_id";
    $query = $database->prepare($sql);
    $query->execute(array(':branch_id' => $branch_id));

    // fetch() is the PDO method that gets a single result as an object
    return $query->fetch(PDO::FETCH_OBJ);
}


    /**
     * Set a note (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (was the note created properly ?)
     */

        /**
     * Set a note (create a new one)
     * @param string $branch_name note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
public static function createBranch($branch_name, $branch_code, $sections)
{
    if (!$branch_name || strlen($branch_name) == 0) {
        Session::add('feedback_negative', Text::get('FEEDBACK_BRANCH_CREATION_FAILED'));
        return false;
    }

    $database = DatabaseFactory::getFactory()->getConnection();

    $sql = "INSERT INTO branches (branch_name, branch_code, sections) VALUES (:branch_name, :branch_code, :sections)";
    $query = $database->prepare($sql);
    $query->execute(array(':branch_name' => $branch_name, ':branch_code' =>$branch_code, ':sections' => $sections));
    if ($query->rowCount() == 1) {
        return true;
    }

    return false;
}

public static function updateBranch($branch_id, $branch_name,$branch_code,$sections)
    {
        if (!$branch_id|| !$branch_name) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE branches SET branch_name = :branch_name, branch_code = :branch_code,sections = :sections WHERE branch_id = :branch_id";
        $query = $database->prepare($sql);
        $query->execute(array(':branch_id' => $branch_id, ':branch_name' => $branch_name, ':branch_code' =>$branch_code, ':sections' => $sections));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_BRANCH_EDITING_FAILED'));
        return false;
    }

      public static function deletebranch($branch_id)
    {
        if (!$branch_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM branches WHERE branch_id = :branch_id";
        $query = $database->prepare($sql);
        $query->execute(array(':branch_id' => $branch_id));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_DELETION_FAILED'));
        return false;
    }


}