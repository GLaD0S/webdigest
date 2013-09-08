<?php

require_once('../config.php');
require_once('../lib/modinfolib.php');

class MoodleDatabase
{
	private $isconnected = false; //if this variable is true then the class is connected to the database
	private $dbhost;
	private $dbuser;
	private $dbpass;
	private $dbname;
	private $connect;

	function __construct()	//grab the database information neccecary.
	{
		global $CFG;
		$this->dbhost = $CFG->dbhost;
		$this->dbuser = $CFG->dbuser;
		$this->dbpass = $CFG->dbpass;
		$this->dbname = $CFG->dbname;
	}

	function courses()
	{
		$this->connect();

		//$query = "SELECT enrolid FROM taasa_user_enrolments WHERE userid='$uid'";
		//$query = "SELECT taasa_enrol.courseid FROM taasa_user_enrolments LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid=taasa_enrol.id WHERE taasa_user_enrolments.userid='$uid'";
		$query = "SELECT id, fullname FROM taasa_course WHERE id!=1"; //where the id is not equal to 1 is for the first entry in the taasa_course table, which is not a course.
		$result = mysqli_query($this->connect, $query);

		$fieldlist = array("id", "fullname");
		$formatted_results = $this->format_result($result, $fieldlist);

		$this->disconnect();
		if($result)
			return $formatted_results;
		else
			return false;
	}

	function courses_by_category($category)
	{
		$query = "";
		if($category == 1)
			$query = "SELECT taasa_course.id, taasa_course.fullname FROM taasa_course LEFT JOIN taasa_course_categories ON taasa_course.category=taasa_course_categories.id WHERE taasa_course.id!=1 AND taasa_course_categories.id=1"; //where the id is not equal to 1 is for the first entry in the taasa_course table, which is not a course.
		else if($category == 2)
			$query = "SELECT taasa_course.id, taasa_course.fullname FROM taasa_course LEFT JOIN taasa_course_categories ON taasa_course.category=taasa_course_categories.id WHERE taasa_course.id!=1 AND taasa_course_categories.id=2"; //where the id is not equal to 1 is for the first entry in the taasa_course table, which is not a course.
		else
			$query = "SELECT id, fullname FROM taasa_course WHERE id!=1"; //where the id is not equal to 1 is for the first entry in the taasa_course table, which is not a course.


		$this->connect();

		//$query = "SELECT enrolid FROM taasa_user_enrolments WHERE userid='$uid'";
		//$query = "SELECT taasa_enrol.courseid FROM taasa_user_enrolments LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid=taasa_enrol.id WHERE taasa_user_enrolments.userid='$uid'";
		//$query = "SELECT id, fullname FROM taasa_course WHERE id!=1"; //where the id is not equal to 1 is for the first entry in the taasa_course table, which is not a course.
		$result = mysqli_query($this->connect, $query);

		$fieldlist = array("id", "fullname");
		$formatted_results = $this->format_result($result, $fieldlist);

		$this->disconnect();
		if($result)
			return $formatted_results;
		else
			return false;
	}

	function courses_mod_visible($uid)
	{
		$this->connect();
		
		/*$query = "SELECT taasa_course.id, taasa_course.fullname, taasa_course_modules.module, taasa_course_modules_completion.timemodified AS timecompleted, taasa_course_modules.id AS cmid
		FROM taasa_course 
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course 
		LEFT JOIN taasa_course_modules_completion ON taasa_course_modules.id=taasa_course_modules_completion.coursemoduleid 
		AND taasa_course_modules_completion.userid='$uid' 
		WHERE taasa_course.id!=1";*/

		$query = "SELECT taasa_course.id, taasa_course.fullname, taasa_course_modules.module, taasa_course_modules_completion.timemodified AS timecompleted, taasa_course_modules.id AS cmid
		FROM taasa_course 
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course 
		LEFT JOIN taasa_course_categories ON taasa_course.category=taasa_course_categories.id
		LEFT JOIN taasa_course_modules_completion ON taasa_course_modules.id=taasa_course_modules_completion.coursemoduleid
		AND taasa_course_modules_completion.userid='$uid' 
		WHERE taasa_course.id!=1 AND taasa_course.category=1";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		//preprocess enrolled in hash table
		$enrolledin = $this->user_courses_enrolled($uid); // enrolled in formatted results.
		$enrolledin_hash = array();
		$i = 0;
		while($i < count($enrolledin))
		{
			$id = $enrolledin[$i]['id'];
			$enrolledin_hash[$id] = $enrolledin[$i]['enrolled'];
			$i++;
		}



		$fieldlist = array("id", "fullname", "module" , "timecompleted", "cmid");
		$formatted_results = $this->format_result($result, $fieldlist);

		//echo $this->print_formatted_results($formatted_results, $fieldlist, "Course Moduels With Visibility");

		$formatted_results_copy = array();

		$i = 0;
		while($i < count ($formatted_results))
		{
			$id = $formatted_results[$i]['id'];
			$cmid = $formatted_results[$i]['cmid'];
			$fullname = $formatted_results[$i]['fullname'];
			$module = $formatted_results[$i]['module'];
			$timecompleted = $formatted_results[$i]['timecompleted'];
			$enrolled = $enrolledin_hash[$id];

			$formatted_results_copy[$i]['id'] = $id;
			$formatted_results_copy[$i]['cmid'] = $cmid;
			$formatted_results_copy[$i]['fullname'] = $fullname;
			$formatted_results_copy[$i]['enrolled'] = $enrolled;
			$formatted_results_copy[$i]['module'] = $module;
			$formatted_results_copy[$i]['timecompleted'] = $timecompleted;

			$modinfo = get_fast_modinfo($id, $uid);

			if($cmid != NULL)
			{
				$cm = $modinfo->get_cm($cmid);
				if(!$cm->uservisible)
					$formatted_results_copy[$i]['visible'] = false;
				else
					$formatted_results_copy[$i]['visible'] = true;
			}
			else
				$formatted_results_copy[$i]['visible'] = false;

			$i++;
		}

		$fieldlist = array("id", "fullname", 'cmid', 'module', 'enrolled', 'visible');
		$output = $this->print_formatted_results($formatted_results_copy, $fieldlist, "Course Moduels With Visibility");

		if($result)
			return $formatted_results_copy;
		else
			return false;
	}

	function courses_completed($uid)
	{
		$this->connect();
		
		$query = "SELECT taasa_course.id, taasa_course.fullname, taasa_course_modules.module, taasa_scorm_scoes_track.timemodified AS timecompleted, taasa_course_modules.id AS cmid
		FROM taasa_course 
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course 
		LEFT JOIN taasa_scorm ON taasa_course.id=taasa_scorm.course
		LEFT JOIN taasa_scorm_scoes_track ON taasa_scorm.id=taasa_scorm_scoes_track.scormid
		AND taasa_scorm_scoes_track.userid='$uid'
		AND taasa_scorm_scoes_track.value='passed'
		WHERE taasa_course.id!=1 ORDER BY taasa_scorm_scoes_track.timemodified DESC";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		//preprocess enrolled in hash table
		$enrolledin = $this->user_courses_enrolled($uid); // enrolled in formatted results.
		$enrolledin_hash = array();
		$i = 0;
		while($i < count($enrolledin))
		{
			$id = $enrolledin[$i]['id'];
			$enrolledin_hash[$id] = $enrolledin[$i]['enrolled'];
			$i++;
		}

		$fieldlist = array("id", "fullname", "module" , "timecompleted", "cmid");
		$formatted_results = $this->format_result($result, $fieldlist);

		$formatted_results_copy = array();


		$i = 0;
		while($i < count ($formatted_results))
		{
			$id = $formatted_results[$i]['id'];
			$cmid = $formatted_results[$i]['cmid'];
			$fullname = $formatted_results[$i]['fullname'];
			$module = $formatted_results[$i]['module'];
			$timecompleted = $formatted_results[$i]['timecompleted'];
			$enrolled = $enrolledin_hash[$id];

			$formatted_results_copy[$i]['id'] = $id;
			$formatted_results_copy[$i]['cmid'] = $cmid;
			$formatted_results_copy[$i]['fullname'] = $fullname;
			$formatted_results_copy[$i]['enrolled'] = $enrolled;
			$formatted_results_copy[$i]['module'] = $module;
			$formatted_results_copy[$i]['timecompleted'] = $timecompleted;

			$modinfo = get_fast_modinfo($id, $uid);

			if($cmid != NULL)
			{
				$cm = $modinfo->get_cm($cmid);
				if(!$cm->uservisible)
					$formatted_results_copy[$i]['visible'] = false;
				else
					$formatted_results_copy[$i]['visible'] = true;
			}
			else
				$formatted_results_copy[$i]['visible'] = false;

			$i++;
		}

		$fieldlist = array("id", "fullname", 'cmid', 'module', 'enrolled', 'visible');
		$output = $this->print_formatted_results($formatted_results_copy, $fieldlist, "Course Moduels With Visibility");

		if($result)
			return $formatted_results_copy;
		else
			return false;
	}

	function courses_completed_test($uid)
	{
		$this->connect();
		
		$query = "SELECT taasa_course.id, taasa_course.category, taasa_course.fullname, taasa_course_modules.module, taasa_scorm_scoes_track.timemodified AS timecompleted, taasa_course_modules.id AS cmid
		FROM taasa_course
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course 
		LEFT JOIN taasa_scorm ON taasa_course.id=taasa_scorm.course
		LEFT JOIN taasa_scorm_scoes_track ON taasa_scorm.id=taasa_scorm_scoes_track.scormid
		AND taasa_scorm_scoes_track.userid='$uid'
		AND taasa_scorm_scoes_track.value='passed'
		WHERE taasa_course.id!=1 ORDER BY taasa_scorm_scoes_track.timemodified DESC";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		//preprocess enrolled in hash table
		$enrolledin = $this->user_courses_enrolled($uid); // enrolled in formatted results.
		$enrolledin_hash = array();
		$i = 0;
		while($i < count($enrolledin))
		{
			$id = $enrolledin[$i]['id'];
			$enrolledin_hash[$id] = $enrolledin[$i]['enrolled'];
			$i++;
		}

		$fieldlist = array("id", "category", "fullname", "module" , "timecompleted", "cmid");
		$formatted_results = $this->format_result($result, $fieldlist);
		$formatted_results_copy = array();


		$i = 0;
		while($i < count ($formatted_results))
		{
			$id = $formatted_results[$i]['id'];
			$category = $formatted_results[$i]['category'];
			$cmid = $formatted_results[$i]['cmid'];
			$fullname = $formatted_results[$i]['fullname'];
			$module = $formatted_results[$i]['module'];
			$timecompleted = $formatted_results[$i]['timecompleted'];
			$enrolled = $enrolledin_hash[$id];

			$formatted_results_copy[$i]['id'] = $id;
			$formatted_results_copy[$i]['category'] = $category;
			$formatted_results_copy[$i]['cmid'] = $cmid;
			$formatted_results_copy[$i]['fullname'] = $fullname;
			$formatted_results_copy[$i]['enrolled'] = $enrolled;
			$formatted_results_copy[$i]['module'] = $module;
			$formatted_results_copy[$i]['timecompleted'] = $timecompleted;

			$modinfo = get_fast_modinfo($id, $uid);

			if($cmid != NULL)
			{
				$cm = $modinfo->get_cm($cmid);
				if(!$cm->uservisible)
					$formatted_results_copy[$i]['visible'] = false;
				else
					$formatted_results_copy[$i]['visible'] = true;
			}
			else
				$formatted_results_copy[$i]['visible'] = false;

			$i++;
		}

		$fieldlist = array("id", "category", "fullname", 'cmid', 'module', 'enrolled', 'visible');
		$output = $this->print_formatted_results($formatted_results_copy, $fieldlist, "Course Moduels With Visibility");
		if($result)
			return $formatted_results_copy;
		else
			return false;
	}

	function user_courses($uid)	//this function will return the courses that a user is enrolled in.
	{
		$this->connect();

		//$query = "SELECT enrolid FROM taasa_user_enrolments WHERE userid='$uid'";
		//$query = "SELECT taasa_enrol.courseid FROM taasa_user_enrolments LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid=taasa_enrol.id WHERE taasa_user_enrolments.userid='$uid'";
		$query = "SELECT taasa_enrol.courseid, taasa_course.fullname FROM taasa_user_enrolments LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid=taasa_enrol.id LEFT JOIN taasa_course ON taasa_enrol.courseid=taasa_course.id WHERE taasa_user_enrolments.userid='$uid'";
		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		$fieldlist = array("courseid", "fullname");
		$formatted_results = $this->format_result($result, $fieldlist);

		if($result)
			return $formatted_results;
		else
			return false;
	}

	function users_report()
	{
		$this->connect();

		$query = "SELECT taasa_user.username, taasa_user.id, taasa_user.firstname, taasa_user.lastname, taasa_user.city, taasa_user.email, taasa_user.country, taasa_user.firstaccess, taasa_user.lastaccess,
		workt.data AS work,
   		jobt.data AS job,
   		statet.data AS state,
   		occut.data AS occupation
   		FROM taasa_user
   		LEFT JOIN taasa_user_info_data AS workt ON taasa_user.id = workt.userid AND workt.fieldid = 1
		LEFT JOIN taasa_user_info_data AS jobt ON taasa_user.id = jobt.userid AND jobt.fieldid = 2
		LEFT JOIN taasa_user_info_data AS statet ON taasa_user.id = statet.userid AND statet.fieldid = 3
		LEFT JOIN taasa_user_info_data AS occut ON taasa_user.id = occut.userid AND occut.fieldid = 4
		WHERE taasa_user.id!=1 AND taasa_user.id!=2 AND taasa_user.deleted!=1";

		$result = mysqli_query($this->connect, $query);
		$this->disconnect();

		$fieldlist = array("username", "id", "firstname", "lastname", "city", "email", "work", "job", "state", "occupation", "country", "firstaccess", "lastaccess");
		$formatted_results = $this->format_result($result, $fieldlist);

		/*$output = $this->print_formatted_results($formatted_results, $fieldlist, "User Info");
		echo $output;*/

		if($result)
			return $formatted_results;
		else
			return false;
	}

	function courses_report()
	{
		$this->connect();

		$query = "SELECT taasa_course.fullname, taasa_user_enrolments.enrolid, taasa_enrol.courseid, COUNT(taasa_course.fullname) AS count FROM taasa_user_enrolments
		LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid = taasa_enrol.id
		LEFT JOIN taasa_course ON taasa_course.id = taasa_enrol.courseid
		WHERE taasa_enrol.enrol = 'self' GROUP BY taasa_course.fullname";

		$result = mysqli_query($this->connect, $query);

		//echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("fullname", "enrolid", "courseid", "count");
		$formatted_results = $this->format_result($result, $fieldlist);

		$output = $this->print_formatted_results($formatted_results, $fieldlist, "User Info");
		echo $output;

		if($result)
			return $formatted_results;
		else
			return false;

	}

	function courses_report_test()
	{
		$this->connect();

		/*$query = "SELECT taasa_course.fullname, taasa_user_enrolments.enrolid, taasa_enrol.courseid, COUNT(taasa_course.fullname) AS count FROM taasa_user_enrolments
		LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid = taasa_enrol.id
		LEFT JOIN taasa_course ON taasa_course.id = taasa_enrol.courseid
		WHERE taasa_enrol.enrol = 'self' GROUP BY taasa_course.fullname";

		$query = "SELECT taasa_course.id, taasa_course.fullname, taasa_course_modules.module, taasa_course_modules_completion.timemodified AS timecompleted, taasa_course_modules.id AS cmid
		FROM taasa_course 
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course 
		LEFT JOIN taasa_course_modules_completion ON taasa_course_modules.id=taasa_course_modules_completion.coursemoduleid 
		AND taasa_course_modules_completion.userid='$uid' 
		WHERE taasa_course.id!=1";*/

		/*taasa_course_modules.module needs to be selected only when it is 18 FIX THIS!*/
		$query = "SELECT taasa_course_modules.module, taasa_course.fullname, taasa_user_enrolments.enrolid, taasa_enrol.courseid, COUNT(taasa_course.fullname) AS count, taasa_course_modules.id AS cmid FROM taasa_user_enrolments
		LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid = taasa_enrol.id
		LEFT JOIN taasa_course ON taasa_course.id = taasa_enrol.courseid
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course
		WHERE taasa_enrol.enrol = 'self' AND taasa_course_modules.module = 18 GROUP BY taasa_course.fullname";

		$result = mysqli_query($this->connect, $query);

		//echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("fullname", "enrolid", "courseid", "count", "module", "cmid");
		$formatted_results = $this->format_result($result, $fieldlist);
		/*$output = $this->print_formatted_results($formatted_results, $fieldlist, "User Info");
		echo $output;

		$hash = $this->hash_formatted_results($formatted_results, $fieldlist, "courseid");
		var_dump($hash);*/



		if($result)
			return $formatted_results;
		else
			return false;

	}

	function state_report()
	{
		$this->connect();

		$query = "SELECT data, COUNT(data) AS count FROM taasa_user_info_data WHERE fieldid = 3 GROUP BY data";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		$fieldlist = array("data", "count");
		$formatted_results = $this->format_result($result, $fieldlist);

		if($result)
			return $formatted_results;
		else
			return false;
	}

	function city_report()
	{
		$this->connect();

		$query = "SELECT city, COUNT(city) AS count FROM taasa_user WHERE id!=1 AND id!=2 AND deleted!=1 GROUP BY city";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		$fieldlist = array("city", "count");
		$formatted_results = $this->format_result($result, $fieldlist);

		if($result)
			return $formatted_results;
		else
			return false;

	}

	function user_course()
	{
		$this->connect();

		/*$query = "SELECT taasa_course_modules.module, taasa_course.fullname, taasa_user_enrolments.enrolid, taasa_enrol.courseid, COUNT(taasa_course.fullname) AS count, taasa_course_modules.id AS cmid
		FROM taasa_user_enrolments
		LEFT JOIN taasa_enrol ON taasa_user_enrolments.enrolid = taasa_enrol.id
		LEFT JOIN taasa_course ON taasa_course.id = taasa_enrol.courseid
		LEFT JOIN taasa_course_modules ON taasa_course.id=taasa_course_modules.course
		WHERE taasa_enrol.enrol = 'self' GROUP BY taasa_course.fullname";*/

		$query = "SELECT taasa_user_enrolments.userid, taasa_enrol.courseid 
		FROM taasa_enrol
		LEFT JOIN taasa_user_enrolments ON taasa_enrol.id = taasa_user_enrolments.enrolid
		WHERE taasa_enrol.enrol = 'self'
		";

		$result = mysqli_query($this->connect, $query);

		echo $this->connect->error;

		$this->disconnect();

		$fieldlist = array("userid", "courseid");
		$formatted_results = $this->format_result($result, $fieldlist);

		/*$output = $this->print_formatted_results($formatted_results, $fieldlist, "User Course Enrol Info");
		echo $output;*/

		if($result)
			return $formatted_results;
		else
			return false;
	}

	function user_courses_enrolled($uid)
	{
		$user_courses = $this->user_courses($uid);
		$courses = $this->courses();
		$courses_copy = $this->courses();

		$user_courses_numof = count($user_courses);
		$courses_numof = count($courses);


		$i = 0;
		while($i < $courses_numof)
		{
			$id = $courses[$i]['id'];
			if($this->in_formatted_results($id, $user_courses, "courseid"))
				$courses_copy[$i]['enrolled'] = "true";
			else
				$courses_copy[$i]['enrolled'] = "false";
			$i++;
		}

		return $courses_copy;

	}

	function get_username($uid)
	{
		$this->connect();

		$query = "SELECT username FROM taasa_user WHERE id = '$uid'";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		$fieldlist = array("username");
		$formatted_results = $this->format_result($result, $fieldlist);

		return $formatted_results[0]['username'];
	}

	function get_lastest_scorm_grade($uid, $cid) //this function will breakdown if there is more than one quiz per course
	{

		$this->connect();

		$query = "SELECT taasa_scorm_scoes_track.timemodified FROM taasa_scorm 
		LEFT JOIN taasa_scorm_scoes_track ON taasa_scorm.id = taasa_scorm_scoes_track.scormid
		WHERE taasa_scorm.course = '$cid' AND taasa_scorm_scoes_track.userid = '$uid' AND taasa_scorm_scoes_track.value = 'passed' ORDER BY taasa_scorm_scoes_track.timemodified DESC LIMIT 0, 1";

		$result = mysqli_query($this->connect, $query);
		$this->disconnect();

		$fieldlist = array("timemodified");
		$formatted_results = $this->format_result($result, $fieldlist);

//var_dump($formatted_results);

		return $formatted_results[0]['timemodified'];

	}

	function get_a_scorm_mod_id()
	{
		$this->connect();

		$query = "SELECT id FROM taasa_course_modules WHERE module = 18";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		$fieldlist = array("id");
		$formatted_results = $this->format_result($result, $fieldlist);
		
		if($formatted_results[0])
			return $formatted_results[0]['id'];
		else
			return null;
	}

	function get_user_type($uid)
	{
		$this->connect();

		$query = "SELECT taasa_user_info_data.data AS occupation FROM taasa_user_info_data
		LEFT JOIN taasa_user_info_field ON taasa_user_info_data.fieldid=taasa_user_info_field.id
		WHERE taasa_user_info_data.userid = '$uid' AND taasa_user_info_field.name = 'Occupation'";

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();
		//var_dump($result);

		$fieldlist = array("occupation");
		$formatted_results = $this->format_result($result, $fieldlist);
		//var_dump($formatted_results);
		$occupation = $formatted_results[0]['occupation'];
		//echo "<p>Occupation: $occupation</p>";
		
		if($formatted_results[0])
			return $formatted_results[0]['occupation'];
		else
			return null;
	}

	function datatable_query($query, $fieldlist)
	{
		$this->connect();

		$result = mysqli_query($this->connect, $query);

		$this->disconnect();

		$formatted_results = $this->format_result($result, $fieldlist);

		if($result)
			return $formatted_results;
		else
			return false;
	}

	function connect()
	{
		$this->connect = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
		if(!mysqli_connect_errno($this->connect))
			$this->isconnected = true;
		else
			exit();
	}

	function disconnect()
	{
		mysqli_close($this->connect);
		$this->isconnected = false;
		$this->connect = 0;
	}

	function format_result($result, $fieldlist)  
	{
		$fields = count($fieldlist);
		$results_array = array();
		$i = 0; 					//mysqli row loop
		$j = 0;						//for fields loop

		while ($row = mysqli_fetch_assoc($result))
		{
			while($j < $fields)
			{
				$fieldname = $fieldlist[$j];
				$results_array[$i][$fieldname] = $row[$fieldname];
				$j++;
			}
			$j = 0;
			$i++;
		}

		return $results_array;
	}

	function format_result_classic($result, $fieldlist)  
	{
		$fields = count($fieldlist);
		$results_array = array();
		$i = 0; 					//mysqli row loop
		$j = 0;						//for fields loop

		while ($row = mysql_fetch_assoc($result))
		{
			while($j < $fields)
			{
				$fieldname = $fieldlist[$j];
				$results_array[$i][$fieldname] = $row[$fieldname];
				$j++;
			}
			$j = 0;
			$i++;
		}

		return $results_array;
	}

	function hash_formatted_results($formatted_results, $fieldlist, $key)  
	{
		$i = 0;
		$key_exists = false;
		while($i < count($fieldlist))
		{
			if($fieldlist[$i] == $key)
			{
				$key_exists = true;
				break;
			}
			$i++;
		}

		$i = 0;
		$j = 0;
		$hash = array();
		if($key_exists)
		{
			while ($i < count($formatted_results))
			{
				$k = $formatted_results[$i][$key];
				$j = 0;
				while($j < count($fieldlist))
				{
					if($fieldlist[$j] != $key)
					{
						$field = $fieldlist[$j];
						$hash[$k][$field] = $formatted_results[$i][$field];
					}
					$j++;
				}
				$i++;
			}
		}

		return $hash;
	}

	function in_formatted_results($searchfor, $results_array, $fieldname) //NOT SQL RESULTS ARRAY
	{
		$num = count($results_array);

		$i = 0;
		while($i < $num)
		{
			if($results_array[$i][$fieldname] == $searchfor)
				return true;
			$i++;
		}
		return false;
	}

    function print_formatted_results($formatted_results, $fields, $name)
    {
    	//echo "<p>in print_formatted_results()</p>";
    	$num = count($formatted_results);
    	//echo "<p>after count()</p>";
    	$i = 0;
    	$output ="";
    	$output .= "<div id='formatted_results' style='background-color:white; position:relative;'>";
    	$output .= "<p style='font-size:130%; font-weight:bold;'>$name</p>";
    	$output .= "<p style='font-weight:bold;'>array($num){</p>";
    	while($i < $num)
    	{
    		//echo "<p>outer while: $i</p>";
    		$numinner = count($fields);
    		$output .= "<p style='font-weight:bold;'>&nbsp;&nbsp;array($numinner){</p>";
    		$j = 0;
    		while($j < $numinner)
    		{
    			//echo "<p>inner while: $j</p>";
    			$field = $fields[$j];
    			$value = $formatted_results[$i][$fields[$j]];
    			$output .= "<p>";
    			$output .= "&nbsp;&nbsp;&nbsp;&nbsp;$j: $field, $value";
    			$output .= "</p>";
    			$j++;
    		}
    		
    		$output .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;}</p>";
    		$i++;
    	}
   		$output .= "<p>}</p>";
    	$output .= "</div>";
    
    	return $output;
    
    }

}



?>
