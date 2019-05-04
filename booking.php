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

    /**
     * The opening time as Unix timestamp
     *
     * @var int
     */
    private $openingTime;

    /**
     * The closing time as Unix timestamp
     *
     * @var int
     */
    private $closingTime;

    /**
     * The current or last booked slot
     *
     * @var array
     */
    private $lastBooking;

    public function __construct($openingTime, $closingTime)
    {
        $this->openingTime = strtotime($this->convertTime($openingTime));
        $this->closingTime = strtotime($this->convertTime($closingTime));

        $this->lastBooking = end($this->bookedSlots);

    }
    
    public function getAllBookings()
    {
        // add code here
    }

    public function bookASlot($from, $to)
    {
        $last_booking_opening_time = strtotime($this->convertTime($this->lastBooking['from']));
        $last_booking_closing_time = strtotime($this->convertTime($this->lastBooking['to']));

        $booked_opening_time = strtotime($this->convertTime($from));
        $booked_closing_time = strtotime($this->convertTime($to));

    }
    
    public function getOpeningTime()
    {
        // add code here
    }
    
    public function getClosingTime()
    {
        // add code here
    }

    /** Convert time to either AM or PM
     * @param $time
     * @return false|string
     */
    public function convertTime($time){
        return date('h:i a', strtotime($time));
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

