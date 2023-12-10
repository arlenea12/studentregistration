<?php
require_once('init.php');

use helpers\Errors;
use helpers\Flash;
use helpers\Input;
use helpers\Request;
use helpers\Student;
use helpers\TimeSlot;
use helpers\Redirect;

$time_slots = TimeSlot::findAllWithRemainingSeats();

if (Request::isPost()) {

	$umid = Request::getParam('umid');
	$first_name = Request::getParam('first_name');
	$last_name = Request::getParam('last_name');
	$project_title = Request::getParam('project_title');
	$email = Request::getParam('email');
	$phone_number = Request::getParam('phone_number');
	$time_slot_id = Request::getParam('time_slot_id');

	// Validation
	if (empty($umid) || !preg_match('/^\d{8}$/', $umid)) {
	    Errors::addError('umid', 'Student ID must be 8 digits');
	}
	if (empty($first_name) || !ctype_alpha($first_name)) {
	    Errors::addError('first_name', 'First name is required and must contain only alphabetic characters');
	}
	if (empty($last_name) || !ctype_alpha($last_name)) {
	    Errors::addError('last_name', 'Last name is required and must contain only alphabetic characters');
	}
	if (empty($project_title)) {
	    Errors::addError('project_title', 'Project title required');
	}
	if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	    Errors::addError('email', 'Invalid email address format');
	} else {
	    $emailParts = explode('@', $email);
	    if (count($emailParts) !== 2 || strlen($emailParts[0]) > 64 || !preg_match('/^[a-zA-Z0-9.-]{1,80}$/', $emailParts[1])) {
	        Errors::addError('email', 'Invalid email address format');
	    }
	}
	if (empty($phone_number) || !preg_match('/^\d{3}-\d{3}-\d{4}$/', $phone_number)) {
	    Errors::addError('phone_number', 'Invalid phone number format (should be in the form 999-999-9999)');
	}

	if (!Errors::hasErrors()) {
		if (Student::isRegistered($umid) && !Student::isRegisteredInTimeSlot($umid, $time_slot_id)) {
			Redirect::to('confirmation.php?umid='.$umid.'&time_slot_id='.$time_slot_id);
		} else if (Student::isRegistered($umid) && Student::isRegisteredInTimeSlot($umid, $time_slot_id)) {
			Flash::setSuccessMessage('Your registration for this time slot is already confirmed.');
			Redirect::to('index.php');
		} else {
			Student::create($umid, $first_name, $last_name, $project_title, $email, $phone_number, TimeSlot::findById($time_slot_id));
			Flash::setSuccessMessage('Your registration for this time slot has been successfully confirmed.');
			Redirect::to('index.php');
		}
	}
}


require_once('./includes/header.php');

?>

<?php if (Flash::hasSuccessMessage()) : ?>
<div class="alert alert-success" role="alert"><?= Flash::getSuccessMessage(); ?></div>
<?php endif; ?>

<?php if (Flash::hasErrorMessage()) : ?>
<div class="alert alert-error" role="alert"><?= Flash::getErrorMessage(); ?></div>
<?php endif; ?>

<h1 style="text-align: center; margin: 20px 0; ">Register</h1>

<form action="" method="post" id="register_form">
	<ul class="form-style-1">
	    <li>
	    	<label>Full Name <span class="required">*</span></label>
	    	<input 
	    		type="text" name="first_name" id="first_name" placeholder="First" 
	    		class="field-divided <?= (Errors::hasError('first_name')) ? 'is-invalid' : '' ?>" 
	    		value="<?= Input::getPrev('first_name'); ?>"
	    	/>
	    	<input 
	    		type="text" name="last_name" id="last_name" placeholder="Last" 
	    		class="field-divided <?= (Errors::hasError('last_name')) ? 'is-invalid' : '' ?>" 
	    		value="<?= Input::getPrev('last_name'); ?>"
	    	/>
	    	<?= Errors::getFirstErrorHTML('first_name') ?>
            <?= Errors::getFirstErrorHTML('last_name') ?>
	    </li>
	    <li>
	        <label>UMID <span class="required">*</span></label>
	        <input 
	        	type="text" name="umid" id="umid"
	        	class="field-long <?= (Errors::hasError('umid')) ? 'is-invalid' : '' ?>" 
	        	value="<?= Input::getPrev('umid'); ?>"
	        />
	        <?= Errors::getFirstErrorHTML('umid') ?>
	    </li>
	    <li>
	        <label>Project Title <span class="required">*</span></label>
	        <input 
	        	type="text" name="project_title" id="project_title"
	        	class="field-long <?= (Errors::hasError('project_title')) ? 'is-invalid' : '' ?>" 
	        	value="<?= Input::getPrev('project_title'); ?>"
	        />
	        <?= Errors::getFirstErrorHTML('project_title') ?>
	    </li>
	    <li>
	        <label>Email <span class="required">*</span></label>
	        <input type="email" name="email" id="email"
	        	class="field-long <?= (Errors::hasError('email')) ? 'is-invalid' : '' ?>" 
	        	value="<?= Input::getPrev('email'); ?>"
	        />
	        <?= Errors::getFirstErrorHTML('email') ?>
	    </li>
	    <li>
	        <label>Phone Number <span class="required">*</span></label>
	        <input type="text" name="phone_number" id="phone_number"
	        	class="field-long <?= (Errors::hasError('phone_number')) ? 'is-invalid' : '' ?>" 
	        	value="<?= Input::getPrev('phone_number'); ?>"
	        />
	        <?= Errors::getFirstErrorHTML('phone_number') ?>
	    </li>
	    <li>
	        <label>Time Slots Available</label>
	        <select name="time_slot_id" class="field-select">
		        <?php foreach($time_slots as $time_slot) { ?>
		        	<option value="<?= $time_slot->getId(); ?>" <?= Input::isSelected('time_slot_id', $time_slot->getId()) ?> ><?= "{$time_slot->getDate()} {$time_slot->getStartTime()} - {$time_slot->getEndTime()} {$time_slot->getRemainingSeats()} seats remaining" ?></option>
		        <?php } ?>
	        </select>
	    </li>
	    <li>
	        <input type="submit" value="Submit" />
	    </li>
	</ul>
</form>

<?php

require_once('./includes/footer.php');

?>  