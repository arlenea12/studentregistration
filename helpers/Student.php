<?php

namespace helpers;

use PDO;
use Exception;

class Student {

    private $_umid;
    private $_first_name;
    private $_last_name;
    private $_project_title;
    private $_email;
    private $_phone_number;
    private $_time_slot;

    private static $conn;

    public function __construct($umid, $first_name, $last_name, $project_title, $email, $phone_number, $time_slot) {
        $this->_umid = $umid;
        $this->_first_name = $first_name;
        $this->_last_name = $last_name;
        $this->_project_title = $project_title;
        $this->_email = $email;
        $this->_phone_number = $phone_number;
        $this->_time_slot = $time_slot;

        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }
    }

    public static function findAll() {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        $stmt = self::$conn->prepare("SELECT * FROM students");
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $students = [];
        foreach ($results as $result) {
            $student = new Student(
                $result['umid'],
                $result['first_name'],
                $result['last_name'],
                $result['project_title'],
                $result['email'],
                $result['phone_number'],
                TimeSlot::findById($result['time_slot_id'])
            );
            $students[] = $student;
        }

        return $students;
    }

    public static function findByUMID($umid) {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }
        $stmt = self::$conn->prepare("SELECT * FROM students WHERE umid = :umid LIMIT 1");
        $stmt->bindParam(':umid', $umid);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$result) {
            return null;
        }

        $student = new Student(
            $result['umid'],
            $result['first_name'],
            $result['last_name'],
            $result['project_title'],
            $result['email'],
            $result['phone_number'],
            TimeSlot::findById($result['time_slot_id'])
        );

        return $student;
    }

    public static function create($umid, $first_name, $last_name, $project_title, $email, $phone_number, $time_slot) {
    	if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        self::$conn->beginTransaction();

        try {
            $time_slot_id = $time_slot->getId();

            // Check if there are remaining seats in the selected time slot
            $checkSeatsStmt = self::$conn->prepare("SELECT remaining_seats FROM time_slots WHERE id = :time_slot_id FOR UPDATE");
            $checkSeatsStmt->bindParam(':time_slot_id', $time_slot_id);
            $checkSeatsStmt->execute();
            $remainingSeats = $checkSeatsStmt->fetchColumn();
            $checkSeatsStmt->closeCursor();

            if ($remainingSeats <= 0) {
                throw new \Exception("No available seats in the selected time slot.");
            }

            // Book a seat by inserting the student and updating remaining seats
            $insertStudentStmt = self::$conn->prepare("INSERT INTO students (umid, first_name, last_name, project_title, email, phone_number, time_slot_id) VALUES (:umid, :first_name, :last_name, :project_title, :email, :phone_number, :time_slot_id)");
            $insertStudentStmt->bindParam(':umid', $umid);
            $insertStudentStmt->bindParam(':first_name', $first_name);
            $insertStudentStmt->bindParam(':last_name', $last_name);
            $insertStudentStmt->bindParam(':project_title', $project_title);
            $insertStudentStmt->bindParam(':email', $email);
            $insertStudentStmt->bindParam(':phone_number', $phone_number);
            $insertStudentStmt->bindParam(':time_slot_id', $time_slot_id);
            $insertStudentStmt->execute();

            // Decrement the remaining seats for the selected time slot
            $updateSeatsStmt = self::$conn->prepare("UPDATE time_slots SET remaining_seats = remaining_seats - 1 WHERE id = :time_slot_id");
            $updateSeatsStmt->bindParam(':time_slot_id', $time_slot_id);
            $updateSeatsStmt->execute();

            self::$conn->commit();
            return true;
        } catch (Exception $e) {
            self::$conn->rollBack();
            // Handle exceptions or return false to indicate failure
            return false;
        }
    }

    public function updateTimeSlot($newTimeSlotId) {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        self::$conn->beginTransaction();

        try {
            // Retrieve the old time slot ID
            $oldTimeSlotId = $this->_time_slot->getId();

            // Update the student's time slot
            $stmt = self::$conn->prepare("UPDATE students SET time_slot_id = :new_time_slot_id WHERE umid = :umid");
            $stmt->bindParam(':new_time_slot_id', $newTimeSlotId);
            $stmt->bindParam(':umid', $this->_umid);
            $stmt->execute();

            // Increment the remaining seats for the old time slot
            $updateOldTimeSlotStmt = self::$conn->prepare("UPDATE time_slots SET remaining_seats = remaining_seats + 1 WHERE id = :old_time_slot_id");
            $updateOldTimeSlotStmt->bindParam(':old_time_slot_id', $oldTimeSlotId);
            $updateOldTimeSlotStmt->execute();

            // Decrement the remaining seats for the new time slot
            $updateNewTimeSlotStmt = self::$conn->prepare("UPDATE time_slots SET remaining_seats = remaining_seats - 1 WHERE id = :new_time_slot_id");
            $updateNewTimeSlotStmt->bindParam(':new_time_slot_id', $newTimeSlotId);
            $updateNewTimeSlotStmt->execute();

            self::$conn->commit();
            return true;
        } catch (Exception $e) {
            self::$conn->rollBack();
            // Handle exceptions or return false to indicate failure
            return false;
        }
    }

    public function save() {
        if ($this->_umid !== null) {
            // Update existing record
            $stmt = self::$conn->prepare("UPDATE students SET first_name = :first_name, last_name = :last_name, project_title = :project_title, email = :email, phone_number = :phone_number, time_slot_umid = :time_slot_umid WHERE umid = :umid");

            $stmt->bindParam(':first_name', $this->_first_name);
            $stmt->bindParam(':last_name', $this->_last_name);
            $stmt->bindParam(':project_title', $this->_project_title);
            $stmt->bindParam(':email', $this->_email);
            $stmt->bindParam(':phone_number', $this->_phone_number);
            $stmt->bindParam(':time_slot_umid', $this->_time_slot->getId());
            $stmt->bindParam(':umid', $this->_umid);

            $stmt->execute();

            return true;
        }
    }

    public function delete() {
        if ($this->_umid !== null) {
            // Delete the record
            $stmt = self::$conn->prepare("DELETE FROM students WHERE umid = :umid");
            $stmt->bindParam(':umid', $this->_umid);
            $stmt->execute();

            // Reset object properties after deletion if needed
            $this->_umid = null;
            // Reset other properties as required
        }
    }

    public static function isRegistered($umid) {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        $stmt = self::$conn->prepare("SELECT COUNT(*) as count FROM students WHERE umid = :umid");
        $stmt->bindParam(':umid', $umid);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $result['count'] > 0;
    }

    public static function isRegisteredInTimeSlot($umid, $time_slot_id) {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        $stmt = self::$conn->prepare("SELECT COUNT(*) as count FROM students WHERE umid = :umid AND time_slot_id = :time_slot_id");
        $stmt->bindParam(':umid', $umid);
        $stmt->bindParam(':time_slot_id', $time_slot_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $result['count'] > 0;
    }

    // Setters
    public function setUMID($umid) {
        $this->_umid = $umid;
    }

    public function setFirstName($first_name) {
        $this->_first_name = $first_name;
    }

    public function setLastName($last_name) {
        $this->_last_name = $last_name;
    }

    public function setProjectTitle($project_title) {
        $this->_project_title = $project_title;
    }

    public function setEmail($email) {
        $this->_email = $email;
    }

    public function setPhoneNumber($phone_number) {
        $this->_phone_number = $phone_number;
    }

    public function setTimeSlot($time_slot) {
        $this->_time_slot = $time_slot;
    }

    // Getters
    public function getUMID() {
        return $this->_umid;
    }

    public function getFirstName() {
        return $this->_first_name;
    }

    public function getLastName() {
        return $this->_last_name;
    }

    public function getProjectTitle() {
        return $this->_project_title;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getPhoneNumber() {
        return $this->_phone_number;
    }

    public function getTimeSlot() {
        return $this->_time_slot;
    }

}