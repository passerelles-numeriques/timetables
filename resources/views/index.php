<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>PNC Timetables</title>
        
        <!-- JS and CSS of the app //-->
        <link href="<?php echo url('css/app.css'); ?>" rel="stylesheet">
        <script src="<?php echo url('js/main.js'); ?>" async></script>

        <!-- Icons and favicons //-->
        <link rel="icon" href="<?php echo url('favicon.ico'); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo url('apple-touch-icon.png'); ?>">
        <link rel="mask-icon" href="<?php echo url('safari-pinned-tab.svg'); ?>" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Manifest for Progressive application //-->
        <link rel="manifest" href="<?php echo url('manifest.json'); ?>">

<?php
//Pass some configuration to the embedded Vue application
$config['baseURL'] = url('/') . '/';
$config['pathURL'] = parse_url($config['baseURL'], PHP_URL_PATH);
$configAsJson = json_encode($config);
?>
        <script id="configTag" type="application/json"><?php echo $configAsJson; ?></script>
        <noscript>This application doesn't work without JavaScript!</noscript>
    </head>

    <body>
        <div id="app"></div>
    </body>

    <script type="text/javascript">
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker
                .register('<?php echo url('service-worker.js'); ?>')
                .then(function() { console.log('Service Worker Registered'); });
        }
    </script>

</html>
