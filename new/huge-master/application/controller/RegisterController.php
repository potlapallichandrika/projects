<?php

/**
 * RegisterController
 * Register new user
 */
class RegisterController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class. The parent::__construct thing is necessary to
     * put checkAuthentication in here to make an entire controller only usable for logged-in users (for sure not
     * needed in the RegisterController).
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Register page
     * Show the register form, but redirect to main-page if user is already logged-in
     */
    public function index()
    {
        if (LoginModel::isUserLoggedIn()) {
            Redirect::home();
        } else {
            $this->View->render('register/index');
        }
    }

    /**
     * Register page action
     * POST-request after form submit
     */
   public function register_action()
    {
    
        $registration_successful = RegistrationModel::registerNewUser();

        if ($registration_successful) {
            Redirect::to('login/index');
        } else {
            Redirect::to('register/index');
        }
    }

public function preview()
{
    /*echo '<pre>';
    print_r($_FILES);
    die;*/
    if (isset($_FILES['csvfile']) && $_FILES['csvfile']['error'] ==0) {
        $file_path = $_FILES['csvfile']['tmp_name'];
        $file_handle = fopen($file_path, 'r');
    if (!$file_handle) {
        return false; // Unable to open the file
    }

    // Process the CSV data
    $respData = '<table>';
    $respData.= '<tr><td>Name</td><td>Email</td><td>PWD</td></tr>';
    while (($data = fgetcsv($file_handle)) !== false) {
        /*echo '<pre>';
        print_r($data);
        die;*/
        $respData.= '<tr><td>'.$data[0].'</td><td>'.$data[1].'</td><td>'.$data[2].'</td></tr>';
    }
    $respData.= '</table>';
    echo $respData;
    die;

        // Process the uploaded CSV file here
         
    } else {
        // No CSV file uploaded or error occurred during upload
        echo ('Failed to upload the CSV file.');
    }

}
public function bulk_upload_action()
{
    if (isset($_FILES['csvfile']) && $_FILES['csvfile']['error'] ==0) {
        $file_path = $_FILES['csvfile']['tmp_name'];



        // Process the uploaded CSV file here
        $success = $this->processCsvData($file_path);

        if ($success) {
            // CSV data processing was successful
            echo 'CSV data uploaded and processed successfully.';
        } else {
            // CSV data processing failed
            echo 'Failed to process the uploaded CSV file.';
        }
    } else {
        // No CSV file uploaded or error occurred during upload
        echo ('Failed to upload the CSV file.');
    }

    // Redirect back to the registration page
    Redirect::to('register/index');
}

private function processCsvData($file_path)
{
    // Open the CSV file for reading
    $file_handle = fopen($file_path, 'r');
    if (!$file_handle) {
        return false; // Unable to open the file
    }

    // Process the CSV data
    while (($data = fgetcsv($file_handle)) !== false) {
        // Process each row of the CSV data
        // Example: extract values and perform any necessary operations
        $username = $data[0];
        $email = $data[1];
        $emailrepeat = $data[2];
        $password = $data[3];

        $_POST['user_name'] = $username;
        $_POST['user_email'] = $email;
        $_POST['user_email_repeat'] = $emailrepeat;
        $_POST['user_password_new'] = $password;

        // Example: Register the user using the UserModel
        $registration_successful = RegistrationModel::registerNewUser($username, $email, $password);

        // Check if registration was successful
        if (!$registration_successful) {
            fclose($file_handle);
            return false; // CSV data processing failed
        }
    }

    fclose($file_handle);

    return true; // CSV data processing was successful
} 

    /**
     * Verify user after activation mail link opened
     * @param int $user_id user's id
     * @param string $user_activation_verification_code user's verification token
     */
    public function verify($user_id, $user_activation_verification_code)
    {
        if (isset($user_id) && isset($user_activation_verification_code)) {
            RegistrationModel::verifyNewUser($user_id, $user_activation_verification_code);
            $this->View->render('register/verify');
        } else {
            Redirect::to('login/index');
        }
    }

    /**
     * Generate a captcha, write the characters into $_SESSION['captcha'] and returns a real image which will be used
     * like this: <img src="......./login/showCaptcha" />
     * IMPORTANT: As this action is called via <img ...> AFTER the real application has finished executing (!), the
     * SESSION["captcha"] has no content when the application is loaded. The SESSION["captcha"] gets filled at the
     * moment the end-user requests the <img .. >
     * Maybe refactor this sometime.
     */
    public function showCaptcha()
    {
        CaptchaModel::generateAndShowCaptcha();
    }



}
