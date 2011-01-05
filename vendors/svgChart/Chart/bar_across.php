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

class Chart_Bar_across extends PEAR {
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
		$x = $options["x"] ? $options["x"] : 50;
		$y = $options["y"] ? $options["y"] : 80;
		$data = $this->chart->data;
		$output = <<<DEFS
		<defs>
    <filter id="flt">
      <feGaussianBlur in="SourceAlpha" stdDeviation="0.5" result="blur"/>
      <feSpecularLighting in="blur" surfaceScale="5" specularConstant="0.5"
        specularExponent="10" result="specOut" style="lighting-color: #FFF">
        <fePointLight x="-5000" y="-5000" z="5000"/>
      </feSpecularLighting>
      <feComposite in="specOut" in2="SourceAlpha" operator="in" result="specOut2"/>
      <feComposite in="SourceGraphic" in2="specOut2" operator="arithmetic"
        k1="0" k2="1" k3="1" k4="0"/>
    </filter>
  </defs>
DEFS;
		$count = 0;
		$barx = $x + 100;
		$descx = $x + 200;
		foreach($data as $obj) {
			$color = $this->chart->getColor($count);
			$texty = $y + 11;
			$width = $obj->percent;
			$percent = number_format($obj->percent, 2, ",", ".");
			if($options["animated"] === false) {
				
$output .= <<<ENDE
				<rect x="$barx" y="$y" width="$width" height="15" fill="$color" style="filter: url(#flt)" />
				<text x="$x" y="$texty" style="font-size: 12px; text-anchor: right">{$obj->desc}</text>
				<text x="$descx" y="$texty" style="font-size: 12px; text-anchor: right;">[$percent%]</text>
ENDE;
			} else {
$output .= <<<ENDE
				<rect x="$barx" y="$y" width="0" height="15" fill="$color" style="filter: url(#flt)">
					<animate attributeName="width" attributeType="XML" begin="0s" dur="1s" fill="freeze" from="0" to="$width"/>
				</rect>
				<text x="$x" y="$texty" style="font-size: 12px; text-anchor: right">{$obj->desc}</text>
				<text x="$descx" y="$texty" style="font-size: 12px; text-anchor: right; visibility: hidden">[$percent%]
					<animate attributeName="visibility" attributeType="CSS" begin="1s" dur="0.1s" fill="freeze" from="hidden" to="visible" calcMode="discrete"/>
				</text>
ENDE;
			}
			$y = $y + 27;
			$count++;
		}
		
		if(isset($this->options["legend"])) {
			unset($this->options["legend"]);
		}
		return $output;
	}
}

?>