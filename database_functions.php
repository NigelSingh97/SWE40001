<?php
// Among the functions here, these should be the most used:
// GetConnection, CreateTablesOrPass, RegisterUser, GetUsername, LogInUser, AddRequisitionForm, AddRequisitionFormComment
// SetRequisitionFormApprovalStatus, GetRequisitionForms, GetRequisitionForm

function QueryDatabase(mysqli& $connection, string $query, array $arguments = [], string $argumentTypes = '') : array {
	/*
	Executes a query.
	All '?' characters in $query will be sequentially replaced with the provided $arguments.
	Returns [bool true, mysqli_result|bool result] on success, [bool false, string error] on failure.
	*/
	
	if (empty($arguments)) {
		foreach (explode(';', $query) as $querySingle) {
			$result = $connection->query($querySingle);
			if (!$result) return [false, $connection->error];
		}
		
		return [true, $result];
	}
	
	$queryObj = $connection->prepare($query);
	if (!$queryObj) return [false, $connection->error];
	
	if ($argumentTypes == '') {
		$queryObj->bind_param(str_repeat('s', count($arguments)), ...$arguments);
	} else {
		$queryObj->bind_param($argumentTypes, ...$arguments);
	}
	
	$returnValue = [];
	if ($queryObj->execute()) {
		$returnValue = [true, $queryObj->get_result()];
	} else {
		$returnValue = [false, $queryObj->error];
	}
	
	$queryObj->close();
	return $returnValue;
}

function QueryLastInsertID(mysqli& $connection) : array {
	// returns [bool true, int id] on success, [bool false, string error] otherwise
	$result = QueryDatabase($connection, 'SELECT LAST_INSERT_ID()');
	if (!$result[0]) return [false, $result[1]];
	
	return [true, $result[1]->fetch_assoc()['LAST_INSERT_ID()']];
}

function ResultIsEmpty(mysqli_result& $result) : bool {
	// returns true if the result set is empty
	return $result->num_rows == 0;
}

class ApprovalStatus {
	const Pending = 0;
	const Approved = 1;
	const Rejected = 2;
	const ApprovedRejected = 3;
}

function GetConnection(string $hostname = "localhost", string $username = "root", string $password = "", string $database = "", int $port = 3306) : array {
	// creates a connection to the database.
	// returns [int 0, mysqli connection] on success, [int 1, string error] on failure.
	$password ="";
	$database="requisitionDatabase";
	$connection = new mysqli($hostname, $username, $password, $database, $port);
	if ($connection->connect_errno) return [1, $connection->connect_error];
	
	return [0, $connection];
}



///*
// THIS IS FOR DEVELOPMENT PURPOSES ONLY, DO NOT LEAVE THIS UNCOMMENTED BEFORE RELEASE!!!
function DropAllTables(mysqli& $connection) : array {
	// drops all existing tables
	// returns [bool true, bool true] on success, [bool false, string error] otherwise.
	return QueryDatabase($connection, 'DROP TABLE IF EXISTS `RequisitionFormComments`, `RequisitionFormLines`, `RequisitionForms`, `Users`');
}
//*/

function RegisterUser(mysqli& $connection, string $username, string $password) : array {
	// registers a user into the database
	// returns [int 0, int userID] on success, [int 1] for username collision, [int 2, string error] on database error
	$result = QueryDatabase($connection, 'SELECT `id` FROM `Users` WHERE `userName` = ?', [$username]);
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (!ResultIsEmpty($result[1])) return [1];
	
	$result = QueryDatabase($connection, 'INSERT INTO `Users` (`userName`, `password`) VALUES (?, ?)', [$username, $password]);
	if (!$result[0]) return [2, $result[1]];
	
	$result = QueryLastInsertID($connection);
	return [$result[0] ? 0 : 2, $result[1]];
}

function GetUsername(mysqli& $connection, int $userID) : array {
	// gets the username for a given user ID
	// returns [int 0, string username] on success, [int 1] for invalid userID, [int 2, string error] on database error
	$result = QueryDatabase($connection, 'SELECT `userName` FROM `Users` WHERE `id` = ?', [$userID], 'i');
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (ResultIsEmpty($result[1])) return [1];
	
	return [0, $result[1]->fetch_assoc()['userName']];
}

function LogInUser(mysqli& $connection, string $username, string $password) : array {
	// logs in a user
	// gets the user ID for a given username and password
	// returns [int 0, int userID] on success, [int 1] for incorrect username / password, [int 2, string error] on database error
	$result = QueryDatabase($connection, 'SELECT `id` FROM `Users` WHERE `userName` = ? AND `password` = ?', [$username, $password]);
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (ResultIsEmpty($result[1])) return [1];
	
	return [0, $result[1]->fetch_assoc()['id']];
}

function GetUserAccessLevel(mysqli& $connection, int $userID) : array {
	// gets the access level for a given user ID
	// can also be used to check whether a given userID is valid
	// returns [int 0, bool true] if admin, [int 0, bool false] if regular user, [int 1] for invalid userID, [int 2, string <error>] on database error
	$result = QueryDatabase($connection, 'SELECT `admin`,`type` FROM `Users` WHERE `id` = ?', [$userID], 'i');
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (ResultIsEmpty($result[1])) return [1];
	
    $fetch_assoc = $result[1]->fetch_assoc();
	return [0,$fetch_assoc['admin'], $fetch_assoc['type']];
}

function AddRequisitionForm(mysqli& $connection, int $userID, array $rows = []) : array {
	/*
	adds a requisition form to the database
	$rows must be an array of row arrays, where each row array must have the following: [
		'item'=>string,
		'unitPrice'=>string,
		'quantity'=>string,
		'supplier'=>string,
		'supplierAddress'=>string?,
		'remarks'=>string?
	]
	returns [int 0, int formID] on success, [int 1, int 0] on invalid userID, [int 1, int 1] on incorrect row array contents, [int 2, string error] on database error
	*/
	
	// check 1: userID
	$result = GetUserAccessLevel($connection, $userID);
	if ($result[0] != 0) return $result;
	
	$result = QueryDatabase($connection, 'SELECT `id` FROM `Users` WHERE `id` = ?', [$userID]);
	if (!$result[0]) {
		return [2, $result[1]];
	} if (ResultIsEmpty($result[1])) return [1, 0];
	
	// check 2: rows
	foreach ($rows as $row) {
		if (!empty($row['item'])) {
			if (
				empty($row['quantity'])
				or empty($row['unitPrice'])
				or empty($row['supplier'])
			) return [1, 1];
		}
	}
	
	// actual insertion
	$result = QueryDatabase($connection, 'BEGIN');
	if (!$result[0]) return [2, $result[1]];
	
	$result = QueryDatabase(
		$connection,
		'INSERT INTO `RequisitionForms` (`userID`) VALUES (?)',
		[$userID],
		'i'
	);
	if (!$result[0]) return [2, $result[1]]; 
	
	$result = QueryLastInsertID($connection);
	if (!$result[0]) return [2, $result[1]];
	$formID = $result[1];
	
	foreach ($rows as $row) {
		$columns = ['`formID`', '`item`', '`quantity`', '`unitPrice`', '`supplier`'];
		$values = [$formID, $row['item'], $row['quantity'], $row['unitPrice'], $row['supplier']];
		
		if (!empty($row['supplierAddress'])) {
			$columns[] = '`supplierAddress`';
			$values[] = $row['supplierAddress'];
		}
		if (!empty($row['remarks'])) {
			$columns[] = '`remarks`';
			$values[] = $row['remarks'];
		}
		
		$result = QueryDatabase(
			$connection,
			'INSERT INTO `RequisitionFormLines` ('.implode(
				', ', $columns
			).') VALUES ('.implode(
				', ', array_fill(0, count($columns), '?')
			).')',
			$values,
			'i'.str_repeat('s', count($columns)-1)
		);
		if (!$result[0]) return [2, $result[1]]; 
	}
	
	$result = QueryDatabase($connection, 'COMMIT');
	if (!$result[0]) return [2, $result[1]];
	
	return [0, $formID];
}

function AddRequisitionFormComment(mysqli& $connection, int $formID, int $userID, string $comment) : array {
	// adds a requisition form comment to the database
	// returns [int 0, int commentID] on success, [int 1, int 0] for invalid form ID, [int 1, int 1] for invalid user ID, [int 2, string error] on database error
	
	// check 1: userID
	$result = GetUserAccessLevel($connection, $userID);
	switch ($result) {
	case 2:
		return $result;
		break;
	case 1:
		return [1, 1];
		break;
	}
	
	$result = QueryDatabase($connection, 'SELECT `id` FROM `Users` WHERE `id` = ?', [$userID]);
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (ResultIsEmpty($result[1])) return [1, 1];
	
	// check 2: formID
	$result = QueryDatabase($connection, 'SELECT `id` FROM `RequisitionForms` WHERE `id` = ?', [$formID]);
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (ResultIsEmpty($result[1])) return [1, 0];
	
	// actual insertion
	$result = QueryDatabase(
		$connection,
		'INSERT INTO `RequisitionFormComments` (`formID`, `userID`, `comment`) VALUES (?, ?, ?)',
		[$formID, $userID, $comment],
		'iis'
	);
	if (!$result[0]) return [2, $result[1]];
	
	$result = QueryLastInsertID($connection);
	return [$result[0] ? 0 : 2, $result[1]];
}

function SetRequisitionFormApprovalStatus(mysqli& $connection, int $formID, int $userID, int $status) : array {
	// sets the approval status for a requisition form
	// returns [int 0, bool false] on success, [int 1, int 0] for invalid form ID, [int 1, int 1] for invalid user ID, [int 1, int 2] for non-admin user ID, [int 1, int 3] for invalid status, [int 2, string error] on database error
	
	// check 1: userID
	$result = GetUserAccessLevel($connection, $userID);
	switch ($result[0]) {
	case 2:
		return [2, $result[1]];
		break;
	case 1:
		return [1, 1];
		break;
	}
	if (!$result[1]) return [1, 2];
	
	// check 2: formID
	$result = QueryDatabase($connection, 'SELECT `id` FROM `RequisitionForms` WHERE `id` = ?', [$formID]);
	if (!$result[0]) {
		return [2, $result[1]];
	} else if (ResultIsEmpty($result[1])) return [1, 0];
	
	// actual insertion
	$approvalString = '';
	switch ($status) {
	case ApprovalStatus::Pending:
		$approvalString = 'pending';
		break;
	case ApprovalStatus::Approved:
		$approvalString = 'approved';
		break;
	case ApprovalStatus::Rejected:
		$approvalString = 'rejected';
		break;
	default:
		return [1, 3];
	}
	$result = QueryDatabase(
		$connection,
		'UPDATE `RequisitionForms` SET `approvedState` = ?, `approverUserID` = ? WHERE `id` = ?',
		[$approvalString, $userID, $formID],
		'sii'
	);
	return [$result[0] ? 0 : 2, $result[1]];
}

function GetRequisitionForms(mysqli& $connection, array $filters = []) : array {
	/*
	gets requisition forms from the database, optionally based on a set of filters
	
	available filters:
	'onOrAfterTime'=>string (in yyyy-mm-dd hh:mm:ss format),
	'onOrBeforeTime'=>string,
	'username'=>string,
	'approvedState'=>int
	
	returns [int 1] on invalid approval status filter, [int 2, string error] on database error
	otherwise, returns [int 0, [
		[
			'id'=>int,
			'timeSubmitted'=>string,
			'userID'=>int,
			'username'=>string,
			'approvedState'=>int (see ApprovalStatus)
		],
		[
			'id'=>int,
			...
		],
		...
	]]
	*/
	$approvalStatusStringToEnum = [
		'pending'=>ApprovalStatus::Pending,
		'approved'=>ApprovalStatus::Approved,
		'rejected'=>ApprovalStatus::Rejected
	];
	
	// where clause
	$wherePieces = [];
	$arguments = [];
	if (!empty($filters['onOrAfterTime'])) {
		$wherePieces[] = 'TIMESTAMPDIFF(SQL_TSI_SECOND, ?, `timeSubmitted`) >= 0';
		$arguments[] = $filters['onOrAfterTime'];
	}
	if (!empty($filters['onOrBeforeTime'])) {
		$wherePieces[] = 'TIMESTAMPDIFF(SQL_TSI_SECOND, ?, `timeSubmitted`) <= 0';
		$arguments[] = $filters['onOrBeforeTime'];
	}
	if (!empty($filters['username'])) {
		$wherePieces[] = '`userName` = ?';
		$arguments[] = $filters['username'];
	}
	if (!empty($filters['approvedState'])) {
		$wherePiece = '`approvedState` = ?';
		$approvalString = '';
		
		switch ($filters['approvedState']) {
		case ApprovalStatus::Pending:
			$approvalString = 'pending';
			break;
		case ApprovalStatus::Approved:
			$approvalString = 'approved';
			break;
		case ApprovalStatus::Rejected:
			$approvalString = 'rejected';
			break;
		case ApprovalStatus::ApprovedRejected:
			$wherePiece = '(`approvedState` = ? OR `approvedState` = ?)';
			$arguments[] = 'approved';
			$approvalString = 'rejected';
			break;
		default:
			return [1];
		}
		
		$wherePieces[] = $wherePiece;
		$arguments[] = $approvalString;
	}
	
	// actual execution
	$assembledQuery = 'SELECT
		`RequisitionForms`.`id` AS `id`,
		`RequisitionForms`.`timeSubmitted` AS `timeSubmitted`,
		`RequisitionForms`.`userID` AS `userID`,
		`Users`.`userName` AS `userName`,
		`RequisitionForms`.`approvedState` AS `approvedState`
		FROM `RequisitionForms`
		LEFT OUTER JOIN `Users` ON `RequisitionForms`.`userID` = `Users`.`id`
	';
	
	if ($wherePieces) {
		$assembledQuery .= ' WHERE ' . implode('AND', $wherePieces);
	}
	
	$assembledQuery .= ' ORDER BY `timeSubmitted` DESC';
	
	$result = QueryDatabase($connection, $assembledQuery, $arguments);
	if (!$result[0]) return [2, $result[1]];
	
	// output presentation
	$forms = [];
	while ($row = $result[1]->fetch_assoc()) $forms[] = [
		'id'=>$row['id'],
		'timeSubmitted'=>$row['timeSubmitted'],
		'userID'=>$row['userID'],
		'username'=>$row['userName'],
		'approvedState'=>$approvalStatusStringToEnum[$row['approvedState']]
	];
	
	return [0, $forms];
}

function GetRequisitionForm(mysqli& $connection, int $formID) : array {
	/*
	gets a requisition form from the database for a given form ID
	
	this function should not be called more than once per page
	returns [int 2, string error] on database error and [int 1] for invalid form ID
	otherwise returns [int 0, [
		'id'=>int,
		'timeSubmitted'=>string,
		'userID'=>int,
		'username'=>string,
		'approvedState'=>int (see ApprovalStatus),
		'approverUserID'=>int,
		'approverUsername'=>string,
		'rows'=>[
			'item'=>string,
			'quantity'=>string,
			'unitPrice'=>string,
			'supplier'=>string,
			'supplierAddress'=>string|null,
			'remarks'=>string|null,
		]
		'comments'=>[
			[
				'id'=>int,
				'time'=>string,
				'userID'=>int,
				'username'=>string,
				'comment'=>string
			],
			[
				'id'=>int,
				...
			],
			...
		]
	]]
	*/
	
	$result = QueryDatabase($connection, 'SELECT * FROM `RequisitionForms` WHERE `id` = ?', [$formID], 'i');
	if (!$result[0]) {
		return [2, $result[1]];
	}  if (ResultIsEmpty($result[1])) return [1];
	$form = $result[1]->fetch_assoc();
	
	$approvalStatusStringToEnum = [
		'pending'=>ApprovalStatus::Pending,
		'approved'=>ApprovalStatus::Approved,
		'rejected'=>ApprovalStatus::Rejected
	];
	$form['approvedState'] = $approvalStatusStringToEnum[$form['approvedState']];
	
	// username
	$result = GetUsername($connection, $form['userID']);
	if ($result[0]==2) return $result;
	$form['username'] = $result[1];
	
	// rows
	$result = QueryDatabase($connection, 'SELECT `item`, `quantity`, `unitPrice`, `supplier`, `supplierAddress`, `remarks` FROM `RequisitionFormLines` WHERE `formID` = ?', [$formID], 'i');
	if (!$result[0]) return [2, $result[1]];
	$form['rows'] = [];
	while ($row = $result[1]->fetch_assoc()) {
		$form['rows'][] = $row;
	}
	
	// approverUsername
	if (!empty($form['approverUserID'])) {
		$result = GetUsername($connection, $form['approverUserID']);
		if ($result[0]==2) return $result;
		$form['approverUsername'] = $result[1];
	}
	
	// comments
	$result = QueryDatabase($connection, 'SELECT `id`, `time`, `userID`, `comment` FROM `RequisitionFormComments` WHERE `formID` = ? ORDER BY `time` DESC', [$formID], 'i');
	if (!$result[0]) return [2, $result[1]];
	$form['comments'] = [];
	while ($comment = $result[1]->fetch_assoc()) {
		$usernameResult = GetUsername($connection, $comment['userID']);
		if ($usernameResult[0]==2) return $usernameResult;
		$comment['username'] = $usernameResult[1];
		
		$form['comments'][] = $comment;
	}
	
	return [0, $form];
}

?>
