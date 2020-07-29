<?php


namespace App\Helper;
use App\Helper\JDate;

class DateHelper {

	static function toLong($date) {
			$format =  'd-m-Y H:i:s' ;
			$d=new JDate($date);
			
			return $d->format($format);
	}

	
	static function toShort($date) {
	    $format =  'd-m-Y' ;
	    $d=new JDate($date);
	    
	    return $d->format($format);
	}
	
	
	function getOffsetDay($count,$start){

		$date = $start + $count*24*60*60;
		return $date;
	}
	public static function  getCountDay($start,$end){

		//$start = strtotime($start);
		$start = new JDate($start);
		$end = new JDate($end);
		//$end = strtotime($end);
		//$days_between = ceil(abs($end - $start) / 86400);
		$days = $start->diff($end);
		return $days->days;
		//return $days_between;
	}
	public static function dateBeginDay($date, $tzoffset = 0)
	{
		$day = date('Y-m-d',$date);

		$date = strtotime($day.' 00:00:00');
		return $date;
	}
	public static function dateBeginWeek($date, $tzoffset = 0)
	{
		$date = strtotime('last Monday',$date);

		return $date;
	}
	public static function dateEndWeek($date, $tzoffset = 0)
	{
		$date = strtotime('next Sunday',$date);
		return $date;
	}
	function startMonth($m,$y){
		$date = date('Y-m-d H:i:s',mktime(0,0,0,$m,01,$y));
		return $date;

	}
	function endMonth($d,$m,$y){
		$date = date('Y-m-d H:i:s',mktime(23,59,59,$m,$d,$y));
		return $date;
	}
	public static function dateBeginMonth($date, $tzoffset = 0)
	{
		$fromdate = date('01-m-Y 00:00:00',$date);
		//$date = strtotime('first day this month',$date);
		$fromdate = strtotime($fromdate);
		return $fromdate;
	}
	public static function dateEndMonth($date, $tzoffset = 0)
	{
		$todate = date('t-m-Y 23:59:59',$date);

		$todate = strtotime($todate);
		return $todate;
	}



	public static function dateEndDay($date, $tzoffset = 0)
	{
		$date = date('Y-m-d',$date);
		$date = strtotime($date.' 23:59:59');
		return $date;
	}



	/**
	 * @param int $hourformat
	 * @return number
	 */
	static function getTime($hourformat)
	{
		$number = 0;
		$datearr=explode(':', $hourformat);
		if(isset($datearr[0])){
			$number = $number+$datearr[0]*60*60;
		}

		if(isset($datearr[1])){
			$number = $number+$datearr[1]*60;
		}

		if(isset($datearr[2])){
			$number = $number+$datearr[2];
		}
		return $number;
	}

}