<?php

//<Isaac Gyasi Nimako> <gyasinimako.gh@gmail.com>.
interface BookingStructure
{
    public function bookASlot($from, $to);
}


class Booking implements BookingStructure
{

    private $from;
    private $to;
    
    private $maxMinute = 120;
    private $minMinute = 30;

    private $closingTime;
    private $openingTime;

    private $response = "";

    private $bookedSlots = [
        ['from'=>'8:00', 'to'=>'9:30']
    ];

    public function __construct($openingTime, $closingTime)
    {
      $this->openingTime = $openingTime;
      $this->closingTime = $closingTime;
    }
    
    public function getAllBookings()
    {
      return $this->bookedSlots;
    }
    
    public function bookASlot($from, $to)
    {

      $this->from = $from;
      $this->to   = $to;
      
      $from_hour = $from_minute = 0;
      list($from_hour, $from_minute) = explode(':', $from); 
      
      $to_hour = $to_minute = 0;
      list($to_hour, $to_minute) = explode(':', $to); 

      $booked_from_hour = $booked_from_minute = 0;
      list($booked_from_hour, $booked_from_minute) = explode(':',$this->bookedSlots[0]['from']);

      $booked_to_hour = $booked_to_minute = 0;
      list($booked_to_hour, $booked_to_minute) = explode(':',$this->bookedSlots[0]['to']);

       $booking_hours_from_minute= $this->convertHoursToMinutes($this->from);
       $booking_hours_to_minute= $this->convertHoursToMinutes($this->to);
       
       $bookin_minutes_difference = $booking_hours_to_minute - $booking_hours_from_minute;

       $booking_hour_difference = $to_hour - $booked_from_hour;

      $closingTimeHourTo   =  explode(':',$this->closingTime)['0'];
      $openingTimeHourFrom =  explode(':',$this->openingTime)['0'];

      $booked_from = $this->bookedSlots[0]["from"];
      $booked_to   = $this->bookedSlots[0]["to"];
      
      $this->response= ($to_hour <= $booked_to_hour)?'Sorry there is a meeting from {booked_from} & {$booked_to} ':"";


      if($bookin_minutes_difference===0 || $bookin_minutes_difference <$this->minMinute){ 

         $this->response = "Sorry you can't book less than a 30 min slot <br>";

      }
      if($booking_hour_difference>2 && $to_hour<=$closingTimeHourTo) {

          $this->response = "Sorry you can't book above a 2 hour slot <br>";

      }

      if($to_hour>$closingTimeHourTo){

         $this->response = "Sorry you can't book outside of the closing time <br>";

       }

      if(empty($this->response)){
   
         $this->bookedSlots = [
                             ['from' => $this->from,
                             'to'    => $this->to]
                           ];
          return $this->bookedSlots;
       }else{

          return $this->response;
       } 

    }
    
    public function getOpeningTime()
    {
        return  $this->openingTime;
    }
    
    public function getClosingTime()
    {
        return  $this->closingTime;
    }


    public function convertHoursToMinutes($time){

            $minutes = 0;
            $time = explode(":", $time); 

            $hours = $time[0]; 
            if(!empty($time[1])) { 
                $m = $time[1]; 
            }else{ 
                $minutes = "00"; 
            } 
            $mm = ($hours * 60)+$minutes; 
            return $mm; 
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


