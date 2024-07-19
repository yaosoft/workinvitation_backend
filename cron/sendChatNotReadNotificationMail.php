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
	$type 		= '';
	// Get unread messages
	$table01 	= 'chat_message';
	$table02 	= 'chat_file';
	$table03 	= 'users';
	$viewed		= 0;
	$alerted	= 0;
	$user_type 	= '';	// Client / Project manager
	// Alert for chat message
	
	try {	// Get All no viewed and not alerted
		$type	= 'chat';
		
		$conn = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword );
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$query = "SELECT id, receiver_id, project_id, chat_message FROM $table01 WHERE viewed = :viewed AND alerted = :alerted";
		$stmt = $conn->prepare( $query );
		$stmt->execute( array( 
			'viewed' => $viewed, 
			'alerted' => $alerted 
		));

		// set the resulting array to associative
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		
		// foreach( $result as $data ){
			
			// $user_id 		= $data[ 'receiver_id' ];
			// $project_id 	= $data[ 'project_id' ];
			// $item_id 		= $data[ 'id' ];
			// $chat_message 	= $data[ 'message'];
			// $query = "SELECT email FROM $table03 WHERE id = :user_id";
			// $stmt  = $conn->prepare( $query );
			// $stmt->execute( array( 'user_id' => $user_id ) );
		
			// // set the resulting array to associative
			// $stmt->setFetchMode(PDO::FETCH_ASSOC);
			// $result = $stmt->fetchAll();

// // echo  $result[0]["email"];
// // echo '-----------------------';

			// // send email
			// $email  = $result[0]["email"];
			// sendMail( $email, $project_id, $chat_message );
			
			// // update the database
			// updateAlert( $table01, $item_id, $project_id, $dbhost, $dbname, $dbusername, $dbpassword  );
		// }

		// send only one alert and mark all the unread as alerted
		if( !count( $result ) )
			goto break_free_of_try01; // goto is ok ;)

		$ln = count( $result );
		$user_id 		= $result[ $ln -1 ][ 'receiver_id' ];
		$project_id 	= $result[ $ln -1 ][ 'project_id' ];
		$item_id 		= $result[ $ln -1 ][ 'id' ];
		$chat_message 	= $result[ $ln -1 ][ 'chat_message' ];
		$query 			= "SELECT email, username, isadmin FROM $table03 WHERE id = :user_id";
		$stmt  			= $conn->prepare( $query );
		$stmt->execute( array( 'user_id' => $user_id ) );
		
		// set the resulting array to associative
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();

 // echo '<pre>';
 // var_dump( $result );
 // echo '</pre>';
 // die;
		// update the database
		updateAlert( $table01, $item_id, $project_id, $dbhost, $dbname, $dbusername, $dbpassword  );
		
		// send email
		$email  	= $result[0]["email"];
		$user_name 	= $result[0]["username"];
		$user_type	= $result[0]["isadmin"];
		sendMail( $email, $user_name, $project_id, $chat_message, $type, $user_type );
		
		$conn = null;
		
	}
	catch( PDOException $e ) {
		echo "Error: " . $e->getMessage();
	}
	break_free_of_try01:
	// End chat message //
	
	//////////////////////
	//////////////////////
	
	// Alert for chat files
	try {
		$type = 'file';
		$conn = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword );
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$query = "SELECT id, receiver_id, project_id, name FROM $table02 WHERE viewed = :viewed AND alerted = :alerted";
		$stmt = $conn->prepare( $query );
		$stmt->execute( array( 
			'viewed' => $viewed, 
			'alerted' => $alerted 
		));

		// set the resulting array to associative
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		
		// foreach( $result as $data ){
			// $user_id 	= $data[ 'receiver_id' ];
			// $project_id = $data[ 'project_id' ];
			// $item_id 	= $data[ 'id' ];
			// $query = "SELECT email FROM $table03 WHERE id = :user_id";
			// $stmt  = $conn->prepare( $query );
			// $stmt->execute( array( 'user_id' => $user_id ) );
		
			// // set the resulting array to associative
			// $stmt->setFetchMode(PDO::FETCH_ASSOC);
			// $result = $stmt->fetchAll();

// // echo  $result[0]["email"];
// // echo '-----------------------';

			// // send email
			// $email  = $result[0]["email"];
			// sendMail( $email, $project_id, $chat_message );
			
			// // update the database
			// updateAlert( $table02, $item_id, $project_id, $dbhost, $dbname, $dbusername, $dbpassword  );
		// }
		
		// send only the last message found in the cron cycle
		if( !count( $result ) )
			goto break_free_of_try02; // it's ok ;)
		
		$ln = count( $result );
		$user_id 	= $result[ $ln -1 ][ 'receiver_id' ];
		$project_id = $result[ $ln -1 ][ 'project_id' ];
		$item_id 	= $result[ $ln -1 ][ 'id' ];
		$chat_message	= $result[ $ln -1 ][ 'name' ];
		$query 		= "SELECT email, username, isadmin FROM $table03 WHERE id = :user_id";
		$stmt  		= $conn->prepare( $query );
		$stmt->execute( array( 'user_id' => $user_id ) );
		
		// set the resulting array to associative
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();

// echo '<pre>';
// var_dump( $result );
// echo '</pre>';	

		// update the database
		updateAlert( $table02, $item_id, $project_id, $dbhost, $dbname, $dbusername, $dbpassword  );
		
		// send email
		$email  	= $result[0]["email"];
		$user_name 	= $result[0]["username"];
		$user_type	= $result[0]["isadmin"];
		
		sendMail( $email, $user_name, $project_id, $chat_message, $type, $user_type );
		
		$conn = null;
	} 
	catch( PDOException $e ) {
		echo "Error: " . $e->getMessage();
	}
	// End chat file //
	break_free_of_try02:
	//////////////////////
	//////////////////////
	
	// send mail
	function sendMail( $email, $user_name, $project_id, $chat_message, $type, $user_type ){
		$project_link 	= 'https://diamta.com/projects/public/index.php/projects/' . $project_id;
		$subject 		= 'You have a new message!';
echo 'User_type: ' . $user_type;		
		$text01			= ( $user_type ) ? 'Your client' : 'Your project manager';
		$text02 		= ( $type == 'chat' ) ? $text01 . ' left you new a message' : $text01 . ' sent you a file';
		
		$message 		= "<!DOCTYPE html><html><style>body {font-family: Arial, Helvetica, sans-serif;}</style><body><p><a href='https://www.diamta.com/?email=[email]&title=[title]' style='color:#ff5335;text-decoration:none;text-transform:none;' target='_blank'><img src='https://diamta.com/img/logo053.png' title='" . $subject . "' alt='Logo Diamta'> </a><br></p><h2>You have a new message</h2><p style='line-height: 30px;'>Hello " . $user_name . ",<br>" . $text02 . ": </p><p style='line-height: 30px; white-space: pre-wrap;'>\"" . $chat_message . "\"</p><p>Please visite the link below to reply. <br><br><a href ='" . $project_link . "'>" . $project_link . "</a></p><p><br>The team</p></body></html>"; // html body';
		
		$headers  = "From: projects@diamta.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		
		//$responce = mail( $email, $subject, $message, $headers ); //  mail( to,subject,message,headers,parameters );
		 echo $message;
	}
	
	// update all table after alert sent to not send repetively
	function updateAlert( $table, $item_id, $project_id, $dbhost, $dbname, $dbusername, $dbpassword  ){
		$alerted = 1;
		// Get unread messages
		try {
			$conn = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword );
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			// $query = "UPDATE table $table WHERE id = :item_id SET alerted = :alerted";
			$query = "UPDATE $table SET alerted = :alerted WHERE project_id = :project_id";
			$stmt  = $conn->prepare( $query );
			$stmt->execute( array( 
				'alerted' 		=> $alerted,
				'project_id'	=> $project_id,
				// 'item_id'	=> $item_id,
			));

			$conn = null;
		} 
		catch( PDOException $e ) {
			echo "Error: " . $e->getMessage();
		}
	}
?>
