<?php

//Daniel Mantey Mensah contactmantey@gmail.com.
interface BookingStructure
{
    public function bookASlot($from, $to);
}


class Booking implements BookingStructure
{
    private $bookedSlots = [
        ['from'=>'8:00', 'to'=>'9:30']
       
    ];
    public function _construct($openingTime, $closingTime)
    {
        //add code here
    }
    
    public function getAllBookings()
    {
        // add code here
    }

    public function bookASlot($from, $to)
    {
        //add code here
    }
    
    public function getOpeningTime()
    {
        // add code here
    }
    
    public function getClosingTime()
    {
        // add code here
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

