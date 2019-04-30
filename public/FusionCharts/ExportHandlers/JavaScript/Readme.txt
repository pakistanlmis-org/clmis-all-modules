How to setup your own server to process and export JavaScript charts?
========================================================================================

You may want to set up the export service of the JavaScript charts on your own server. For this, you require PHP and Java running in your server. A JavaScript chart sends the export data to a PHP file. This PHP file (present as index.php in the Download Pack > ExportHandlers > JavaScript folder) relays the data to a third party Java library named Batik, which in turn, converts the exported data into the required export format. 

Perform the following steps to set up your server:

 * Get index.php from the Download Pack > ExportHandlers > JavaScript folder and upload the file to a location on your web server.

 * Create a directory called temp in the same directory as index.php. If you have Linux or Unix servers, chmod this new directory to 777.

 * Download the Batik library (as a compressed file) from here.

 * Extract the Batik library in a temporary location and upload batik-rasterizer.jar and the entire lib directory to a location on your web server.

 * Edit index.php and set the path to batik-rasterier.jar in the options section provided at the top of the index.php file as shown below: 
   
   // Options
   define ('BATIK_PATH', 'batik-rasterizer.jar');


 * Set the path to the index.php in the html5ExportHandler attribute of your chart's XML/JSON data as shown below: 

    <chart html5ExportHandler='http://myserver.com/exporter/index.php' ...>






