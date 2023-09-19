<<?php 
/**
 * The branch controller: Just an example of simple create, read, update and delete (CRUD) actions.
 */
class BranchController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
        //Auth::checkAuthentication();
    }

      /**
     * This method controls what happens when you move to /branch/index in your app.
     * Gets all notes (of the branches).
     */
    public function index()
    {
        $this->View->render('branch/index', array(
            'branches' => BranchModel::getAllBranches()
        ));
    }


    /**
     * This method controls what happens when you move to /dashboard/create in your app.
     * Creates a new note. This is usually the target of form submit actions.
     * POST request.
     */
public function create()
{
    $branch_name = $_POST['branch_name'];
    $branch_code = $_POST['branch_code'];
    $sections = $_POST['sections'];
    
    
    

    BranchModel::createBranch($branch_name, $branch_code, $sections);
    Redirect::to('branch');
}



     /**
     * This method controls what happens when you move to /note/edit(/XX) in your app.
     * Shows the current content of the note and an editing form.
     * @param $note_id int id of the note
     */
   public function edit($branch_id)
{
    $branches = BranchModel::getBranch($branch_id);
    //var_dump($branches); // Debug statement
    $this->View->render('branch/edit', array(
        'branches' => $branches
    ));
}

      /**
     * This method controls what happens when you move to /note/editSave in your app.
     * Edits a note (performs the editing after form submit).
     * POST request.
     */

    public function editSave()
{
    BranchModel::updateBranch(Request::post('branch_id'), Request::post('branch_name'), Request::post('branch_code'),Request::post('sections'));
    Redirect::to('branch');
}

    public function delete($branch_id)
    {
        BranchModel::deletebranch($branch_id);
        Redirect::to('branch');
    }

}

 ?>