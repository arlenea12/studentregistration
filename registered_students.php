<?php
require_once('init.php');

use helpers\Student;

$registered_students = Student::findAll();

require_once('./includes/header.php');
?>

<h1 style="text-align: center; margin: 20px 0; ">Registered Students</h1>

<table>
  <thead>
    <tr>
      <th scope="col">UMID</th>
      <th scope="col">First Name</th>
      <th scope="col">Last Name</th>
      <th scope="col">Project Title</th>
      <th scope="col">Email</th>
      <th scope="col">Phone Number</th>
      <th scope="col">Time Slot</th>
    </tr>
  </thead>
  <tbody>
    	<?php foreach ($registered_students as $registered_student) : ?>
	    <tr>
			<td><?= $registered_student->getUMID(); ?></td>
			<td><?= $registered_student->getFirstName(); ?></td>
			<td><?= $registered_student->getLastName(); ?></td>
			<td><?= $registered_student->getProjectTitle(); ?></td>
			<td><?= $registered_student->getEmail(); ?></td>
			<td><?= $registered_student->getPhoneNumber(); ?></td>
			<td><?= $registered_student->getTimeSlot()->getDateTime(); ?></td>
    	</tr>
    	<?php endforeach; ?>
  </tbody>
</table>

<?php

require_once('./includes/footer.php');

?>