<?php

namespace helpers;

use PDO;

class TimeSlot {

    private $_id;
    private $_date;
    private $_start_time;
    private $_end_time;
    private $_max_capacity;
    private $_remaining_seats;

    private static $conn;

    public function __construct($date, $start_time, $end_time, $max_capacity, $remaining_seats) {
        $this->_id = null;
        $this->_date = $date;
        $this->_start_time = $start_time;
        $this->_end_time = $end_time;
        $this->_max_capacity = $max_capacity;
        $this->_remaining_seats = $remaining_seats;

        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }
    }

    public static function findAll() {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        $stmt = self::$conn->prepare("SELECT * FROM time_slots");
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $timeSlots = [];
        foreach ($results as $result) {
            $timeSlot = new TimeSlot(
                $result['date'],
                $result['start_time'],
                $result['end_time'],
                $result['max_capacity'],
                $result['remaining_seats']
            );
            $timeSlot->setId($result['id']);
            $timeSlots[] = $timeSlot;
        }

        return $timeSlots;
    }

    public static function findById($id) {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }
        $stmt = self::$conn->prepare("SELECT * FROM time_slots WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$result) {
            return null;
        }

        $timeSlot = new TimeSlot(
            $result['date'],
            $result['start_time'],
            $result['end_time'],
            $result['max_capacity'],
            $result['remaining_seats']
        );
        $timeSlot->setId($result['id']);

        return $timeSlot;
    }

    public function create($date, $start_time, $end_time, $max_capacity, $remaining_seats) {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }
        $stmt = self::$conn->prepare("INSERT INTO time_slots (date, start_time, end_time, max_capacity, remaining_seats) VALUES (:date, :start_time, :end_time, :max_capacity, :remaining_seats)");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':max_capacity', $max_capacity);
        $stmt->bindParam(':remaining_seats', $remaining_seats);

        $stmt->execute();

        if ($this->_id === null) {
            $this->_id = self::$conn->lastInsertId();
        }
    }

    public function update() {
        if ($this->_id !== null) {
            // Update existing record
            $stmt = self::$conn->prepare("UPDATE time_slots SET date = :date, start_time = :start_time, end_time = :end_time, max_capacity = :max_capacity, remaining_seats = :remaining_seats WHERE id = :id");

            $stmt->bindParam(':date', $this->_date);
            $stmt->bindParam(':start_time', $this->_start_time);
            $stmt->bindParam(':end_time', $this->_end_time);
            $stmt->bindParam(':max_capacity', $this->_max_capacity);
            $stmt->bindParam(':remaining_seats', $this->_remaining_seats);
            $stmt->bindParam(':id', $this->_id);

            $stmt->execute();
        }
    }

    public function delete() {
        if ($this->_id !== null) {
            // Delete the record
            $stmt = self::$conn->prepare("DELETE FROM time_slots WHERE id = :id");
            $stmt->bindParam(':id', $this->_id);
            $stmt->execute();

            // Reset object properties after deletion if needed
            $this->_id = null;
            // Reset other properties as required
        }
    }

    public static function findAllWithRemainingSeats() {
        if (self::$conn === null) {
            self::$conn = DB::getInstance()->getConnection();
        }

        $stmt = self::$conn->prepare("SELECT * FROM time_slots WHERE remaining_seats > 0");
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $timeSlots = [];
        foreach ($results as $result) {
            $timeSlot = new TimeSlot(
                $result['date'],
                $result['start_time'],
                $result['end_time'],
                $result['max_capacity'],
                $result['remaining_seats']
            );
            $timeSlot->setId($result['id']);
            $timeSlots[] = $timeSlot;
        }

        return $timeSlots;
    }

    // Setters
    public function setId($id) {
        $this->_id = $id;
    }

    public function setDate($date) {
        $this->_date = $date;
    }

    public function setStartTime($start_time) {
        $this->_start_time = $start_time;
    }

    public function setEndTime($end_time) {
        $this->_end_time = $end_time;
    }

    public function setMaxCapacity($max_capacity) {
        $this->_max_capacity = $max_capacity;
    }

    public function setRemainingSeats($remaining_seats) {
        $this->_remaining_seats = $remaining_seats;
    }

    // Getters
    public function getId() {
        return $this->_id;
    }

    public function getDate() {
        return $this->_date;
    }

    public function getStartTime() {
        return $this->_start_time;
    }

    public function getEndTime() {
        return $this->_end_time;
    }

    public function getMaxCapacity() {
        return $this->_max_capacity;
    }

    public function getRemainingSeats() {
        return $this->_remaining_seats;
    }

    public function getDateTime() {
        return $this->_date . ' ' . $this->_start_time . ' - ' . $this->_end_time;
    }

}
