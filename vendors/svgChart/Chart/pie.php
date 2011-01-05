<?PHP
/**
* Class file for {@link Chart_Pie}
* @package Chart
* @author Jonas Schneider <JonasSchneider@gmx.de>
*/

/**
* This is a pie visualization class. 
* You shouldn´t use this class alone, but you can.
* You should only use it in corporation with the {@link Chart} class.
* @see Chart_Pie::get()
* @package Chart
*/

class Chart_Pie extends PEAR {
	/**
	* This function generates a pie chart of the given data.
	* It uses the technology provided by the {@link Chart} class to generate a pie chart from the given data.<br>
	* You can pass the following options to the this method  by using {@link Chart::get()}:<br>
	* * cx & cy - The coordinates of the center point of the pie chart.<br>
	* * r - The radius of the pie chart.
	* @param array An array of options, see {@link Chart::get()}.
	* @return string
	* @see Chart::get()
	*/
	function get($options) {
		$cx = $options["cx"] ? $options["cx"] : 200;
		$cy = $options["cy"] ? $options["cy"] : 200;
		$r = $options["r"] ? $options["r"] : 150;
		$x1 = $cx;
		$y1 = $cy - $r;
		$alpha = 0;
		$output = "";
		$count = 0;
		
		$data = $this->chart->data;
		$sum = 0;
		foreach($data as $obj) {
			$sum += $obj->value;
		}
		$colors = $this->chart->colors;
		
		foreach($data as $obj) {
			$alpha = $alpha + ($obj->percent / 100 * (2*M_PI));
			
			$x2 = $cx + ($r*sin($alpha));
			$y2 = $cy - ($r*cos($alpha));
			
			$over180 = $obj->percent > 50 ? "1" : "0"; 
			
			$color = $this->chart->getColor($count);
			
			$output .= "<path d='M{$cx},{$cy} L$x1,$y1 A{$r},{$r} 0 $over180,1 $x2,$y2 Z' fill='$color' opacity='0.6'/>\n\n";
			
			$x1 = $x2;
			$y1 = $y2;
			$count++;
		}
		
		if(isset($this->options["legend"])) {
			$x = $cx + $r * 1.2;
			$y = $cy - $r;
			$this->options["legend"]["x"] = $x;
			$this->options["legend"]["y"] = $y;
		}
		
		return $output;
	}
}

?>