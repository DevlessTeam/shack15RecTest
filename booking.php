<?php

//<Maclean Akanluk Ayarik> <ayarikmaclean@yahoo.com>.

interface BookingStructure
{
    public function bookASlot($from, $to);
}


class Booking implements BookingStructure
{
    /**
     * Booked time slots.
     *
     * @var array
     */
    private $bookedSlots = [
        ['from' => '8:00', 'to' => '9:30']
    ];

    /**
     * Opening time of the conference room.
     *
     * @var string
     */
    private $openingTime;

    /**
     * Closing time of the conference room.
     *
     * @var string
     */
    private $closingTime;

    /**
     * Minimum booking time.  30 minutes is 1800 seconds
     *
     * @var int
     */
    private const MIN_BOOKING_TIME = 1800;

    /**
     * Maximum booking time.  2 hours is 7200 seconds.
     *
     * @var int
     */
    private const MAX_BOOKING_TIME = 7200;

    /**
     * Class constructor.
     * 
     * @param string    $openingTime   Conference room opening time.
     * @param string    $closingTime   Conference room closing time.
     */
    public function __construct($openingTime, $closingTime)
    {
        $this->openingTime = $openingTime;
        $this->closingTime = $closingTime;
    }

    /**
     * Gets all recorded bookings.
     *
     * @return array    List of booked time slots
     */
    public function getAllBookings()
    {
        return $this->bookedSlots;
    }

    /**
     * Booking a time slot.
     *
     * @param string    $from   Bookings starting time.
     * @param string    $to     Bookings ending time.
     */
    public function bookASlot($from, $to)
    {
        if ($this->isValid($from, $to)) {
            $this->bookedSlots[] = ['from' => $from, 'to' => $to];
        }
    }

    /**
     * Gets the opening time of the conference room.
     *
     *  @return string    The opening time.
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }

    /**
     * Gets the closing time  of the conference room.
     *
     *  @return string    The closing time.
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Checks booking time for validity and throws exception if errors occur.
     *
     * @param string      $fromTime      Bookings starting time.
     * @param string      $toTime        Bookings ending time.
     * @return bool       To confirm validation
     * @throws Exception  Provided start time and end time of bookings not properly validated
     */
    private function isValid($from, $to)
    {
        $fromTime = strtotime($from);
        $toTime = strtotime($to);
        $openingTime = strtotime($this->openingTime);
        $closingTime = strtotime($this->closingTime);
        $timeDifference = $toTime - $fromTime;

        if (($fromTime < $openingTime) || ($fromTime >= $closingTime)) {
            throw new Exception("Exception Sorry you can't book outside of the closing time");
        }

        if (($toTime < $openingTime) || ($toTime > $closingTime)) {
            throw new Exception("Exception Sorry you can't book outside of the closing time");
        }

        if ($timeDifference < self::MIN_BOOKING_TIME) { //If less than minimum time throw exception.
            throw new Exception("Exception Sorry you can't book less than a 30 min slot");
        }

        if ($timeDifference > self::MAX_BOOKING_TIME) { //If more than maximum time  throw exception.
            throw new Exception("Exception Sorry you can't book above a 2 hour slot in");
        }

        //Check if time slot is not taken.
        $this->isSlotTaken($fromTime);

        return true;
    }

    /**
     * Checks if conference room is availble for the time slot.
     *
     * @param string      $fromTime      Booking starting time.
     * @throws Exception  If booking time slot is not available
     */
    private function isSlotTaken($fromTime)
    {
        foreach ($this->bookedSlots as $bookedTime) {
            $bookedFromTime = strtotime($bookedTime['from']);
            $bookedToTime = strtotime($bookedTime['to']);

            if (($fromTime >= $bookedFromTime) && ($fromTime <= $bookedToTime)) {
                throw new Exception("Exception Sorry there is a meeting from " . $bookedTime['from'] . " to " . $bookedTime['to']);
            }
        }
    }
}


/* Test Cases */
$bookingInstance = new Booking("6:30", "18:00");
echo "<pre>";
var_dump($bookingInstance->getAllBookings()); // array(1) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } }
var_dump($bookingInstance->bookASlot('8:00', '8:30')); // Uncaught exception: Exception Sorry there is a meeting from 8:00 to 9:30 ...
var_dump($bookingInstance->bookASlot('8:00', '8:00')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
var_dump($bookingInstance->bookASlot('8:00', '18:00')); // Uncaught exception: Exception Sorry you can't book above a 2 hour slot in ...
var_dump($bookingInstance->bookASlot('8:00', '23:00')); // Uncaught exception: Exception Sorry you can't book outside of the closing time ...
var_dump($bookingInstance->bookASlot('12:00', '12:15')); // Uncaught exception: Exception Sorry you can't book less than a 30 min slot ...
var_dump($bookingInstance->bookASlot('12:00', '12:30'));
var_dump($bookingInstance->getOpeningTime()); // string(4) "6:30"
var_dump($bookingInstance->getClosingTime()); // string(5) "18:00"
var_dump($bookingInstance->getAllBookings()); // array(2) { [0]=> array(2) { ["from"]=> string(4) "8:00" ["to"]=> string(4) "9:30" } [1]=> array(2) { ["from"]=> string(5) "12:00" ["to"]=> string(5) "12:30" } }
