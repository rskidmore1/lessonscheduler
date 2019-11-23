<?php
/**
 * Plugin Name: My GCal Plugin
 * Description: Displays upcoming events from public calendars
 * Version: 1.0.0
 */


require __DIR__ . '/vendor/autoload.php';

class My_GCal_Widget extends WP_Widget {



public $client;

public $service; 
public $results; 


public function __construct() {
    parent::__construct('my_gcal_widget', 'My GCal Widget',
            array(
                'classname' => 'my_gcal_widget',
                'description' => 'Shows events from a calendar'
            )
    );

}

public function printClass(){  //Test class to figure out google gcal 
	
	global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calID . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	
	
	
// Start date from where to get the events
	$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-30 days'));
	$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+ 30 days'));
    echo 'After time range for dates <br>'; 

	//Get event details
	$events = $service->events->listEvents($calID, $optParams );
	
while(true) { 
  foreach ($events->getItems() as $event) {
    echo ' '. $event->getSummary();
	echo ' '. $event->getId();
	$start = $event->getStart();
	echo 'start: '.  $event->start->dateTime; 
	echo 'End: ' . $event->end->dateTime;
	$getPID = $event->getDescription();
	echo ' '.$getPID.'<br>';
	echo 'PID position: '.strpos($getPID, 'ID:').'<br>';
	$pidPosition = strpos($getPID, 'ID:');
	echo ' PID selection: '.substr($getPID, $pidPosition+1,$pidPosition+10).'<br>';  
	$extractPID= substr($getPID, $pidPosition+1,$pidPosition+10); 
	$pidArray = explode(' ', $extractPID); 
	$pid = substr($pidArray[1], 0, 2); 
	echo ' PID: '. $pid.'<br>'; 
	echo ' End section.    '.'<br>';
	
  }
  $pageToken = $events->getNextPageToken();
  if ($pageToken) {
    $optParams = array('pageToken' => $pageToken);
    $events = $service->events->listEvents('primary', $optParams);
  } else {
    break;
  }
}
//End events sections 

	
	
	  $optParams = array(
    "timeMin" => date('Y-m-d\TH:i:s', strtotime('Today'. ' - '. '4' .' days')),
    "timeMax" => date('Y-m-d\TH:i:s', strtotime('Today'. ' + '. '100' .' weeks'))
  );
	
	
	


  $events = $service->events->listEvents($calID, $optParams);

  foreach ($events->getItems() as $event) {    

    echo $eventnum . " - Event Name: ". $event->summary . "<br>";

  }
  //End date section 

}


 
	//$placeholderweeks
	public $placeholder;  //Declares in class to pass between methods 
	public $placeholderweeks; 
	public $placeholderdate; 
	

	
	
	
	
//Start create Event Section 
public function createEvent($lessoncount){ //TEst class to create a google event via API 
	
	
	
    global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calendarId = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	
	
	
	
	//-------------------------------------------------------------------------------------------
	//Old calendar code 
	/**$client = new Google_Client();
	$client->setAuthConfig(__DIR__ . '/hhfportal2-0ea6f2bffa21.json');

	$client->addScope("https://www.googleapis.com/auth/calendar.events");
	$service = new Google_Service_Calendar($client);

	$calendarId = "CALID "; 
    **/
	
	//--------------------------------------------------------------------------------------------------
  $scheduleEventDate = '2019-04-28T09:00:00-07:00'; 
  $scheduleEventDateEnd = '2019-04-28T10:00:00-07:00'; 
	
	$event1 = new Google_Service_Calendar_Event(array(
	  'summary' => 'Google I/O 2015',  //Set this 
	  'location' => '800 Howard St., San Francisco, CA 94103', //Set this to HHF 
	  'description' => 'A chance to hear more about Google\'s developer products.',
	  'start' => array(
		'dateTime' => $event->start->dateTime, //Make date input here 
		'timeZone' => 'America/Los_Angeles',
	  ),
	  'end' => array(
		'dateTime' => $event->end->dateTime, //Make end date input here 
		'timeZone' => 'America/Los_Angeles',
	  ),
	  'recurrence' => array(
		'RRULE:FREQ=WEEKLY;COUNT='. $lessoncount 
	  ),


	  ));

	
	if ($lessoncount == '3'){
		$placeholderweeks = '3'; 
		$placeholderrecurringevents = '6'; //Weeks 
	} elseif ( $lessoncount == '7'){
		$placeholderweeks = '7'; 
		$placeholderrecurringevents = '11';  //Weeks 
	} elseif ( $lessoncount == '1'){
		$placeholderweeks = '1'; 
		$placeholderrecurringevents = '2';  //Weeks 
	}
	
	//Placeholder code 

	$placeholderdate = date('Y-m-d\TH:i:s', strtotime($scheduleEventDate. ' + '. $placeholderweeks .' weeks'));
    $placeholderdateend = date('Y-m-d\TH:i:s', strtotime($scheduleEventDateEnd. ' + '. $placeholderweeks .' weeks'));
	$event2 = new Google_Service_Calendar_Event(array(
  'summary' => 'Placeholder event',  //Set this 
  'location' => '800 Howard St., San Francisco, CA 94103', //Set this to HHF 
  'description' => 'A chance to hear more about Google\'s developer products.',
  'start' => array(
    'dateTime' => $placeholderdate, //Make date input here 
    'timeZone' => 'America/Los_Angeles',
  ),
  'end' => array(
    'dateTime' => $placeholderdateend, //Make end date input here 
    'timeZone' => 'America/Los_Angeles',
  ),
  'recurrence' => array(
    'RRULE:FREQ=WEEKLY;COUNT='. $placeholderrecurringevents //How many times placeholder event will recurr 
  ),
  
  
  )
);
	


	$eventCreate1 = $service->events->insert($calendarId, $event1);
	printf('Event created: %s\n', $eventCreate1->htmlLink);
	echo 'Event ID: ' . $eventCreate1->id; //Gets ID to place in the database 

	
	
$eventCreate2 = $service->events->insert($calendarId, $event2);
printf('Event created: %s\n', $eventCreate2->htmlLink);	
		
		
}
//Eend create event method 	


public function deleteEvent($eventID){ //Delte funciton after relcurring events are created 
	
	
	
	global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calendarId = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	
	
	
	//-----------------------------------------------------------------------------------	
	//Old calendar code 
	/**$client = new Google_Client();
	$client->setAuthConfig(__DIR__ . '/hhfportal2-0ea6f2bffa21.json');
	$client->addScope("https://www.googleapis.com/auth/calendar.events");
	$service = new Google_Service_Calendar($client);
	$calendarId = 'calid'; **/
	//-----------------------------------------------------------------------------------
	
	$service->events->delete($calendarId, $eventID);
	echo 'Event deleted.'; 

}




	
//Start createEvent method 
public function recreateEvent(){ //Crreates recurring event after calendly 
	
	global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	
	

	$calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 

	
	// Start date from where to get the events
	$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-2 days'));
	$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+ 30 days'));
  

	//Get event details
	$events = $service->events->listEvents($calID, $optParams );
	echo 'Events count: '. count($events)."<br>"; 
	$eventCount = count($events); 
	 $counter1 = 0 ; 
	

	//Loop over events 
	for ($counter1 =  0; $counter1 <= $eventCount;  $counter1++){ 
	
	$event = $events[$counter1]; 
	echo '<br>'; 
	
	//Gets details for each event 
    echo ' '. $event->getSummary();
	echo ' '. $event->getId();
	$eventID = $event->getId();
	$getPID = $event->getDescription();
	
	//Finds position of PID in event body 
	echo ' '.$getPID.'<br>';
	echo 'PID position: '.strpos($getPID, 'ID:').'<br>';
	$pidPosition = strpos($getPID, 'ID:');
	echo ' PID selection: '.substr($getPID, $pidPosition+1,$pidPosition+10).'<br>';  
	$extractPID= substr($getPID, $pidPosition+1,$pidPosition+10); 
	$pidArray = explode(' ', $extractPID); 
	$pid = substr($pidArray[1], 0, 2); 
	echo ' PID: '. $pid.'<br>'; 
	
	//Get name and other package into data base for event PID 
	echo 'Counter: '.$counter1.'<br>'; 
	global $wpdb;
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	$lessoncount = $database ->lesson_package; 
	$descriptionofEvent = $event->getDescription(); 
		
	//Datetime start and end of event 
	$scheduleEventDate = $event->start->dateTime; 
	$scheduleEventDateEnd = $event->end->dateTime; 	
	echo 'Event start: ' . $scheduleEventDate . '<br>'; 
	echo 'Event start type: ' . gettype($scheduleEventDate) . '<br>';
	echo 'Event end: ' . $scheduleEventDateEnd . '<br>'; 	
		

//Creates recurring event and placeholder event 
if (strpos($event->getSummary(), 'created event') == false){
	echo 'Event doesnt have the Event Created in discr.' . '<br>'; 
	echo  ' '. $event->getSummary() . '<br>';
  
	//Replacesment event 
	  $event1 = new Google_Service_Calendar_Event(array(
  'summary' => $event->getSummary() . ' created event',  //Set this 
  'location' => '800 Howard St., San Francisco, CA 94103', //Set this to HHF 
  'description' => $event->getDescription() . ' created event',
  'start' => array(
    'dateTime' => $event->start->dateTime, //Make date input here 
    'timeZone' => 'America/Los_Angeles',
  ),
  'end' => array(
    'dateTime' => $event->end->dateTime, //Make end date input here 
    'timeZone' => 'America/Los_Angeles',
  ),
  'recurrence' => array(
    'RRULE:FREQ=WEEKLY;COUNT='. $lessoncount 
  ),
  
  
  )); 
	
	echo 'Entered new event. '. '<br>'; 
	//Off sets weeks count for placeholder 
	  if ($lessoncount == '4'){
		$placeholderweeks = '28'; 
		$placeholderrecurringevents = '6'; //Weeks 
		$lastlessonoffset = '21'; 
	} elseif ( $lessoncount == '8'){ // Lesson is 8 count 
		$placeholderweeks = '56'; 
		$placeholderrecurringevents = '11';  //Weeks 
	} elseif ( $lessoncount == '1'){
		$placeholderweeks = '7'; 
		$placeholderrecurringevents = '2';  //Weeks 

	
	//Sets offsert for placeholder 
    $placeholderformating = new DateTime($scheduleEventDate);
	$placeholderformating->add(new DateInterval('P' . $placeholderweeks .'D'));
	echo 'Placeholder start: ' . $placeholderformating->format('Y-m-d\TH:i:s') . "\n";
	$placeholderdate = $placeholderformating->format('Y-m-d\TH:i:s'); 
	
	
    $placeholderformatend = new DateTime($scheduleEventDateEnd);
	$placeholderformatend->add(new DateInterval('P' .$placeholderweeks .'D'));
	echo $placeholderformatend->format('Y-m-d\TH:i:s') . "\n";
	$placeholderdateend = $placeholderformatend->format('Y-m-d\TH:i:s'); 

	$lastlesson = new DateTime($scheduleEventDate);
	$lastlesson->add(new DateInterval('P' . $placeholderweeks .'D'));
	echo 'Placeholder start: ' . $lastlesson->format('Y-m-d\TH:i:s') . "\n";
	$lastlessonoffset = $lastlesson->format('Y-m-d\TH:i:s'); 
	
		  
		  
 	/**
	global $wpdb;
	$table = 'wp_participants_database';
	$cell_value = 'last_lesson'; 
	$fld = $lastlessonoffset; 
	$obid = $pid; 
	return $wpdb->update($table, $fld, $obid);
	**/
	
		  
	//Enters last lesson into DB
    global $wpdb;
    $table = 'wp_participants_database';
	$fld = array('last_lesson' => $lastlessonoffset);
	$obid = array('id' => $pid); 
	$wpdb->update($table, $fld, $obid); 
	echo 'Last lesson date: '. $lastlessonoffset . '<br>';

	
	//Sets up placeholder event details 
   $event2 = new Google_Service_Calendar_Event(array(
  'summary' => 'Placeholder event ' . $event->getSummary() . ' ' . 'created event',  //Set this 
  'location' => '800 Howard St., San Francisco, CA 94103', //Set this to HHF 
  'description' => 'A chance to hear more about Google\'s developer products.',
  'start' => array(
    'dateTime' => $placeholderdate, //Make date input here 
    'timeZone' => 'America/Los_Angeles',
  ),
  'end' => array(
    'dateTime' => $placeholderdateend, //Make end date input here 
    'timeZone' => 'America/Los_Angeles',
  ),
  'recurrence' => array(
    'RRULE:FREQ=WEEKLY;COUNT='. $placeholderrecurringevents //How many times placeholder event will recurr 
  ),
  
  
  )
);

	//Creates recurring events 
	$eventCreate1 = $service->events->insert($calID, $event1);
	printf('Event created: %s\n', $eventCreate1->htmlLink);
	echo 'Event ID: ' . $eventCreate1->id; //Gets ID to place in the database 
	
	//Enters event ID for editing later 
	$table = 'wp_participants_database';
	$fld = array('root_event_id' => $eventCreate1->id);
	$obid = array('id' => $pid); 
	$wpdb->update($table, $fld, $obid); 
	
	
	//Creates placeholder evnets 
    $eventCreate2 = $service->events->insert($calID, $event2);
    printf('Event created: %s\n', $eventCreate2->htmlLink);	
	

} 

	
  }

}
}
//End recreateEvent method 
 
	
	
	
	
	
	
//Remove placeholder 
//Removes placeholder for Entering new if client cancels lesson 
public function removePlaceholder(){
	
	global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	
	  
	

	$calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	
	
	
// Start date from where to get the events
	$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-2 days'));
	$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+ 30 days'));
  

	//Get event details
	$events = $service->events->listEvents($calID, $optParams );
	echo 'Events count: '. count($events)."<br>"; 
	$eventCount = count($events); 
	 $counter1 = 0 ; 


	//Iterates through all events 
	for ($counter1 =  0; $counter1 <= $eventCount;  $counter1++){
	$event = $events[$counter1]; 
	echo '<br>'; 
    echo ' '. $event->getSummary();
	echo ' '. $event->getId();
	$eventID = $event->getId();
	$getPID = $event->getDescription();
	echo ' '.$getPID.'<br>';
	echo 'PID position: '.strpos($getPID, 'ID:').'<br>';
	$pidPosition = strpos($getPID, 'ID:');
	//echo ' PID selection: '.substr($getPID, 41+4,41-37).'<br>'; 
	echo ' PID selection: '.substr($getPID, $pidPosition+1,$pidPosition+10).'<br>';  
	$extractPID= substr($getPID, $pidPosition+1,$pidPosition+10); 
	$pidArray = explode(' ', $extractPID); 
	$pid = substr($pidArray[1], 0, 2); 
	echo ' PID: '. $pid.'<br>'; 
	
	
	global $wpdb;
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	
	if ($database->lesson_package == $database->lesson_completed){
	  echo 'Lessons completed equal lesson package.' ; 
	} else {
		echo 'Lessons are not completed'; 
	}
}

}

//Sends text reminder 
public function renewReminder(){
	
	
	$counter1 = 0; 
	 global $wpdb;
	$wpdb->get_results('SELECT * FROM wp_participants_database'); 
	$AllValues = 	$wpdb->get_results('SELECT * FROM wp_participants_database'); 
	$idValues = $AllValues->id; 
	echo 'Id values: '. $idValues . '<br>'; 
	global $post, $wpdb;
	
	
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM wp_participants_database");
	
	$addday = new DateTime('now');
	$addday->add(new DateInterval('P1D'));
	$addday->setTimezone(new DateTimeZone('America/Los_Angeles'));
	$addday->format('Y-m-d\TH:i:s');
	$adddayreminder = $addday->format('Y-m-d\TH'); 
	echo 'Now '. $adddayreminder . '<br>'; 
	
	foreach ( $AllValues as $value){
		echo 'Value: '. $value->id . '<br>'; 
		echo 'Last lesson: '. $value->last_lesson . '<br>'; 
		if ($adddayreminder >  $value->last_lesson  && $value->last_lesson_reminder_sent == false && $value->last_lesson != null){
			echo 'Last lesson triggered '.'<br>'; 
			
			
		//Marks last lesson reminder sent  
		global $wpdb;
		$table1 = 'wp_participants_database';
		$lastLessonReminderSent = true; 
		$fld1 = array('last_lesson_reminder_sent' => $lastLessonReminderSent);
		$pid1 = $value->id; 
		$obid1 = array('id' => $pid1); 
		 $wpdb->update($table1, $fld1, $obid1); 
		echo 'Last lesson date: '. $lastlessonoffset . '<br>';
			

	

		if($value->package == '1'){
			$paymentlink = 'http://farmfunfordogssd.com/index.php/1-lessons/?id='.$pid1.'&lesson_package=1'; 
			echo 'Lesson package in if: '. $value->package . '<br>';
		}elseif ($value->package == '4'){
			$paymentlink = 'http://farmfunfordogssd.com/index.php/4-lessons/?id='.$pid1.'&lesson_package=4'; 
			echo 'Lesson package in if: '. $value->package . '<br>';
		}elseif ($value->package == '8'){
			$paymentlink = 'http://farmfunfordogssd.com/index.php/8-lessons/?id='.$pid1.'&lesson_package=8'; 
			echo 'Lesson package in if: '. $value->package . '<br>';
		}

		$args = array( 
		'number_to' => $value->phone,
		'message' => $paymentlink); 
				
	twl_send_sms( $args );	
	echo 'Lesson package: '. $value->package . '<br>'; 
	echo 'SMS sent.'; 


		}

	}

	

	
}
	
//Adds recurrence when client cancels 
public function addRecurrence(){
	
	
	
	global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	$customerValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	
	
	$eventID = $customerValues->root_event_id; 
	$placeholderID = $customerValues->placeholder_id;
	echo 'Root event id: '. $eventID . '<br>'; 
	echo 'Root event id: '. $placeholderID . '<br>'; 
	global $post, $wpdb;
	
	
 

	//$calendarList = $service->calendarList->listCalendarList();
   // echo 'from Print class ' . '<br>'; 
	//$arrayCal = $calendarList->getItems();
	//echo 'Array cal: ' . $arrayCal; 
	//echo 'Array with items: ' . reset($arrayCal);
	//---------------------------------------------	------------------------------------
	
	$counter1 = 0; 

  
//Get root event details 
	//$event = $service->events->get($calID, '6bt77lf7cnuk323krlt5saq31v' );
	$event = $service->events->get($calID, $eventID);
	//$event = $service->events->get($calID, $placeholderID);
   
	

	$currentDescr = $event->getDescription(); 
	$getRecur = $event->getRecurrence(); 
	$jsonRecur = json_encode($getRecur); 
	$decodejsonRecur = json_decode($jsonRecur); 
	//$recurringExtract = substr ( $decodejsonRecur[0], 24, strlen($decodejsonRecur[0]) ); 
	$recurringPos = strpos($decodejsonRecur[0], 'COUNT'); 
	$recurringGetCount = substr($decodejsonRecur[0], $recurringPos, strlen($decodejsonRecur[0])); 
	$recurringGetNum = substr($recurringGetCount, 6, strlen($recurringGetCount)); 
	
	//Up recurring event by one 
	$recurringAdd = intval($recurringGetNum) + 1; 
	$recurringToStr = strval($recurringAdd); 
	$event->setRecurrence(array('RRULE:FREQ=WEEKLY;COUNT=' . $recurringToStr));
	$lessonNumber = 0; // what ever lesson number we are on 
	$completeDate = 'current Datetime'; 
	$event->setDescription($currentDescr . '<br>'.'Lesson '. $lessonNumber .' : '  . $completeDate . ' complete'); 
	
	echo '$serviceEvent sumary: ' . $event->getSummary() . '<br>';
	echo '$serviceEvent description: ' . $event->getDescription() . '<br>'; 
	echo '$serviceEvent recurrence: ' .  $decodejsonRecur[0]  . '<br>'; 
	echo '$serviceEvent recurring count : ' .  $recurringGetCount . '<br>'; 
	echo '$serviceEvent recurring count : ' .  $recurringGetNum . '<br>';
	
	
	
//Placeholder event 
	echo '<br>';

	echo '<br>';

	echo '<br>';
	
	echo 'Placeholder Info <br>'; 
$event2= $service->events->get($calID, $placeholderID);

	

	$currentDescr2  = $event2->getDescription(); 
	$getRecur2 = $event2->getRecurrence(); 
	$jsonRecur2 = json_encode($getRecur2); 
	$decodejsonRecur2 = json_decode($jsonRecur2); 
	//$recurringExtract = substr ( $decodejsonRecur[0], 24, strlen($decodejsonRecur[0]) ); 
	$recurringPos2 = strpos($decodejsonRecur2[0], 'COUNT'); 
	$recurringGetCount2 = substr($decodejsonRecur2[0], $recurringPos2, strlen($decodejsonRecur2[0])); 
	$recurringGetNum2 = substr($recurringGetCount2, 6, strlen($recurringGetCount2)); 
	
	//Up recurring event by one 
	$recurringAdd2 = intval($recurringGetNum2) + 1; 
	$recurringToStr2 = strval($recurringAdd2); 
	//$event2->setRecurrence(array('RRULE:FREQ=WEEKLY;COUNT=' . $recurringToStr2));
	$lessonNumber2 = 0; // what ever lesson number we are on 
	$completeDate2 = 'current Datetime'; 
	$event2->setDescription($currentDescr2 . '<br>'.'Lesson '. $lessonNumber2 .' : '  . $completeDate2 . ' complete');
	$startDate = $event2->getStart(); 
	$startDateFormat = $startDate->dateTime; 
	$endDate = $event2->getEnd(); 
	$EndDateFormat = $endDate->dateTime; 
	
	
	echo 'Start datetime: '. $startDateFormat . '<br>';
	$startDateFormat2 = strtotime($startDateFormat); 
	//$newDateString = DateTime::createFromFormat('Y-m-d\TH:i:s', $startDateFormat)->format('Y-m-d'); 
	echo 'new date sting: '. $startDateFormat2. '<br>'; 
		//$newDateString = new DateTime($startDate);
		//echo 'Lesson: '. $newDateString->format('Y-m-d') . '<br>'; 
		
		$date = new DateTime();
		echo $date->format('U = Y-m-d H:i:s') . "<br>";

		$date->setTimestamp($startDateFormat2);
		echo $date->format('U = Y-m-d H:i:s') . "<br>";
		
		//$newDateString = DateTime::createFromFormat('Y-m-d\TH:i:s', $startDateFormat2)->format('Y-m-d');
		//echo 'new date string: '.$newDateString.'<br>'; 
		$date->setTimezone(new DateTimeZone('America/Los_Angeles'));
		$lessonSubTime = $date->add(new DateInterval('P' . '1' .'W')); 
		
		//echo 'Adding week: '. $lessonSubTime->format('Y-m-d') . '<br>'; 
		$lessonSubDay = $lessonSubTime->format('Y-m-d\TH:i:sP'); 
		echo 'Adding week: '. $lessonSubDay . '<br>'; 
		//echo 'Google date format: ' .date('Y-m-d\TH:i:s', strtotime($lessonSubTime. ' - '. '4' .' days')). '<rb>'; 
		//$googleformatstartdate = date('Y-m-d\TH:i:s', strtotime($dateEnd. ' - '. '4' .' days')); 
		
 		$start2 = new Google_Service_Calendar_EventDateTime();
		
		//$start2->setDateTime('2018-03-29T10:00:00.000-05:00');
		echo 'For Google start datre: '. $lessonSubDay.'<br>'; 
		$start2->setDateTime($lessonSubDay);
		$start2->setTimeZone('America/Los_Angeles'); 
	  	 
		$event2->setStart($start2);
	
	
	
	
	
	
	
		echo 'End datetime: '. $EndDateFormat . '<br>';
		$EndDateFormat2 = strtotime($EndDateFormat); 
		//$newDateString = DateTime::createFromFormat('Y-m-d\TH:i:s', $startDateFormat)->format('Y-m-d'); 
		echo 'new End date unix : '. $EndDateFormat2. '<br>'; 
		//$newDateString = new DateTime($startDate);
		//echo 'Lesson: '. $newDateString->format('Y-m-d') . '<br>'; 
		
		$dateEnd = new DateTime();
		//echo $dateEnd->format('U = Y-m-d H:i:s') . "<br>";

		$dateEnd->setTimestamp($EndDateFormat2);
		//echo $dateEnd->format('U = Y-m-d H:i:s') . "<br>";
		
		//$newDateString = DateTime::createFromFormat('Y-m-d\TH:i:s', $startDateFormat2)->format('Y-m-d');
		//echo 'new date string: '.$newDateString.'<br>'; 
		$dateEnd->add(new DateInterval('P' . '1' .'W')); 
		echo 'Adding week: '. $dateEnd->format('Y-m-d\TH:i:sP') . '<br>'; 
		$dateEnd->setTimezone(new DateTimeZone('America/Los_Angeles'));
    	$lessonEndAddDay = $dateEnd->format('Y-m-d\TH:i:sP'); 
		//$lessonEndAddDay = '2019-07-31T10:30:00-07:00';
		$end = new Google_Service_Calendar_EventDateTime();
		//$start2->setDateTime('2018-03-29T10:00:00.000-05:00');
		echo 'For Google END datre: '. $lessonEndAddDay.'<br>'; 
		
		$end->setDateTime($lessonEndAddDay);
		$end->setTimezone('America/Los_Angeles');
		//$event2->setTimeMax(date("c", strtotime(date('Y-m-d H:i:s').'+ 100 days'))); 
		echo 'End time: '. $lessonEndAddDay . '<br>'; 

		$event2->setEnd($end);
		
		//$event2->setSequence($event->getSequence() + 1);
	
	//echo 'Start date: '. $lessonSubDay .'<br>'; 
	
	
	echo '$serviceEvent sumary: ' . $event2->getSummary() . '<br>';
	echo '$serviceEvent description: ' . $event2->getDescription() . '<br>'; 
	echo '$serviceEvent recurrence: ' .  $decodejsonRecur2[0]  . '<br>'; 
	echo '$serviceEvent recurring count : ' .  $recurringGetCount2 . '<br>'; 
	echo '$serviceEvent recurring count : ' .  $recurringGetNum2 . '<br>';
	
	
	
	//Update event 
	//$service->events->update($calID, '6bt77lf7cnuk323krlt5saq31v', $event);
	//$service->events->update($calID, $eventID, $event);
	$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-14 days'));
	$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+ 100 days'));
  
	$service->events->update($calID, $placeholderID, $event2);
	echo 'Placeholder updated. <br>'; 
}
	
	
	
	
public function lessonDatesInSQL(){
	
	
	
	global $wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	$customerValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	
	
	$eventID = $customerValues->root_event_id; 
	$placeholderID = $customerValues->placeholder_id;
	echo 'Root event id: '. $eventID . '<br>'; 
	echo 'Root event id: '. $placeholderID . '<br>'; 
	global $post, $wpdb;
	
	 
	// Check the current month

	$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-2 days'));
	$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+ 30 days'));
  
  
	//Get event details
	$events = $service->events->listEvents($calID, $optParams );
	echo 'Events count: '. count($events)."<br>"; 
	$eventCount = count($events); 
	 $counter1 = 0 ; 


	//while($counter1 <= $eventCount) {
    //foreach ($events->getItems() as $event) {
	for ($counter1 =  0; $counter1 <= $eventCount;  $counter1++){
	$event = $events[$counter1]; 
	
	echo '<br>'; 
    echo ' '. $event->getSummary();
	echo ' '. $event->getId();
	$eventID = $event->getId();
	$getPID = $event->getDescription();
	echo ' '.$getPID.'<br>';
	echo 'PID position: '.strpos($getPID, 'ID:').'<br>';
	$pidPosition = strpos($getPID, 'ID:');
	//echo ' PID selection: '.substr($getPID, 41+4,41-37).'<br>'; 
	echo ' PID selection: '.substr($getPID, $pidPosition+1,$pidPosition+10).'<br>';  
	$extractPID= substr($getPID, $pidPosition+1,$pidPosition+10); 
	$pidArray = explode(' ', $extractPID); 
	$pid = substr($pidArray[1], 0, 2); 
	echo ' PID: '. $pid.'<br>'; 
	 
	echo 'Counter: '.$counter1.'<br>'; 
	global $wpdb;
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	$lessoncount = $database ->lesson_package; 
	$descriptionofEvent = $event->getDescription(); 
		
	$scheduleEventDate = $event->start->dateTime; 
	$scheduleEventDateEnd = $event->end->dateTime; 	
	echo 'Event start: ' . $scheduleEventDate . '<br>'; 
	$lessonArray = array(); 
	$eventCount2 = 5; 
	for ($counter2 =  0; $counter2 <= $eventCount2;  $counter2++){
		$time = date('Y-m-d\TH:i:s', strtotime($scheduleEventDate. ' + '. $counter2 .' weeks'));
		echo 'Time: ' . $time . '<br>'; 
		$lessonArray['Lesson '.$counter2] = $time; 	
	
	}

	echo 'Lesson array: ' . json_encode($lessonArray).'<br>'; 
	global $wpdb;
	$table = 'wp_participants_database';
	$fld = array('lesson_dates' => json_encode($lessonArray));
	$obid = array('id' => $pid); 
	$wpdb->update($table, $fld, $obid); 
	echo 'Last lesson date: '. $lastlessonoffset . '<br>';

	}
	
	
	
}
	
//Marks when lesson is completed 
public function lessonComplete(){
	
	global $post,$wpdb;
	$pid = 76; 
	$database = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $database ->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $database->lesson_package; 
	$instructor = $database->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	$customerValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	
	
	$eventID = $customerValues->root_event_id; 
	$placeholderID = $customerValues->placeholder_id;
	echo 'Root event id: '. $eventID . '<br>'; 
	echo 'Root event id: '. $placeholderID . '<br>'; 
	global $post, $wpdb;
	
	
	//Old gcal header 
	//-------------------------------------------------------------------------------------
		//global $wpdb;
		$pid = 76; 
		$AllValues = 	$wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'"); 
	$idValues = $AllValues->id; 
 
	
	
	$lessonArray = $AllValues->lesson_dates;
	$noMoreQuote = str_replace('"', '', $lessonArray); 
	$noMoreboxleft = str_replace('{', '',  $noMoreQuote); 
	$noMoreboxright = str_replace('}', '',  $noMoreboxleft);
	$pairs = explode(',', $noMoreboxright);
	$data = array();
	foreach ($pairs as $pair) {
    	list($key, $value) = explode(':', $pair);
    	$data[$key] = $value;
	}
	echo "Lesson values : " . var_dump($data) . '<br>' ;
	echo "Lesson values : " . $data['Lesson 0'] . '<br>' ;
	 foreach ($data as $key => $value ) {
			echo "Key  : " . $key . ": ". $value . "<br>" ;
	
		 $newDateString = DateTime::createFromFormat('Y-m-d\TH', $value)->format('Y-m-d\TH');
		 
		echo 'Converting to now datetime: '.$newDateString . "<br>" ;

		$lessonDay = new DateTime('now');
		//$onedayadd->add(new DateInterval('P1D'));
		$lessonDay->setTimezone(new DateTimeZone('America/Los_Angeles'));
		$lessonDay->format('Y-m-d\TH:i:s');
		$lessonDayFinal = $lessonDay->format('Y-m-d\TH'); 
 		echo 'Converting to datetime: '.$lessonDayFinal . "<br>" ;
		 
		if ($newDateString == $lessonDayFinal) {
			echo 'Reminder fired '. $key . ': ' . $value . "<br>" ;
			//send 1 day reminder from here 
			$lessonComplete = $AllValues->lesson_completed; 
			
			$eventID = $AllValues->root_event_id; 
			echo 'Current lesson: ' . $lessonComplete . '<br>'; 
			$lessonComplete = $lessonComplete + 1; 
			echo 'Lesson complete: ' . $lessonComplete . '<br>'; 
			
			
			$table = 'wp_participants_database';
			$fld = array('lesson_completed' => $lessonComplete, 'last_cancel_time'=> '');
			$obid = array('id' => $pid); 
			//$wpdb->update($table, $fld, $obid); 
			
			$event = $service->events->get($calID, $eventID);
			
			echo 'Event details: '. $event->getDescription() . '<br>';
			echo 'Summary after insert: ' . $event->htmlLink .'<br>';
			$currentDescr = $event->getDescription(); 
			$event->setDescription($currentDescr . '<br>'.'Lesson '. $lessonComplete .' : '  . $newDateString . 			 ' complete'); 
			echo 'Summary: ' . $event->getSummary() .'<br.'; 
			$currentSummary = $event->getSummary(); 
			$event->setSummary($currentSummary . 'look here is an edit');
			
			
			
			//$currentDescr . '<br>'.'Lesson '. $lessonNumber .' : '  . $completeDate . ' complete'
			$service->events->update($calID, $eventID, $event);
			
			
		}else {
			echo 'Reminder time is not equal to lessons time' . "<br>"; 
		} 
 		



	}

	
	/**
	

	$calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	//echo 'Array with items: ' . reset($arrayCal);
	
	
	// Check the current month
//$start = date('Y-m-01');
//$end = date('Y-m-').date("t");

//$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-3 days'));
//$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'-2 days'));

	
// Start date from where to get the events

	
	//$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-2 days'));
	//$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+ 30 days'));
  
  
//Get event details
	$event = $service->events->get($calID, '6bt77lf7cnuk323krlt5saq31v' );

	$currentDescr = $event->getDescription(); 
	$getRecur = $event->getRecurrence(); 
	$jsonRecur = json_encode($getRecur); 
	$decodejsonRecur = json_decode($jsonRecur); 
	//$recurringExtract = substr ( $decodejsonRecur[0], 24, strlen($decodejsonRecur[0]) ); 
	$recurringPos = strpos($decodejsonRecur[0], 'COUNT'); 
	$recurringGetCount = substr($decodejsonRecur[0], $recurringPos, strlen($decodejsonRecur[0])); 
	$recurringGetNum = substr($recurringGetCount, 6, strlen($recurringGetCount)); 
	//Up recurring event by one 
	$recurringAdd = intval($recurringGetNum) + 1; 
	$recurringToStr = strval($recurringAdd); 
	$event->setRecurrence(array('RRULE:FREQ=WEEKLY;COUNT=' . $recurringToStr));
	$lessonNumber = 0; // what ever lesson number we are on 
	$completeDate = 'current Datetime'; 
	$event->setDescription($currentDescr . '<br>'.'Lesson '. $lessonNumber .' : '  . $completeDate . ' complete'); 
	
	echo '$serviceEvent sumary: ' . $event->getSummary() . '<br>';
	echo '$serviceEvent description: ' . $event->getDescription() . '<br>'; 
	echo '$serviceEvent recurrence: ' .  $decodejsonRecur[0]  . '<br>'; 
	echo '$serviceEvent recurring count : ' .  $recurringGetCount . '<br>'; 
	echo '$serviceEvent recurring count : ' .  $recurringGetNum . '<br>';
	

	
	//Update event 
	$service->events->update($calID, '6bt77lf7cnuk323krlt5saq31v', $event);
  	**/ 
}
	
	
	
public function lessonReminder(){
	
	global $post,$wpdb;
	$pid = 76; 
	$AllValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $AllValues->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $AllValues->lesson_package; 
	$instructor = $AllValues->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	$customerValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	
	
	$eventID = $customerValues->root_event_id; 
	$placeholderID = $customerValues->placeholder_id;
	echo 'Root event id: '. $eventID . '<br>'; 
	echo 'Root event id: '. $placeholderID . '<br>'; 
	
	//Adapting vars to new header 
	//-------------------------------------------------
	
	
	//--------------------------------------------------
	
	
 
	$idValues = $AllValues->id; 
	echo 'Id values: '. $idValues . '<br>'; 
	$counter1 = 0; 



	
	
	$lessonArray = $AllValues->lesson_dates;
	$noMoreQuote = str_replace('"', '', $lessonArray); 
	$noMoreboxleft = str_replace('{', '',  $noMoreQuote); 
	$noMoreboxright = str_replace('}', '',  $noMoreboxleft);
	$pairs = explode(',', $noMoreboxright);
	$data = array();
	foreach ($pairs as $pair) {
    	list($key, $value) = explode(':', $pair);
    	$data[$key] = $value;
	}
	echo 'Data: ' . json_encode($data). '<br>' ; 
		 
		$now = new DateTime('now');
		$now->setTimezone(new DateTimeZone('America/Los_Angeles'));
		$now->format('Y-m-d\TH');
		$nowFinal = $now->format('Y-m-d\TH'); 
		$nowFinalwohour = $now->format('Y-m-d');
	
	
	
	
    	 //$newDateString = DateTime::createFromFormat('Y-m-d\TH', $date)->format('Y-m-d\TH');

	
//		$newDateString = DateTime::createFromFormat('Y-m-d\TH', $value)->format('Y-m-d\TH');
foreach ($data as $key => $value){
		
		$newDateString = DateTime::createFromFormat('Y-m-d\TH', $value)->format('Y-m-d'); 
		$newDateString2 = DateTime::createFromFormat('Y-m-d\TH', $value); 
		/**$dressTime = mktime(0, 0, 0, $dateParts[1], $dateParts[0], $dateParts[2]);
		if($newDateString > $nowFinal){
			echo "Date: ". $value . '<br>'; 
			echo "Now: ". $nowFinal . '<br>'; 
			//echo "Now as stirng : ". strval($newDateString) . '<br>'; **/ 
	//!!-3 day reminder here 	
	$nowFinal = $now->format('Y-m-d\TH'); 
	$nowFinalwohour = $now->format('Y-m-d');
	$createreminder3days = new DateTime($scheduleEventDate);
	$now->add(new DateInterval('P' . '3' .'D'));
	//echo 'Placeholder start: ' . $lastlesson->format('Y-m-d\TH:i:s') . "\n";
	
	$createreminder3days = $now->format('Y-m-d'); 
	echo 'Add 3: ' . $createreminder3days . '<br>'; 
	
			
			echo "Date as string$newDateString: ". strval($newDateString) . '<br>'; 
			echo "Date as string: ". strval($nowFinalwohour) . '<br>'; 
			

			
			//3 day reminder 
			if (strval($newDateString) == strval($createreminder3days)){
				echo 'Value and now are equal <br>'; 
				
				
				$reminderMsg = 'Reminder for your lesson on ' . $value . '. If you need to cancel please follow this link http://farmfunfordogssd.com/index.php/lesson-cancel/?'. 'id='.$pid. '&'.'lessondate='. $newDateString2->format('Y-m-d\TH:i'); 
			$args = array( 
				'number_to' => $AllValues->phone,
				'message' => $reminderMsg); 

			twl_send_sms( $args );	
			echo 'Lesson package: '. $value->package . '<br>'; 
			echo 'SMS sent.'; 
				
			} else {
				echo 'Values are not equal <br>'; 
			}
	
			$now = new DateTime('now');
			$now->setTimezone(new DateTimeZone('America/Los_Angeles'));
			$now->add(new DateInterval('P' . '1' .'D'));
			$createreminder1day = $now->format('Y-m-d'); 
			echo '1 day reminder: ' .  $createreminder1day . '<br>'; 
	
			//1 day reminder 
			if (strval($newDateString) == strval($createreminder1day)){
				echo 'Value and now are equal <br>'; 
				
				
				$reminderMsg = 'Reminder for your lesson on ' . $value . '. If you need to cancel please follow this link http://farmfunfordogssd.com/index.php/lesson-cancel/?'. 'id='.$pid. '&'.'lessondate='. $newDateString2->format('Y-m-d\TH:i'); 
			$args = array( 
				'number_to' => $AllValues->phone,
				'message' => $reminderMsg); 

			twl_send_sms( $args );	
			echo 'Lesson package: '. $value->package . '<br>'; 
			echo 'SMS sent.'; 
				
			} else {
				echo 'Values are not equal <br>'; 
			}
			
			/**$reminderMsg = 'Reminder for your lget_participantesson on ' . $value . '. If you need to cancel please us this link.'; 
			$args = array( 
				'number_to' => $AllValues->phone,
				'message' => $reminderMsg); 

			twl_send_sms( $args );	
			echo 'Lesson package: '. $value->package . '<br>'; 
			echo 'SMS sent.'; **/

			//break;
		}
	}
	
	
	
	
	
	
	
public function cancel(){

	
	global $post,$wpdb;
	$pid = 76; 
	$AllValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	echo 'DB name: ' . $database ->first_name.'<br>'; 
	echo 'Lesson package: ' . $AllValues->lesson_package.'<br>'; 
	echo 'Instructor ID : ' . $instructor.'<br>';
	$lessoncount = $AllValues->lesson_package; 
	$instructor = $AllValues->instructor_id; 
	
	$instructorDatabase = $wpdb->get_row( "SELECT * FROM `wp_instructors` WHERE `id` = " . $instructor);
	$credLink = $instructorDatabase->credential_link; 
	$calID = $instructorDatabase->calID; 
	echo 'Credlink: ' .$credLink . '<br>'; 
	echo 'CalID for from database:' . $calendarId . '<br>'; 
 
    $client = new Google_Client();
	$client->setAuthConfig($credLink);
	$client->setScopes("https://www.googleapis.com/auth/calendar");
	$service = new Google_Service_Calendar($client);
	
    $calendarList = $service->calendarList->listCalendarList();
    echo 'from Print class ' . '<br>'; 
	$arrayCal = $calendarList->getItems();
	echo 'Array cal: ' . $arrayCal; 
	$customerValues = $wpdb->get_row( "SELECT * FROM `wp_participants_database` WHERE `id` = " . "'" . $pid . "'");
	
	
	$eventID = $customerValues->root_event_id; 
	$placeholderID = $customerValues->placeholder_id;
	echo 'Root event id: '. $eventID . '<br>'; 
	echo 'Root event id: '. $placeholderID . '<br>'; 
	
 
			$counter1 = 0; 
		$idValues = $AllValues->id; 


	
	
	
	$lessonArray = $AllValues->lesson_dates;
	$noMoreQuote = str_replace('"', '', $lessonArray); 
	$noMoreboxleft = str_replace('{', '',  $noMoreQuote); 
	$noMoreboxright = str_replace('}', '',  $noMoreboxleft);
	$pairs = explode(',', $noMoreboxright);
	$data = array();
	
	foreach ($pairs as $pair) {
    	list($key, $value) = explode(':', $pair);
    	$data[$key] = $value;
	}
	//echo 'Data: ' . json_encode($data). '<br>' ; 
		 
		$now = new DateTime('now');
		$now->setTimezone(new DateTimeZone('America/Los_Angeles'));
		$now->format('Y-m-d\TH');
		$nowFinal = $now->format('Y-m-d\TH'); 
		$nowFinalwohour = $now->format('Y-m-d');
		$nowHour = $now->format('H'); 
	
		
	
	
    	 //$newDateString = DateTime::createFromFormat('Y-m-d\TH', $date)->format('Y-m-d\TH');

	
	//		$newDateString = DateTime::createFromFormat('Y-m-d\TH', $value)->format('Y-m-d\TH');
	foreach ($data as $key => $value){
		
		$newDateString = DateTime::createFromFormat('Y-m-d\TH', $value); 
		
		//echo 'Lesson: '. $newDateString->format('Y-m-d') . '<br>'; 
		$lessonSubTime = $newDateString->sub(new DateInterval('P' . '1' .'D')); 
		$lessonSubDay = $lessonSubTime->format('Y-m-d'); 
		//echo 'Lesson sub day: ' . $lessonSubDay  . '<br>'; 
		$lessonAddadd19hours = $newDateString->add(new DateInterval('PT' . '19' .'H')); 
		//echo 'Lesson add 19 hrs: ' . $lessonAddadd19hours->format('Y-m-d\TH') . '<br>'; 
		//echo 'Now hour : ' . $nowHour . '<br>'; 
	
		if ($newDateString->format('Y-m-d') > $nowFinalwohour ){
			echo 'Value: '. $newDateString->format('Y-m-d') . '<br>'; 
			$nextLessonDate = $newDateString->format('Y-m-d'); 
			break; 
		}

	 
		} //end of loop 
		//echo 'Next lesson date: ' .  $nextLessonDate . '<br>';
		echo 'last cancel date : ' . $AllValues->last_cancel_time. '<br>';
		echo 'last canceldate length : ' .  strlen($AllValues->last_cancel_time) . '<br>';
		$lastTimeTime = $AllValues->last_cancel_time; 
		echo 'last canceldate length : ' .  empty($AllValues->last_cancel_time) . '<br>';
		

		//
		if ($nowFinalwohour == $lessonSubDay && intval($nowHour) > 19 || $AllValues->last_cancel_time != '' ){ //Too late to cancel 19:00 day of lesson
			echo 'Too late to cancel next lesson. <br>'; 	
			
		} else {
			echo 'Not to late to cancel next lesson. <br>'; 
			self::addRecurrence();
			
			
			$table = 'wp_participants_database';
			$fld = array('last_cancel_time' => $nowFinal); //-create datetime here 
			$obid = array('id' => $pid); 
			$wpdb->update($table, $fld, $obid); 
			
		}
		
	}
	
	
}

	


	



add_action('widgets_init', function(){
        register_widget('My_GCal_Widget');
	

});
 
