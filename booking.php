<?php

//Daniel Mantey Mensah contactmantey@gmail.com.
interface BookingStructure
{
    public function bookASlot($from, $to);
}


class Booking implements BookingStructure
{
    private $bookedSlots = [
        ['from' => '8:00', 'to' => '9:30']

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


    public function __construct($openingTime, $closingTime)
    {
        @set_exception_handler(array($this, 'handleException'));

        $this->openingTime = strtotime($this->convertTime($openingTime));
        $this->closingTime = strtotime($this->convertTime($closingTime));

    }

    public function getAllBookings()
    {
        return $this->bookedSlots;
    }

    public function bookASlot($from, $to)
    {
        $last_booking = end($this->bookedSlots);

        $last_booking_opening_time = strtotime($this->convertTime($last_booking['from']));
        $last_booking_closing_time = strtotime($this->convertTime($last_booking['to']));

        $booked_opening_time = strtotime($this->convertTime($from));
        $booked_closing_time = strtotime($this->convertTime($to));

        $booked_minutes = abs($booked_opening_time - $booked_closing_time) / 60;
        $booked_hours = abs($booked_minutes) / 60;

        if ($booked_opening_time < $this->openingTime) {
            throw new Exception("Exception Sorry you can't book outside of the opening time.");
        }

        if ($booked_closing_time > $this->closingTime) {
            throw new Exception("Exception Sorry you can't book outside of the closing time.");
        }

        if ($booked_hours > 2) {
            throw new Exception("Exception Sorry you can't book above a 2 hours slot.");
        }

        if ($booked_minutes < 30) {
            throw new Exception("Exception Sorry you can't book less than a 30 minutes slot.");
        }

        if ($booked_opening_time < $last_booking_closing_time) {
            throw new Exception("Exception Sorry there is a meeting from " . $last_booking['from'] .
                " to " . $last_booking['to']);
        }

        $new_booking = [
            'from' => $from,
            'to' => $to
        ];

        array_push($this->bookedSlots, $new_booking);

    }

    public function getOpeningTime()
    {
        return date('H:i', $this->openingTime);
    }

    public function getClosingTime()
    {
        return date('H:i', $this->closingTime);
    }

    /**
     * Convert time to either AM or PM
     * @param $time
     * @return false|string
     */
    public function convertTime($time)
    {
        return date('h:i a', strtotime($time));
    }

    /**
     * Set a top level exception handler to handle all uncaught exception
     * @param $e
     */
    public function handleException($e)
    {
        echo "Uncaught exception: " . $e->getMessage();
    }

}


/* Test Cases */
$bookingInstance = new Booking("6:30", "18:00");
//var_dump($bookingInstance->getAllBookings()); // array(1) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } }
//var_dump($bookingInstance->bookASlot('8:00', '8:30')); // Uncaught exception: Exception Sorry there is a meeting from 8:00 to 9:30 ...
//var_dump($bookingInstance->bookASlot('8:00', '8:00')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
//var_dump($bookingInstance->bookASlot('8:00', '18:00')); // Uncaught exception: Exception Sorry you can't book above a 2 hour slot in ...
//var_dump($bookingInstance->bookASlot('8:00', '23:00')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
//var_dump($bookingInstance->bookASlot('24:00', '12:15')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
//var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
//var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"

$bookingInstance->bookASlot('9:30', '10:30');
$bookingInstance->bookASlot('10:30', '12:25');
$bookingInstance->bookASlot('12:10', '13:00');

var_dump($bookingInstance->getAllBookings()); // array(2) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } [1]=> array(2) { ["from"]=> string(5) "12:00" ["to"]=> string(5) "12:15" } }


