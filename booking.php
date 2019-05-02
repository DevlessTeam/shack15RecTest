<?php

//<Farid Adam> <faridibin@gmail.com>.
interface BookingStructure
{
    public function bookASlot($from, $to);
}

class BookingException extends Exception {}

class Booking implements BookingStructure
{
    private $today;
    private $workingHours;
    private $bookedSlots = [
        ['from'=>'8:00', 'to'=>'9:30']
    ];

    public function __construct($openingTime, $closingTime)
    {
        $this->today = date('Y-m-d');

        try {
            $this->setWorkingHours($openingTime, $closingTime);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function setWorkingHours($openingTime, $closingTime)
    {
        if (empty($openingTime)) {
            throw new BookingException("Uncaught exception: Opening Time must not be empty.");
        } else if (empty($closingTime)) {
            throw new BookingException("Uncaught exception: Closing Time must not be empty.");
        } else if (strtotime($openingTime) > strtotime($closingTime)) {
            throw new BookingException("Uncaught exception: Please make sure the Opening Time and Closing Time are not correctly set.");
        }

        $this->workingHours = [
            'openingTime' => $openingTime,
            'closingTime' => $closingTime
        ];
    }

    private function checkSlotAvailability($from, $to)
    {
        $start = strtotime($from);
        $end = strtotime($to);

        $duration = round(abs($start - $end) / 60,2);

        if (strtotime($from) < strtotime($this->getOpeningTime())) {
            throw new BookingException("Uncaught exception: Exception Sorry you can't book outside of the opening time.");
        } 
        if (strtotime($to) > strtotime($this->getClosingTime())) {
            throw new BookingException("Uncaught exception: Exception Sorry you can't book outside of the closing time.");
        } 
        if ($duration < 30) {
            throw new BookingException("Uncaught exception: Exception Sorry you can't book less than a 30 min slot.");
        } 
        if ($duration > 120) {
            throw new BookingException("Uncaught exception: Exception Sorry you can't book above a 2 hour slot.");
        }

        foreach ($this->bookedSlots as $slots => $time) {
            if (($start >= strtotime($time['from']) ) && ($start <= strtotime($time['to']))) {
                throw new BookingException("Uncaught exception: Exception Sorry there is a meeting from {$time['from']} to {$time['to']}.");
            }
        }

        return true;
    }
    
    public function getAllBookings()
    {
        return $this->bookedSlots;
    }
    
    public function bookASlot($from, $to)
    {
        try {
            if ($this->checkSlotAvailability($from, $to)) {
                $this->bookedSlots[] = ['from' => $from, 'to' => $to];

                echo "Conference Room booked from {$from} to {$to}.";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function getOpeningTime()
    {
        return $this->workingHours['openingTime'];
    }
    
    public function getClosingTime()
    {
        return $this->workingHours['closingTime'];
    }
}


/* Test Cases */
$bookingInstance = new Booking("6:30", "18:00");
var_dump($bookingInstance->getAllBookings()); // array(1) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } }
echo "<br><br>";
var_dump($bookingInstance->bookASlot('8:00', '8:30')); // Uncaught exception: Exception Sorry there is a meeting from 8:00 to 9:30 ...
echo "<br><br>";
var_dump($bookingInstance->bookASlot('8:00', '8:00')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
echo "<br><br>";
var_dump($bookingInstance->bookASlot('8:00', '18:00')); // Uncaught exception: Exception Sorry you can't book above a 2 hour slot in ...
echo "<br><br>";
var_dump($bookingInstance->bookASlot('8:00', '23:00')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
echo "<br><br>";
var_dump($bookingInstance->bookASlot('12:00', '12:15')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
echo "<br><br>";
var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
echo "<br><br>";
var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"
echo "<br><br>";
var_dump($bookingInstance->getAllBookings()); // array(2) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } [1]=> array(2) { ["from"]=> string(5) "12:00" ["to"]=> string(5) "12:15" } }
