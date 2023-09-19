<?php

class ProfileController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This method controls what happens when you move to /overview/index in your app.
     * Shows a list of all users.
     */
public function index()
{
    $users = UserModel::getPublicProfilesOfAllUsers();
    //$branches = UserModel::getBranches();

    //print_r($branches); // Debugging statement

    $this->View->render('profile/index', array(
        'users' => $users,
        //'branches' => $branches,
    ));
}




    /**
     * This method controls what happens when you move to /overview/showProfile in your app.
     * Shows the (public) details of the selected user.
     * @param $user_id int id the the user
     */
    public function showProfile($user_id)
    {
        if (isset($user_id)) {
            $this->View->render('profile/showProfile', array(
                'user' => UserModel::getPublicProfileOfUser($user_id))
            );
        } else {
            Redirect::home();
        }
    }

public function edit($user_id)
    {
        $this->View->render('profile/edit', array(
            'user' => UserModel::getPublicProfileOfUser($user_id),
            'branches' => UserModel::getBranches(),


        ));
    }


public function editsave()
    {
         UserModel::updateProfile(Request::post('user_id'), Request::post('user_name'), Request::post('branch_id'));
         Redirect::to('profile');
        
    }

 public function delete($user_id)
    {
        UserModel::deleteProfile($user_id);
        Redirect::to('profile/index');
    }

public function upload()
{
   // echo '===<pre>';
    //print_r($_FILES);
    //die;
    $resp = [];
    $resp['status'] = false;

    // Check if a file was uploaded
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0 && $_POST['user_id'] > 0) {
        $file = $_FILES['avatar'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        // Perform any additional validation or processing as needed

        // Example: Move the uploaded file to a specific directory
        $destinationDirectory = 'avatars/';
        $fileNameNew = $_POST['user_id'] . '.jpg'; // Set the file name using the user ID
        $fileDestination = $destinationDirectory . $fileNameNew;

        if(file_exists($fileDestination)) {
            unlink($fileDestination);
        }
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            // Set the status to true to indicate a successful upload
            UserModel::updateUserAvatarimage($_POST['user_id']);
            $resp['status'] = true;
            $resp['filename'] = $fileDestination.'?t='.time(); // Store the full path to the uploaded file
        } else {
            // There was an error moving the uploaded file
            $resp['error'] = 'There was an error moving the uploaded file.';
        }
    } else {
        // No file was uploaded or an error occurred
        $resp['error'] = 'No file was uploaded or an error occurred.';
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}

public function updateUserAvatarimage($userId)
{
    // Update the user's `user_has_avatar` field
    UserModel::updateUserAvatarimage($userId);

    
}

}
