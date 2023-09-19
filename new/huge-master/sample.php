 login controller

 public function login()
    {
        // check if csrf token is valid
        if (!Csrf::isTokenValid()) {
            LoginModel::logout();
            Redirect::home();
            exit();
        }

        // perform the login method, put result (true or false) into $login_successful
        $login_successful = LoginModel::login(
            Request::post('user_name'), Request::post('user_password'), Request::post('set_remember_me_cookie')
        );

        // check login status: if true, then redirect user to user/index, if false, then to login form again
        if ($login_successful) {
            if (Request::post('redirect')) {
                Redirect::toPreviousViewedPageAfterLogin(ltrim(urldecode(Request::post('redirect')), '/'));
            } else {
                Redirect::to('user/index');
            }
        } else {
            if (Request::post('redirect')) {
                Redirect::to('login?redirect=' . ltrim(urlencode(Request::post('redirect')), '/'));
            } else {
                Redirect::to('login/index');
            }
        }
    }




public static function login($user_name, $user_password, $set_remember_me_cookie = null)
    {
        // we do negative-first checks here, for simplicity empty username and empty password in one line
        if (empty($user_name) OR empty($user_password)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_OR_PASSWORD_FIELD_EMPTY'));
            return false;
        }

        // checks if user exists, if login is not blocked (due to failed logins) and if password fits the hash
        $result = self::validateAndGetUser($user_name, $user_password);

        // check if that user exists. We don't give back a cause in the feedback to avoid giving an attacker details.
        if (!$result) {
            //No Need to give feedback here since whole validateAndGetUser controls gives a feedback
            return false;
        }
        It checks if the $result is false, indicating that the user does not exist or the validation failed

        // stop the user's login if account has been soft deleted
        if ($result->user_deleted == 1) {
            Session::add('feedback_negative', Text::get('FEEDBACK_DELETED'));
            return false;
        }

        // stop the user from logging in if user has a suspension, display how long they have left in the feedback.
        if ($result->user_suspension_timestamp != null && $result->user_suspension_timestamp - time() > 0) {
            $suspensionTimer = Text::get('FEEDBACK_ACCOUNT_SUSPENDED') . round(abs($result->user_suspension_timestamp - time())/60/60, 2) . " hours left";
            Session::add('feedback_negative', $suspensionTimer);
            return false;
        }

        // reset the failed login counter for that user (if necessary)
        if ($result->user_last_failed_login > 0) {
            self::resetFailedLoginCounterOfUser($result->user_name);
        }

        // save timestamp of this login in the database line of that user
        self::saveTimestampOfLoginOfUser($result->user_name);

        // if user has checked the "remember me" checkbox, then write token into database and into cookie
        if ($set_remember_me_cookie) {
            self::setRememberMeInDatabaseAndCookie($result->user_id);
        }

        // successfully logged in, so we write all necessary data into the session and set "user_logged_in" to true
        self::setSuccessfulLoginIntoSession(
            $result->user_id, $result->user_name, $result->user_email, $result->user_account_type
        );

        // return true to make clear the login was successful
        // maybe do this in dependence of setSuccessfulLoginIntoSession ?
        return true;
    }




    private static function validateAndGetUser($user_name, $user_password)
    {
        // brute force attack mitigation: use session failed login count and last failed login for not found users.
        // block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
        // (limits user searches in database)
        if (Session::get('failed-login-count') >= 3 AND (Session::get('last-failed-login') > (time() - 30))) {
            Session::add('feedback_negative', Text::get('FEEDBACK_LOGIN_FAILED_3_TIMES'));
            return false;
        }

        // get all data of that user (to later check if password and password_hash fit)
        $result = UserModel::getUserDataByUsername($user_name);

        // check if that user exists. We don't give back a cause in the feedback to avoid giving an attacker details.
        // brute force attack mitigation: reset failed login counter because of found user
        if (!$result) {

            // increment the user not found count, helps mitigate user enumeration
            self::incrementUserNotFoundCounter();

            // user does not exist, but we won't to give a potential attacker this details, so we just use a basic feedback message
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_OR_PASSWORD_WRONG'));
            return false;
        }

        // block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
        if (($result->user_failed_logins >= 3) AND ($result->user_last_failed_login > (time() - 30))) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_WRONG_3_TIMES'));
            return false;
        }

        // if hash of provided password does NOT match the hash in the database: +1 failed-login counter
        if (!password_verify($user_password, $result->user_password_hash)) {
            self::incrementFailedLoginCounterOfUser($result->user_name);
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_OR_PASSWORD_WRONG'));
            return false;
        }

        // if user is not active (= has not verified account by verification mail)
        if ($result->user_active != 1) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET'));
            return false;
        }

        // reset the user not found counter
        self::resetUserNotFoundCounter();

        return $result;
    }


    private static function resetUserNotFoundCounter()
    {
        Session::set('failed-login-count', 0);
        Session::set('last-failed-login', '');
    }

    private static function incrementUserNotFoundCounter()
    {
        // Username enumeration prevention: set session failed login count and last failed login for users not found
        Session::set('failed-login-count', Session::get('failed-login-count') + 1);
        Session::set('last-failed-login', time());
    }


     public static function setSuccessfulLoginIntoSession($user_id, $user_name, $user_email, $user_account_type)
    {
        Session::init();

        // remove old and regenerate session ID.
        // It's important to regenerate session on sensitive actions,
        // and to avoid fixated session.
        // e.g. when a user logs in
        session_regenerate_id(true);
        $_SESSION = array();

        Session::set('user_id', $user_id);
        Session::set('user_name', $user_name);
        Session::set('user_email', $user_email);
        Session::set('user_account_type', $user_account_type);
        Session::set('user_provider_type', 'DEFAULT');

        // get and set avatars
        Session::set('user_avatar_file', AvatarModel::getPublicUserAvatarFilePathByUserId($user_id));
        Session::set('user_gravatar_image_url', AvatarModel::getGravatarLinkByEmail($user_email));

        // finally, set user as logged-in
        Session::set('user_logged_in', true);

        // update session id in database
        Session::updateSessionId($user_id, session_id());

        // set session cookie setting manually,
        // Why? because you need to explicitly set session expiry, path, domain, secure, and HTTP.
        // @see https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Cookies
        setcookie(session_name(), session_id(), time() + Config::get('SESSION_RUNTIME'), Config::get('COOKIE_PATH'),
            Config::get('COOKIE_DOMAIN'), Config::get('COOKIE_SECURE'), Config::get('COOKIE_HTTP'));

    }


 public static function incrementFailedLoginCounterOfUser($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE users
                   SET user_failed_logins = user_failed_logins+1, user_last_failed_login = :user_last_failed_login
                 WHERE user_name = :user_name OR user_email = :user_name
                 LIMIT 1";
        $sth = $database->prepare($sql);
        $sth->execute(array(':user_name' => $user_name, ':user_last_failed_login' => time() ));
    }



    public static function resetFailedLoginCounterOfUser($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE users
                   SET user_failed_logins = 0, user_last_failed_login = NULL
                 WHERE user_name = :user_name AND user_failed_logins != 0
                 LIMIT 1";
        $sth = $database->prepare($sql);
        $sth->execute(array(':user_name' => $user_name));
    }



    public static function saveTimestampOfLoginOfUser($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE users SET user_last_login_timestamp = :user_last_login_timestamp
                WHERE user_name = :user_name LIMIT 1";
        $sth = $database->prepare($sql);
        $sth->execute(array(':user_name' => $user_name, ':user_last_login_timestamp' => time()));
    }