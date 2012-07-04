<?php

class Nisanth_Model_Color
{
	
	/**
	 * Get a random color
	 * 
	 * @return string
	 */
	public function getRandomHtmlColor()
	{
		$red   = rand(0,255);
		$green = rand(0,255);
		$blue  = rand(0,255);
    	return $this->getHtmlColor($red, $green, $blue);
	}

	/**
	 * Get a html color
	 *
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @return string
	 */
	public function getHtmlColor($red, $green, $blue)
	{
		$color = '#' . $this->_getHex($red)
					 . $this->_getHex($green)
				 	 . $this->_getHex($blue);
		return $color;
	}
	
	/**
	 * Get a hex number of a fixed length
	 *
	 * @param int $number
	 * @param int $digits
	 * @return string
	 */
	protected function _getHex($number, $digits = 2)
	{
		return substr(str_repeat('0', $digits) . dechex($number), - $digits);
	}
	
}