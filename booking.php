<?php

//<full name> <email>.
interface BookingStructure
{
     function bookASlot($from, $to);
}


class Booking implements BookingStructure
{
    private $bookedSlots = [
        ['from'=>'8:00', 'to'=>'9:30']
    ];

    private $openingTime, $closingTime;
    private $maxMeetingTime = 7200; // equivalent to 2hrs
    private $minimumMeetingTime = 1800; // equivalent to 30 min

    /// optional parameter to give time interval between meetings
    ///  // value of 0 means users can book right after the end of another meeting
    private $timeIntervalBtnMeetings; // in seconds

    /**
     * Booking constructor.
     * @param $openingTime
     * @param $closingTime
     * @param int $timeIntervalBtnMeetingsInMin
     * @throws Exception
     */

    public function __construct($openingTime, $closingTime, $timeIntervalBtnMeetingsInMin = 10)
    {
        // check if the parameters passed are instance of time
        if (!strtotime($openingTime) || !strtotime($closingTime)){
            throw new Exception("opening and closing times must be instance of time. eg. 8:00");
        }

        // check if $timeIntervalBtnMeetingsInMin is an int
        if (!is_int($timeIntervalBtnMeetingsInMin)){
            throw new Exception("Interval between meetings should be an integer");
        }

        $this->openingTime = $openingTime;
        $this->closingTime = $closingTime;
        $this->timeIntervalBtnMeetings = $timeIntervalBtnMeetingsInMin * 60; // converting to seconds
    }
    
    public function getAllBookings()
    {
        return $this->bookedSlots;
    }

    /**
     * bookASlot method
     * @param $from
     * @param $to
     * @return string
     * @throws Exception
     */

    public function bookASlot($from, $to)
    {
        // check if the parameters passed are instance of date time
        if (!strtotime($from) || !strtotime($to)){
            throw new Exception("start time and end time must instance of time. eg 2:30");
        }

        // check the values of from and to were interchanged
        if (strtotime($from) > strtotime($to)){
            throw new Exception("You've likely interchanged the starting and closing times ");
        }


        $slotInterval = strtotime($to) - strtotime($from);

        //check if the slot is less than 30 min
        if ($slotInterval < $this->minimumMeetingTime){
            throw new Exception("Sorry you can't book less than a 30 min slot");
        }

        // check if the slot is more than 2 hrs
        if ($slotInterval > $this->maxMeetingTime){
            throw new Exception("Sorry you can't book above a 2 hour slot");
        }

        // check if its before the opening time or after the closing time
        if (strtotime($from) < strtotime($this->openingTime) || strtotime($from) > strtotime($this->closingTime)){
            throw new Exception("sorry all bookings should be within the opening and closing times thus ". $this->openingTime. " to ". $this->closingTime);
        }

        // check if its outside the closing time
        if (strtotime($to) > strtotime($this->closingTime)){
            throw new Exception("sorry your program can't close after the closing time ".$this->closingTime);
        }

        //check if the slot is already booked

        $isSlotBooked = $this->isSlotsAlreadyBooked($from,$to);
        if ($isSlotBooked["status"]){
            throw new Exception($isSlotBooked["message"]);
        }

        array_push($this->bookedSlots,[ 'from' => $from, 'to' => $to]);

        return "reservation from ".$from." to ".$to." is successful! :D";

    }
    
    public function getOpeningTime()
    {
        // add code here
        return $this->openingTime;
    }
    
    public function getClosingTime()
    {
        // add code here
        return $this->closingTime;
    }

    /*
     * Check if slot is already book
     * @param from
     * @param to
     * @return mixed array of boolean and string // if true, slot is booked, else not booked, message
     * */
    private function isSlotsAlreadyBooked($from, $to){

        $slotIsBooked = ["status" => false, "slot is available"];

        foreach ($this->bookedSlots as $bookedSlot){

            // check if starting time is within a booked slot

            if (strtotime($from) >= strtotime($bookedSlot['from'])  && strtotime($from) <= strtotime($bookedSlot['to'])){
                $slotIsBooked = ["status" => true, "message" => "Sorry, there's a meeting from ". $bookedSlot['from'] . " to ". $bookedSlot['to']];
                break;
            }

            // check if the closing time ends in someone's slot
            if (strtotime($to) >= strtotime($bookedSlot['from']) && strtotime($to) <= strtotime($bookedSlot['to'])){
                $slotIsBooked = ["status" => true,"message" =>  "Sorry, there's a meeting from ". $bookedSlot['from'] . " to ". $bookedSlot['to']];
                break;
            }

            // check if there's a break between the closing time of one meeting and starting time the next meeting
            // given that timeIntervalBtnMeetings has been specified in the constructor

            if (strtotime($from) >= ( strtotime($bookedSlot['from']) - $this->timeIntervalBtnMeetings)  && strtotime($from) <= ( strtotime($bookedSlot['to']) + $this->timeIntervalBtnMeetings )){
                $slotIsBooked = ["status" => true, "message" =>  "Sorry, there should be ".($this->timeIntervalBtnMeetings / 60)." minutes break away from the ". $bookedSlot['from'] . " to ". $bookedSlot['to']. " meeting"];
                break;
            }

            if (strtotime($to) >= (strtotime($bookedSlot['from']) - $this->timeIntervalBtnMeetings) && strtotime($to) <= ( strtotime($bookedSlot['to']) + $this->timeIntervalBtnMeetings)){
                $slotIsBooked = ["status" => true, "message" =>  "Sorry, there should be ".($this->timeIntervalBtnMeetings / 60)." minutes break away from the ". $bookedSlot['from'] . " to ". $bookedSlot['to']. " meeting"];
                break;
            }

        }

        return $slotIsBooked;
    }

}


/* Test Cases */
//$bookingInstance = new Booking("6:30", "18:00");

try {

    $bookingInstance = new Booking("6:30","17:00");
    var_dump($bookingInstance->bookASlot('7:00', '7:40'));
    var_dump($bookingInstance->bookASlot('10:15', '12:05'));
   // var_dump($bookingInstance->bookASlot('17:00', '18:00')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
   // var_dump($bookingInstance->bookASlot('8:00', '18:00')); // Uncaught exception: Exception Sorry you can't book above a 2 hour slot in ...
    //var_dump($bookingInstance->bookASlot('8:00', '23:00')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
    //var_dump($bookingInstance->bookASlot('12:00', '12:15')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ..
//    var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
//    var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"

    var_dump($bookingInstance->getAllBookings());
} catch (Exception $e){
    echo $e->getMessage();
}

die();
//var_dump($bookingInstance->bookASlot("5:00","9:20"));


//var_dump($bookingInstance->getAllBookings()); // array(1) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } }
//var_dump(); // Uncaught exception: Exception Sorry there is a meeting from 8:00 to 9:30 ...
//var_dump($bookingInstance->bookASlot('8:00', '8:00')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
//var_dump($bookingInstance->bookASlot('8:00', '18:00')); // Uncaught exception: Exception Sorry you can't book above a 2 hour slot in ...
//var_dump($bookingInstance->bookASlot('8:00', '23:00')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
//var_dump($bookingInstance->bookASlot('12:00', '12:15')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
//var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
//var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"
//var_dump($bookingInstance->getAllBookings()); // array(2) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } [1]=> array(2) { ["from"]=> string(5) "12:00" ["to"]=> string(5) "12:15" } }
//
