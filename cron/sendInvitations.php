<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set("SMTP", "mail.diamta.com");
	ini_set('smtp_port', 465);
	ini_set("sendmail_from", "info@diamta.com");
	ini_set("auth_username", "mailuser@diamta.com");
	ini_set("auth_password", "_5]xE4h{Uc%c");

	$dbhost 	= 'localhost';
	$dbname 	= 'projects_db'; 	// 'diamta_projects';
	$dbusername = 'diamtaprojects'; // 'diamta_admin';
	$dbpassword = '!aA111111'; 		// '%!vjkYviaDuQ';

	// Get unsend invitations
	$table01 	= 'invitation';
	$table02	= 'project';
	$status		= 0;
	$todayTimestamp = strtotime( date("Y-m-d H:i:s")  );
	$todayDay 	= \date( 'Y-m-d H:i:s', $todayTimestamp);
	
	try {	// Get All not sent

		$conn = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword );
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$query = "SELECT id, receiver_email, receiver_name, attempts, project_id FROM $table01 WHERE status = :status AND date_sending <= :now";
		$stmt = $conn->prepare( $query );
		$stmt->execute( array( 
			'status' 	=> $status, 
			'now' 		=> $todayDay
		));

		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result01 = $stmt->fetchAll();
				
		$ln01 = count( $result01 );
echo $ln01;
		$project_id 	= $result01[ $ln01 -1 ][ 'project_id' ];


		// send only one alert and mark all the unread as alerted
		if( !count( $result01 ) )
			goto break_free_of_try01; // goto is ok ;)

		// get the project details
		$query02 = "SELECT title, date_created, description, budget, duration FROM $table02 WHERE id = :project_id";
		$stmt = $conn->prepare( $query02 );
		$stmt->execute( array( 
			'project_id' 	=> $project_id
		));

		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result02 = $stmt->fetchAll();

		if( !count( $result02 ) )
			goto break_free_of_try01; // goto is ok ;)
		
		// send the projrct and update the project database
		$ln02 = count( $result02 );
		
		$project_title	= $result02[ $ln02 -1 ][ 'title' ];
		$project_date	= $result02[ $ln02 -1 ][ 'date_created' ];
		$project_description	= $result02[ $ln02 -1 ][ 'description' ];
		$project_budget 		= $result02[ $ln02 -1 ][ 'budget' ];
		$project_duration		= $result02[ $ln02 -1 ][ 'duration' ];

		foreach( $result01 as $res ){
			$invitation_id 	= $res[ 'id' ];
			$receiver_name 	= $res[ 'receiver_name' ];
			$receiver_email	= $res[ 'receiver_email' ];
			$attempts 		= $res[ 'attempts' ];
			// send email
			sendMail( $receiver_email, $receiver_name, $project_id, $project_date, $project_budget, $project_description, $project_duration );
			
			// update invitation
			updateInvitation( $table01, $invitation_id, $attempts, $dbhost, $dbname, $dbusername, $dbpassword  );
		}
		$conn = null;

	}
	catch( PDOException $e ) {
		echo "Error: " . $e->getMessage();
	}
	break_free_of_try01:
	
// send mail
	function sendMail( $email, $user_name, $project_id, $project_date, $project_budget, $project_description, $project_duration ){
		$project_link 	= 'https://diamta.com/projects/public/index.php/projects/' . $project_id;
		$subject 		= 'You have received an invitation for work!';

		$text01			= 'Hello';
		
		$message 		= "<!DOCTYPE html><html><style>body {font-family: Arial, Helvetica, sans-serif;}</style><body><p><a href='https://www.diamta.com/?email=[email]&title=[title]' style='color:#ff5335;text-decoration:none;text-transform:none;' target='_blank'><img src='https://diamta.com/img/logo053.png' title='" . $subject . "' alt='Logo Diamta'> </a><br></p><h2>You have a new message</h2><p style='line-height: 30px;'>" . $text01 . ",<br> </p><p style='line-height: 30px; white-space: pre-wrap;'>\"\"</p><p>Please visite the link below to reply. <br><br><a href ='" . $project_link . "'>" . $project_link . "</a></p><p><br>The team</p></body></html>"; // html body';
		
		$headers  = "From: projects@diamta.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		
		//$responce = mail( $email, $subject, $message, $headers ); //  mail( to,subject,message,headers,parameters );
		 echo $message;
	}
	
	// update all table after alert sent to not send repetively
	function updateInvitation( $table, $invitation_id, $attempts, $dbhost, $dbname, $dbusername, $dbpassword  ){
		$status 	= 1;
		$attempts  += 1;
		// Get unread messages
		try {
			$conn = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword );
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$query = "UPDATE $table SET status = :status, attempts = :attempts WHERE id = :invitation_id";
			$stmt  = $conn->prepare( $query );
			$stmt->execute( array( 
				'status' 		=> $status,
				'attempts'		=> $attempts,
				'invitation_id'	=> $invitation_id
			));

			$conn = null;
		} 
		catch( PDOException $e ) {
			echo "Error: " . $e->getMessage();
		}
	}
?>
