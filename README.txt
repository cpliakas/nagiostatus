
Usage:

// Registers the autoloader.
require_once '../library/Nagiostats/Loader.php';
Nagiostats_Loader::register();

// Render the status.dat file as XML.
$parser = new Nagiostats_Parser('/usr/local/nagios/var/status.dat');
echo $parser->render('xml');

// Uncomment to render as JSON.
// echo $parser->render('json');
