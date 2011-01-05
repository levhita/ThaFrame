<?PHP
//header("Content-Type: image/svg+xml");
/**
* Test case for the {@link Chart} class.
* @package Chart_Tests
*/
require_once("Chart.php");

PEAR::setErrorHandling(PEAR_ERROR_DIE);


$data = array(new Chart_Data(50, "Kleiner"), new Chart_Data(150, "Mittel"), new Chart_Data(250, "Sehr gro"), new Chart_Data(20, "Furchtbar tiny"),);

$chart = new Chart($data);
$content = $chart->get("pie", array("x" => 100, "y" => 100, "legend" => ""));
$content .= $chart->get("bar_across", array("x" => 500, "y" => 200));
$content = $chart->encapsulate($content);

$fh = fopen("temp.svg", "w");


fwrite($fh, $content);
fclose($fh);
//echo $content;

//<iframe src="temp.svg" width="1300" height="450">
//</iframe> 
?>
<embed src="temp.svg" width="1300" height="450" type="image/svg-xml">