<?php
require_once('init.php');

use helpers\Request;
use helpers\Student;
use helpers\TimeSlot;
use helpers\Redirect;
use helpers\Flash;

$umid = '';
$time_slot_id = '';
$student = null;
$time_slot = null;

if (Request::isGet()) {

	$umid = Request::getParam('umid');
	$time_slot_id = Request::getParam('time_slot_id');

	if (empty($umid) || empty($time_slot_id)) {
		Flash::setErrorMessage('UMID or time slot not found');
		Redirect::to('index.php');
	}

	$student = Student::findByUMID($umid);
	$time_slot = TimeSlot::findById($time_slot_id);
}

if (Request::isPost()) {

	$student = Student::findByUMID(Request::getParam('umid'));
	$time_slot = TimeSlot::findById(Request::getParam('time_slot_id'));

	$student->updateTimeSlot($time_slot->getId());

	Flash::setSuccessMessage('You have successfully updated your session time slot.');
	Redirect::to('index.php');

}


require_once('./includes/header.php');
?>

<div class="confirmation">
	<form action="" method="post">
		<h3>You are currently registered for another session during this time slot</h3>
		<h2 class="time_slot"><?= $student->getTimeSlot()->getDateTime(); ?></h2>
		<p><b>Would you like to change your session?</b></p>
		<h2 class="time_slot"><?= $time_slot->getDateTime(); ?></h2>
		<p><i>Click the button below to submit your request for a time slot change.</i></p>

		<input type="hidden" name="umid" value="<?= $umid ?>">
		<input type="hidden" name="time_slot_id" value="<?= $time_slot_id ?>">
		
		<input type="submit" value="Change time slot" />
	</form>
</div>

<?php 
require_once('./includes/footer.php');
?>