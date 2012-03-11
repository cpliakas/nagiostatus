
Usage:

// Registers the autoloader.
require_once '../library/Nagiostatus/Loader.php';
Nagiostatus_Loader::register();

// Render the status.dat file as XML.
$parser = new Nagiostatus_Parser('/usr/local/nagios/var/status.dat');
echo $parser->render('xml');

// Uncomment to render as JSON.
// echo $parser->render('json');
