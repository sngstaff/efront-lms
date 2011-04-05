<?php
try {
 $systemAvatars = array("" => _USENONE);
 $avatarsFileSystemTree = new FileSystemTree(G_SYSTEMAVATARSPATH);
 foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($avatarsFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('png')) as $key => $value) {
  $systemAvatars[basename($key)] = basename($key);
 }
 $smarty -> assign("T_SYSTEM_AVATARS", $systemAvatars);
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}

if (!isset($_GET['add_user'])) {
 try {
  $avatar = new EfrontFile($editedUser -> user['avatar']);
 } catch (Exception $e) {
  $avatar = new EfrontFile(G_SYSTEMAVATARSPATH."unknown_small.png");
 }
} else {
 $avatar = new EfrontFile(G_SYSTEMAVATARSPATH."unknown_small.png");
}

$roles = EfrontUser :: getRoles(true);
if ($currentUser->user['user_type'] != 'administrator') {
 $rolesPlain = EfrontUser :: getRoles();
 foreach ($roles as $key => $value) {
  if ($rolesPlain[$key] == 'administrator') {
   unset($roles[$key]);
  }
 }
}

$form = new HTML_QuickForm("user_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=personal&user=".$editedUser -> user['login']."&op=profile".(isset($_GET['add_user']) ? '&add_user=1' : ''), "", null, true);
$form -> addElement('static', '', '<img src = "view_file.php?file='.$avatar['path'].'" alt = "'.$editedUser -> user['login'].'" title = "'.$editedUser -> user['login'].'"/>');
$form -> addElement('file', 'file_upload', _IMAGEFILE, 'class = "inputText"');
$form -> addElement("static", "", _EACHFILESIZEMUSTBESMALLERTHAN.' <b>'.FileSystemTree::getUploadMaxSize().'</b> '._KB);
$form -> addElement("static", "sidenote", '(<a href = "'.basename($_SERVER['PHP_SELF']).'?ctg=personal&user='.$editedUser -> user['login'].'&op=profile&show_avatars_list=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup(\''._VIEWLIST.'\', 2)">'._VIEWLIST.'</a>)');
$form -> addElement('select', 'system_avatar' , _ORSELECTONEFROMLIST, $systemAvatars, "id = 'select_avatar'");
$form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
$form -> addElement("static", "sidenote", _BLANKTOLEAVEUNCHANGED);
$form -> addElement('password', 'password_', _PASSWORD, 'autocomplete="off" class = "inputText"');
$form -> addElement("static", "", str_replace("%x", $GLOBALS['configuration']['password_length'], _PASSWORDMUSTBE6CHARACTERS));
$form -> addElement('password', 'passrepeat', _REPEATPASSWORD, 'class = "inputText "');
$form -> addElement('text', 'name', _NAME, 'class = "inputText"');
$form -> addElement('text', 'surname', _SURNAME, 'class = "inputText"');
$form -> addElement('text', 'email', _EMAILADDRESS, 'class = "inputText"');
$form -> addElement('advcheckbox', 'active', _ACTIVEUSER, null, 'class = "inputCheckbox" id="activeCheckbox" ', array(0, 1));
$form -> addElement('select', 'user_type', _USERTYPE, $roles);
$form -> addElement('select', 'languages_NAME', _LANGUAGE, EfrontSystem :: getLanguages(true, true));
$form -> addElement("select", "timezone", _TIMEZONE, eF_getTimezones(), 'class = "inputText" style="width:20em"');
if ($GLOBALS['configuration']['social_modules_activated'] > 0) {
 $load_editor = true;
 $form -> addElement('textarea', 'short_description', _SHORTDESCRIPTIONCV, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:14em;"'); //The unit content itself
}

$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
$form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');
$form -> addRule('password_', str_replace("%x", $GLOBALS['configuration']['password_length'], _PASSWORDMUSTBE6CHARACTERS), 'minlength', $GLOBALS['configuration']['password_length'], 'client');
$form -> addRule(array('password_', 'passrepeat'), _PASSWORDSDONOTMATCH, 'compare', null, 'client');
$form -> addRule('name', _THEFIELD.' '._NAME.' '._ISMANDATORY, 'required', null, 'client');
$form -> addRule('surname', _THEFIELD.' '._SURNAME.' '._ISMANDATORY, 'required', null, 'client');
$form -> addRule('email', _INVALIDFIELDDATA, 'checkParameter', 'email');
if (isset($_GET['add_user'])) {
 $form -> addRule('login', _THELOGIN.' &quot;'.($form -> exportValue('login')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'login');
 $form -> addRule('login', _THEFIELD.' '._LOGIN.' '._ISMANDATORY, 'required', null, 'client');
 $form -> addRule('login', _INVALIDFIELDDATA, 'checkParameter', 'login');
 $form -> addRule('password_', _THEFIELD.' '._PASSWORD.' '._ISMANDATORY, 'required', null, 'client');
 $form -> addRule('passrepeat', _THEFIELD.' '._REPEATPASSWORD.' '._ISMANDATORY, 'required', null, 'client');
}

$form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
if (isset($_GET['add_user'])) {
 $constrainAccess = array();
 $form -> setDefaults(array('active' => 1,
          'timezone' => $GLOBALS['configuration']['time_zone'],
          'languages_NAME' => $GLOBALS['configuration']['default_language']));
 if ($GLOBALS['configuration']['default_type']) {
  $form -> setDefaults(array('user_type' => $GLOBALS['configuration']['default_type']));
 }
 foreach ($userProfile as $key => $field) {
  if ($field['type'] == 'date' && $field['default_value'] == 0) {
   $form -> setDefaults(array($field['name'] => time()));
  } else {
   $form -> setDefaults(array($field['name'] => $field['default_value']));
  }
 }
} else {
 $form -> setDefaults($editedUser -> user);
 if ($editedUser -> user['user_types_ID']) {
  $form -> setDefaults(array('user_type' => $editedUser -> user['user_types_ID']));
 }
 $form -> setDefaults(array("system_avatar" => $avatar['name']));
 if ((isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change')) {
  $constrainAccess = 'all';
 } else {
  $constrainAccess = array();
  $constrainAccess[] = 'login';
  if ($editedUser -> user['user_type'] == 'administrator' && $editedUser -> user['user_types_ID'] == 0 && $currentUser -> user['user_type'] == 'administrator' && $currentUser -> user['user_types_ID'] != 0) {
   //An admin subtype can't change a pure admin
   $constrainAccess[] = 'passrepeat';
   $constrainAccess[] = 'password_';
   $constrainAccess[] = 'user_type';
  }
  if ($editedUser -> isLdapUser) {
   $constrainAccess[] = 'passrepeat';
   $constrainAccess[] = 'password_';
  }
  if ($GLOBALS['configuration']['onelanguage']) {
   $constrainAccess[] = 'languages_NAME';
  }
  if ($editedUser->user['login'] == $currentUser->user['login']) { //A user can't change his own type, nor deactivate himself
   $constrainAccess[] = 'user_type';
   $constrainAccess[] = 'active';
   if ($currentUser->user['user_type'] != 'administrator') {
    if ($GLOBALS['configuration']['disable_change_info']) {
     $allFields = $form -> _elementIndex;
     unset($allFields['password_']);
     unset($allFields['passrepeat']);
     $constrainAccess = array_keys($allFields);
    }
    if ($GLOBALS['configuration']['disable_change_pass']) {
     $constrainAccess[] = 'passrepeat';
     $constrainAccess[] = 'password_';
    }
   }
  }
 }
}
if ($constrainAccess != 'all') {
 $form -> addElement('submit', 'submit_personal_details', _SUBMIT, 'class = "flatButton"');
 $form -> freeze($constrainAccess);
} else {
 $form -> freeze();
}
if ($form -> isSubmitted() && $form -> validate()) {
 try {
  $values = $form -> exportValues();
  $roles = EfrontUser :: getRoles();
  $userProperties = array('login' => $values['login'],
        'name' => $values['name'],
           'surname' => $values['surname'],
        'active' => $values['active'],
        'email' => $values['email'],
        'password' => $values['password_'],
        'user_type' => $roles[$values['user_type']],
        'languages_NAME' => $values['languages_NAME'],
        'timezone' => $values['timezone'],
         'timestamp' => time(),
        'password' => EfrontUser::createPassword($values['password_']),
        'user_types_ID' => $values['user_type'],
        'short_description' => $values['short_description']);
  foreach ($userProfile as $field) { //Get the custom fields values
   if ($field['type'] == "date") {
    $timestampValues = $values[$field['name']];
    $values[$field['name']] = mktime($timestampValues['H'], $timestampValues['i'], 0, $timestampValues['M'], $timestampValues['d'], $timestampValues['Y']);
   }
   $userProperties[$field['name']] = $values[$field['name']];
  }
  if (isset($_GET['add_user'])) {
   $editedUser = EfrontUser :: createUser($userProperties);
  } else {
   foreach ($constrainAccess as $value) {
    unset($userProperties[$value]);
   }
   if (!$values['password_']) {//If a password is not set, don't set it
    unset($userProperties['password']);
   }
   $editedUser->user = array_merge($editedUser->user, $userProperties);
   $editedUser->persist();
   if ($currentUser -> user['login'] == $editedUser -> user['login'] && $_SESSION['s_password'] != $editedUser -> user['password']) {
    $_SESSION['s_password'] = $editedUser -> user['password'];
   }
   if ($currentUser -> user['login'] == $editedUser -> user['login'] && $_SESSION['s_language'] != $editedUser -> user['languages_NAME']) {
    $_SESSION['s_language'] = $editedUser -> user['languages_NAME'];
   }
  }
  $avatarDirectory = G_UPLOADPATH.$editedUser -> user['login'].'/avatars';
  is_dir($avatarDirectory) OR mkdir($avatarDirectory, 0755);
  try {
   $filesystem = new FileSystemTree($avatarDirectory);
   $uploadedFile = $filesystem -> uploadFile('file_upload', $avatarDirectory);
   eF_normalizeImage($avatarDirectory . "/" . $uploadedFile['name'], $uploadedFile['extension'], 150, 100);// Normalize avatar picture to 150xDimY or DimX x 100
   $editedUser -> user['avatar'] = $uploadedFile['id'];
  } catch (Exception $e) {
   if ($e -> getCode() != UPLOAD_ERR_NO_FILE) {
    throw $e;
   }
   if ($form -> exportValue('system_avatar') == "") {
    $selectedAvatar = 'unknown_small.png';
   } else if (!$personal_profile_form || $form -> exportValue('system_avatar') != "") {
    $selectedAvatar = $form -> exportValue('system_avatar');
   }
   if (isset($selectedAvatar)) {
    $selectedAvatar = $avatarsFileSystemTree -> seekNode(G_SYSTEMAVATARSPATH.$selectedAvatar);
    $newList = FileSystemTree :: importFiles($selectedAvatar['path']); //Import the file to the database, so we can access it with view_file
    $editedUser -> user['avatar'] = key($newList);
   }
  }
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::AVATAR_CHANGE, "users_LOGIN" => $editedUser -> user['login'], "users_name" => $editedUser->user['name'], "users_surname" => $editedUser->user['surname'], "lessons_ID" => 0, "lessons_name" => "", "entity_ID" => $editedUser -> user['avatar']));
  if ($personal_profile_form) {
   $editedUser -> user['short_description'] = $form ->exportValue('short_description');
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::PROFILE_CHANGE, "users_LOGIN" => $editedUser -> user['login'], "users_name" => $editedUser->user['name'], "users_surname" => $editedUser->user['surname'], "lessons_ID" => 0, "lessons_name" => ""));
  }
  $editedUser -> persist();
  if ($editedUser->user['user_type'] == 'administrator' || !isset($_GET['add_user'])) {
   eF_redirect($_SERVER['PHP_SELF']."?ctg=personal&user=".$editedUser->user['login']."&op=profile&message=".urlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success");
  } else {
   eF_redirect($_SERVER['PHP_SELF']."?ctg=personal&user=".$editedUser->user['login']."&op=user_courses&message=".urlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success");
  }
 } catch (Exception $e) {
  handleNormalFlowExceptions($e);
 }
}
$smarty -> assign("T_PROFILE_FORM", $form -> toArray());