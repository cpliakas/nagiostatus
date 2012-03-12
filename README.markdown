
Converting Nagios Status Data To Machine Readable Formats
=========================================================

    <?php

    // Registers the autoloader.
    require_once '../library/Nagiostatus/Loader.php';
    Nagiostatus_Loader::register();

    // Render the status.dat file as XML.
    $parser = new Nagiostatus_Parser('/usr/local/nagios/var/status.dat');
    $xml = $parser->render('xml');

    // Uncomment to render as JSON.
    // $json = $parser->render('json');

    ?>

Working With XML
================

    <?php

    $document = new SimpleXMLElement($xml);

    // Find all hoststatus reports.
    $results = $document->xpath('//status[@type="hoststatus"]/report');

    // Find all CPU Status reports.
    $results = $document->xpath('///report[service_description="CPU Stats"]');
    foreach ($results as $result) {
        $data = $result->plugin_output;
    }

    ?>
