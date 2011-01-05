<?PHP
/*
* The main Chart package file. It includes the core of all Chart classes.
* @package Chart
* @author Jonas Schneider <JonasSchneider@gmx.de>
*/
require_once("PEAR.php");

/**
* The main Chart class. Base class for all subtypes of charts, like Pie, Bar, Line and so on.
* @package Chart
* @see Chart_Data
*/
class Chart extends PEAR {
	/**
	* Array for storing the data to visualize.
	* @var array
	* @access private
	*/
	var $data;
	
	/**
	* Array for storing the colors to visualize the data defined in {@link $data}.
	* @var array
	* @access public
	* @see Chart::$data
	*/	
	var $colors;
	
	/**
	* The constructor.
	* You can pass it an array of {@link Chart_Data} objects as data, 
	* the name of the visualization 
	* and, optional, an array contents the colors in what the Chart data is visualized.
	* @param array {@link Chart_Data) objects
	* @param string The name of the standard visualization method.
	* @param array Colors to display the data in.
	* @return void
	*/
	function Chart($data = false, $standard_name = "", $colors = false) {
		$this->PEAR();
		if(!$colors)
			$colors = array("red", "yellow", "blue", "orange");
		
		$this->colors = $colors;
		$this->standard_name = $standard_name;
		$this->setData($data);
	}
	
	/**
	* This function sets the {@link Chart::$data} array to a new value.
	* @param array New Value for {@link Chart::$data}
	* @return array Old Value of {@link Chart::$data}
	*/
	function setData($data) {
		$tmp = $this->data;
		
		$sum = 0;
		foreach($data as $obj) {
			$sum += $obj->value;
		}
		
		foreach($data as $key => $obj) {
			$obj->percent = $obj->value / $sum * 100;
			$this->data[$key] = $obj;
		}
	}
	
	/**
	* This function returns the svg code for the visualized form of the internal data.
	* It receives the name of the visualization class to use.
	* As $options, you can pass an array of options forwarded to the visualization class.<br>
	* The following options are also implemented in this function:<br>
	* * legend - If set, a legend is also generated. The value is also an array passed to the 
	* {@link Chart::makeLegend()} function.
	* @param string Class to use
	* @param array Options, passed to the chart class
	* @return string SVG code
	*/
	function get($name = false, $options = false) {
		if(!$this->data) return PEAR::raiseError("No \$data set!");
		if(!$name) $name = $this->standard_name;
		if(!$name) return PEAR::raiseError("No visualization name set!");
		if(!$options) $options = array();
		$name = strtolower($name);
		$filename = "Chart/$name.php";
		if(!file_exists($filename)) return PEAR::raiseError("Class file for visualization '$name' doesn´t exist.");
		require_once($filename);
		
		$class_name = "Chart_$name";
		if(!class_exists($class_name)) return PEAR::raiseError("Class '$name' doesn´t exists after including!");
		
		$chart = new $class_name;
		$chart->chart = &$this;
		
		$chart->options = $options;
		$content = $chart->get($options);
		$options = $chart->options;
		
		if(isset($options["legend"])) {
			$content .= $this->makeLegend($options["legend"]);
		}
		return $content;
	}
	
	/**
	* This function does the same as {@link get()}, with one difference:
	* The returned svg code is capsulated in a <svg>....</svg> element structure, so it returns a completely SVG document.
	* @param string Class to use
	* @param array Options, passed to the chart visulaization class
	*/
	function makeSVG($name = false, $options = false) {
		return encapsulate($this->get($name, $options));
	}
	
	/**
	* This is an internal function used by the visualization classes to make a legend to the various chart types.
	* It uses the internal {@link $data} structure.<br>
	* You can pass the following options:<br>
	* * x & y - X & Y coordinates of the top-left point of the legend
	* @param array Options passed
	* @return string SVG code for a legend.
	*/
	function makeLegend($options = false) {
		$x = $options["x"] ? $options["x"] : 200;
		$y = $options["y"] ? $options["y"] : 200;
		$width = $options["width"] ? $options["width"] : 100;
		$height = $options["height"] ? $options["height"] : 200;
		
		# Frame
		$svg = "<rect x='$x' y='$y' width='$width' height='$height' fill='none' stroke='black' />";
		
		$y = $y + 5;
		$x = $x + 5;
		$count = 0;
		$colors = $this->colors;
		$data = $this->data;
		foreach($data as $obj) {
			$texty = $y + 15;
			$textx = $x + 20;
			$color = $colors[$count % count($colors)];
			$svg .= "<rect x='$x' y='$y' width='15' height='15' fill='$color' />\n";
			$svg .= "<text x='$textx' y='$texty'>{$obj->desc}</text>\n";
			$y += 20;
			$count++;
		}
		
		return $svg;		
	}
	
	/**
	* This function simply returns a color from the internal coller palette.
	* Supplied is a number.
	* @param integer The id of the color
	* @return string color name or hexadeciaml triplet
	*/
	function getColor($id) {
		$color = $this->colors[$id % count($this->colors)];
		return $color;
	}
	
	/**
	* This function simply enclosoures the received svg code with the beginning- and ending <svg> or </svg> tags.
	* Also it includes an <?xml ... ?> header.
	* @param string SVG code to encapsulate
	* @return string The encapsulated SVG code
	*/
	function encapsulate($svg) {
		$data = "<?xml version=\"1.0\" standalone=\"yes\"?>\n";
		$data .= "<svg version=\"1.1\" baseProfile=\"full\" xmlns=\"http://www.w3.org/2000/svg\"
		xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:ev=\"http://www.w3.org/2001/xml-events\">";
		$data .= "\n$svg\n</svg>";
		return $data;
	}
}


/**
* This class represents an object in a chart, i.e. a line in a line diagram, a piece of pie in a 
* pie chart and so on.
* @package Chart
*/
class Chart_Data extends PEAR {
	/**
	* Description of the data object.
	* @var string
	* @access public
	*/
	var $desc;
	
	/**
	* Value of the data object.
	* @var float
	* @access public
	*/
	var $value;
	
	/**
	* The constructor.
	* It receives the description o the data, not needed, but for some chart types useful,
	* and, as a float, the value of the data.
	* @param string Description
	* @param float Value
	* @return void
	*/
	function Chart_Data($value, $desc = "") {
		$this->PEAR();
		
		$this->desc = $desc;
		$this->value = $value;
	}
}
	