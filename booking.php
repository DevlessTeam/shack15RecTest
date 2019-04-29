<?php

/**
 * @author Oliver Boamah <oliverboamah@yahoo.com>
 */
interface BookingStructure
{
    function bookASlot($from, $to);
}

/**
 * This booking class is used to handle the booking of a conference room within a co-working space
 */
class Booking implements BookingStructure
{
    /**
     * Contains an array of all the booked slots
     *
     * @var array
     */
    private $bookedSlots = [
        ['from' => '8:00', 'to' => '9:30'],

    ];

    /**
     * Contains the opening time of the conference room
     *
     * @var [string]
     */
    private $openingTime;

    /**
     * Contains the closing time of the conference room
     *
     * @var [string]
     */
    private $closingTime;

    /**
     * Create a new booking
     *
     * @param [string] $openingTime time conference room opens
     * @param [string] $closingTime time conference room closes
     * @return void
     */
    public function __construct($openingTime, $closingTime)
    {
        $this->openingTime = $openingTime;
        $this->closingTime = $closingTime;
    }

    /**
     * Get all bookings
     *
     * @return array
     */
    public function getAllBookings()
    {
        return $this->bookedSlots;
    }

    /**
     * Book a new slot
     *
     * @param [string] $from when to start using the room
     * @param [string] $to when to stop using the room
     * @throws Exception when the booking does not obey the rules
     * @return void
     */
    public function bookASlot($from, $to)
    {
        // get time difference as a number
        $timeDifference = $this->getTimeDifference($from, $to);

        // run validations
        if ($this->timeToNumber($from) < $this->timeToNumber($this->openingTime)) {
            // cannot book outside opening time
            throw new Exception("Exception Sorry you can't book outside of the opening time");
        } else if ($this->timeToNumber($to) > $this->timeToNumber($this->closingTime)) {
            // cannot book outside closing time
            throw new Exception("Exception Sorry you can't book outside of the closing time");
        } else if ($timeDifference < 0.0) {
            // closing time should be greater than opening time
            throw new Exception("Exception Sorry your booking's closing time should not be less than it's opening time");
        } else if ($timeDifference > 2.0) {
            // max booking hrs should not be greater than 2 hrs
            throw new Exception("Exception Sorry you can't book above a 2 hour slot");
        } else if ($timeDifference < 0.5) {
            // min booking hrs should not be less than 0.5 hrs
            throw new Exception("Exception Sorry you can't book less than a 30 min slot");
        }

        // checks whether there will be a meeting during your booking time
        $this->checkExistingMeetings($from);

        // add new booking if all rules are obeyed, i.e no exceptions thrown
        $newBooking = array(
            'from' => $from,
            'to' => $to,
        );
        array_push($this->bookedSlots, $newBooking);
    }

    /**
     * Get opening time of conference room
     *
     * @return string
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }

    /**
     * Get closing time of conference room
     *
     * @return string
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Get time difference as a number between two times in a 'HH:MM' format
     *
     * @param [string] $openingTime
     * @param [string] $closingTime
     * @return number
     */
    private function getTimeDifference($openingTime, $closingTime)
    {
        // convert time format from "HH:MM" to HH.MM
        $openingTime = $this->timeToNumber($openingTime);
        $closingTime = $this->timeToNumber($closingTime);

        return $closingTime - $openingTime;
    }

    /**
     * Converts a time in string to a number
     *
     * @param [string] $time
     * @return number
     */
    private function timeToNumber($time)
    {
        return (strtotime($time) / 60) / 60;
    }

    /**
     * Checks whether there will be a meeting during your booking time
     *
     * @param [string] $from when to start using the room 
     * @throws Exception when there's a meeting during your booking time
     * @return void
     */
    private function checkExistingMeetings($from)
    {
        foreach ($this->bookedSlots as $bookedSlot) {

            $from = $this->timeToNumber($from);

            if ($from >= $this->timeToNumber($bookedSlot['from']) &&
                $from < $this->timeToNumber($bookedSlot['to'])) {
                    throw new Exception("Exception Sorry there is a meeting from " . $bookedSlot['from'] . " to " . $bookedSlot['to']);
            }
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
var_dump($bookingInstance->bookASlot('12:00', '12:15')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"
var_dump($bookingInstance->getAllBookings()); // array(1) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } } 
