<!--  
  Liturgical Calendar display script using AJAX and Javascript
  Author: John Romano D'Orazio 
  Email: priest@johnromanodorazio.com
  Licensed under the Apache 2.0 License
  Version 2.3
  Date Created: 27 December 2017
-->

<!doctype html>

<head>
    <title>{TITLE PLACEHOLDER}</title>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../../favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="msapplication-TileImage" content="easter-egg-5-144-279148.png">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="../../easter-egg-5-152-279148.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../../easter-egg-5-144-279148.png">
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="../../easter-egg-5-120-279148.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../../easter-egg-5-114-279148.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../../easter-egg-5-72-279148.png">
    <link rel="apple-touch-icon-precomposed" href="../../easter-egg-5-57-279148.png">
    <link rel="icon" href="../../easter-egg-5-32-279148.png" sizes="32x32">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="styles.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="script.js"></script>
</head>

<body>
    <div id="spinnerWrapper">
        <div class="lds-roller">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <div><a class="backNav" href="/LiturgicalCalendar">↩ Go back ↩</a></div>
    <span class="material-icons" id="openSettings">settings</span>
    <header>
    </header>
    <table id="LitCalTable">
        <thead></thead>
        <tbody></tbody>
    </table>
    <div style="text-align:center;border:3px ridge Green;background-color:LightBlue;width:75%;margin:10px auto;padding:10px;"><span id="dayCnt"></span> event days created</div>
    <table id="LitCalMessages">
        <thead></thead>
        <tbody></tbody>
    </table>
</body>