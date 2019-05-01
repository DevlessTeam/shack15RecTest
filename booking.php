<?php

//<George Padmore Yeboah> <padmorey@gmail.com>.
interface BookingStructure
{
    public function bookASlot($from, $to);
}


class Booking implements BookingStructure
{
    private $bookedSlots = [
        ['from'=>'8:00', 'to'=>'9:30']
       
    ];
    private $booking_openingTime, $booking_closingTime;

    private $minMinutes = 30; //30 minutes;
    private $maxMinutes = 120; //2 hours (converted to minutes.) NB: 1hr = 60 minutes. 


    public function __construct($openingTime, $closingTime)
    {
        //add code here
        $this->booking_openingTime = $openingTime; 
        $this->booking_closingTime = $closingTime; 
    }
    
    public function getAllBookings()
    {
        // add code here
        return $this->bookedSlots;
    }
    
    public function bookASlot($from, $to)
    {
        
        //add code here
        $bookedSlotsFrom = "";
        $bookedSlotsTo = "";
        $message = "";
        $totalTimeInMinutes = 0;

        $from = preg_replace("([^0-9:])", "", $from);//Validate to accept only allowed characters and inputs
        $to = preg_replace("([^0-9:])", "", $to);//Validate to accept only allowed characters and inputs

        #get the number of minutes this booking is.
        $totalTimeInMinutes = abs((new \DateTime($from))->getTimestamp() - (new \DateTime($to))->getTimestamp()) / 60;

        try {
 
            #check new booking against the minimum and maximum time.
            if ($totalTimeInMinutes < $this->minMinutes){
                throw new Exception("Sorry you can't book less than a 30 min slot<br>");
            }
    
            //if booking time falls within the accepted 2 hours.
            if ($totalTimeInMinutes > $this->maxMinutes){
                throw new Exception("Sorry you can't book above a 2 hour slot in<br>");
            } 

            
            foreach ($this->bookedSlots as $key => $value) {
            
                $bookedSlotsFrom = $value["from"];
                $bookedSlotsTo = $value["to"];

                //check if booking slot is available for this new booking time.
                if (($value["from"] <= $from) && ($to <= $value["to"])) {
                    throw new Exception("Sorry there is a meeting from $bookedSlotsFrom to $bookedSlotsTo <br>");
                }else{
                    $newSlot = ['from'=>$from, 'to'=>$to];
                    array_push($this->bookedSlots, $newSlot);//can now add to booked slots.
                }

            }
        }
        catch(Exception $e) {        
            echo 'Uncaught exception: Exception ' .$e->getMessage();
        }

        return true;
    }
    
    public function getOpeningTime()
    {
        return $this->booking_openingTime;
    }
    
    public function getClosingTime()
    {
        return $this->booking_closingTime;
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

