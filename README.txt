
Usage:

// Registers the autoloader.
require_once '../library/Nagiostats/Loader.php';
Nagiostats_Loader::register();

// Render the status.dat file as XML.
$status = new Nagiostats('/usr/local/nagios/var/status.dat');
echo status->render('xml');

// Uncomment to render as JSON.
// echo status->render('json');
