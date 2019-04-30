<?php

//<Oliver Mensah> <olivermensah96@gmail.com>.
interface BookingStructure
{   
    //The interface method with private modifier prevents from being access its during implementation, hence making it public   
    function bookASlot($from, $to);
}


class Booking implements BookingStructure
{
    private $bookedSlots = [
        ['from'=>'8:20', 'to'=>'9:30']
    ];

    //added these properties to hold reference to the opening and closing time.
    private $openingTime;
    private $closingTime;

    const THIRTYMINDIFF = 30; // 7:10 - 7:40 => 740 - 710 => 30
    const TWOHRDIFF = 200; // 7:00 - 9:00 => 900 - 700 => 200

    public function __construct($openingTime, $closingTime)
    {
        if(!$this->isTimeValid($openingTime))
        {
            throw new Exception('Invalid Opening Time');
        }
        $this->openingTime = $openingTime;
    
        if(!$this->isTimeValid($closingTime))
        {
            throw new Exception("Invalid Closing Time");
        }
        $this->closingTime = $closingTime;
    }
    
    public function getAllBookings()
    {
        return $this->bookedSlots;
    }
    
    public function bookASlot($from, $to)
    {
        if(!$this->isTimeValid($from))
        {
            throw new Exception('Invalid Start Time for booking');
        }
        if(!$this->isTimeValid($to))
        {
            throw new Exception("Invalid Closing Time for booking");
        }

        if($this->timeDifference($from, $to) < self::THIRTYMINDIFF){
            throw new Exception("Sorry you can't book less than 30 minutes ...");
        }
        if($this->timeDifference($from, $to) < self::THIRTYMINDIFF){
            throw new Exception("Sorry you can't book less than 30 minutes ...");
        }
        if($this->timeDifference($this->getOpeningTime(), $from) < 0){
            throw new Exception("Sorry you can't book outside of the opening time ...");
        }
        if($this->timeDifference($this->getClosingTime(), $to) > 0){
            throw new Exception("Sorry you can't book outside of the closing time ...");
        }
        if($this->timeDifference($from, $to)> self::TWOHRDIFF){
            throw new Exception("Sorry you can't book above a 2 hour slot in ...");

        }

        foreach ($this->bookedSlots as $slot) {
            if($slot["from"] == $from){
                throw new Exception("Sorry Arealdy booked ...");
            }
            if($slot["to"] == $to){
                throw new Exception("Sorry Arealdy booked ...");
            }
            $this->checkDateIntervalValidity($from, $to, $slot);
        }

        $this->bookedSlots[] = ["from"=>$from, "to"=>$to];
    }
    
    public function getOpeningTime()
    {
        return $this->openingTime;
    }
    
    public function getClosingTime()
    {
        return $this->closingTime;
    }
    /**
     * Checks the validity of a given time
     * @param string $time   the given time
     * @return boolean
     */
    private function isTimeValid($time)
    {
        if(empty($time)) return  false;
        $timeDestructuring = explode(":", $time);
        if(count($timeDestructuring) != 2) return false;
        if(strlen($timeDestructuring[0]) >3 )return false;
        if(strlen($timeDestructuring[1]) != 2) return false;
        return true;
    }

     /**
     * Finds difference between two given time
     * @param string $first  
     * @param string $second 
     * @return int
     */
    private function timeDifference($first, $second)
    {
        $first = str_replace(":", "", $first);
        $second = str_replace(":", "", $second);
        return (int)$second - (int)$first ;
    }
    /**
     * Gets hours and minutes from a given time
     * @param string $time  
     * @return array
     */
    private function getTime($time)
    {
        $timeDestructuring = explode(":", $time);
        return array("hours" => $timeDestructuring[0], "minutes"=> $timeDestructuring[1]);
    }
    /**
     * finds the time interval validity given
     * @param string $from  
     * @param string $to 
     * @param string $current
     */
    private function checkDateIntervalValidity($from, $to, $current){
        //the time for booking
        $fromHours = $this->getTime($from)["hours"];
        $fromMinutes = $this->getTime($from)["minutes"];
        $toHours = $this->getTime($to)["hours"];        
        $toMinutes = $this->getTime($to)["minutes"];

        //current time at a given index of the data store
        $currentFromHours = $this->getTime($current["from"])["hours"];
        $currentFromMinutes = $this->getTime($current["from"])["minutes"];
        $currentToHours = $this->getTime($current["to"])["hours"];        
        $currentToMinutes = $this->getTime($current["to"])["minutes"];
        
        if($fromHours == $currentFromHours && $fromMinutes > $currentFromMinutes && $fromMinutes < $toMinutes  ) 
        {
            throw new Exception("Sorry you can't book in between booked hours");
        }

        if($fromHours == $currentFromHours && $fromMinutes > $currentFromMinutes && $this->timeDifference($this->getTime($to), $to) > 0){
            throw new Exception("Sorry you can't book in between booked hours");
        }

    }
}


/* Test Cases */
$bookingInstance = new Booking("6:30", "18:00");
var_dump($bookingInstance->getAllBookings()); // array(1) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } }
var_dump($bookingInstance->bookASlot('8:00', '8:30')); // Uncaught exception: Exception Sorry there is a meeting from 8:00 to 9:30 ...
var_dump($bookingInstance->bookASlot('8:00', '8:00')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
var_dump($bookingInstance->bookASlot('8:00', '18:00')); // Uncaught exception: Exception Sorry you can't book above a 2 hour slot in ...
var_dump($bookingInstance->bookASlot('8:00', '23:00')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
var_dump($bookingInstance->bookASlot('12:00', '12:15')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"
var_dump($bookingInstance->getAllBookings()); // array(2) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } [1]=> array(2) { ["from"]=> string(5) "12:00" ["to"]=> string(5) "12:15" } }

