<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


try {
    
    if (!isset($lessons) || !$lessons) {
	    $lessons = EfrontLesson :: getLessons(true);
    }
    if (!isset($courses) || !$courses) {
	    $courses = EfrontCourse :: getCourses(true);
    }
    
	//Mark the lessons and courses that the user already has, so that they can't be selected
	try {
	    $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
	    if ($currentUser -> user['user_type'] == 'administrator') {
	        throw new Exception();
	    }
	    foreach ($currentUser -> getLessons() as $key => $value) {
	        if (in_array($key, array_keys($lessons))) {
	            $lessons[$key] -> lesson['has_lesson'] = 1;
	        }
	    }
	    foreach ($currentUser -> getCourses() as $key => $value) {
	        if (in_array($key, array_keys($courses))) {
	            $courses[$key] -> course['has_course'] = 1;
	        }
	    }
	    foreach ($lessons as $key => $lesson) {	        
	        if ($lesson -> lesson['max_users'] && sizeof($lesson -> getUsers('student')) >= $lesson -> lesson['max_users']) {
	            $lessons[$key] -> lesson['reached_max_users'] = 1;
	        }
	    }
	    foreach ($courses as $key => $course) {
	        if ($course -> course['max_users'] && sizeof($course -> getUsers('student')) >= $course -> course['max_users']) {
	            $courses[$key] -> course['reached_max_users'] = 1;
	        }
	    }
	} catch (Exception $e) {}
	    
	if (isset($_GET['filter'])) {
		foreach ($lessons as $value) {
			$lessonNames[$value -> lesson['id']] = array('name' => $value -> lesson['name']);
		}		
		$filtered = array_keys(eF_filterData($lessonNames, $_GET['filter']));
		foreach ($lessons as $key => $value) {
		    if (!in_array($key, $filtered)) {
		        unset($lessons[$key]);
		    }
		}
	  
		foreach ($courses as $value) {
			$courseNames[$value -> course['id']] = array('name' => $value -> course['name']);
		}
		$filtered = array_keys(eF_filterData($courseNames, $_GET['filter']));
		foreach ($courses as $key => $value) {
		    if (!in_array($key, $filtered)) {
		        unset($courses[$key]);
		    }
		}
	  
		$options['collapse']   = false;
		$options['search']     = false;
		$options['tree_tools'] = false;
		
		$treeString = $directionsTree -> toHTML(false, $lessons, $courses, false, $options);
		$smarty -> assign("T_DISPLAYCODE", $treeString);
		$smarty -> display('display_code.tpl');
		exit;
	}
	
	$smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $lessons, $courses, false, $options));
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
	

?>