<?php

$data = basename(stripslashes($_SERVER["KALENDER_DATA"]));

?>
<!DOCTYPE html>
<html lang="en" style="width:100%; height:100%;">
    <head>
        <meta charset="utf8" />
        <title>Kalender – Junge Piraten</title>

        <link rel='stylesheet' type='text/css' href="https://static.junge-piraten.de/fullcalendar-1.5.4/fullcalendar/fullcalendar.css" />
        <link rel='stylesheet' type='text/css' href="https://static.junge-piraten.de/fullcalendar-1.5.4/fullcalendar/fullcalendar.print.css" media="print" />
        <script type="text/javascript" src="https://static.junge-piraten.de/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="https://static.junge-piraten.de/fullcalendar-1.5.4/jquery/jquery-ui-1.8.23.custom.min.js"></script>
        <script type="text/javascript" src="https://static.junge-piraten.de/fullcalendar-1.5.4/fullcalendar/fullcalendar.min.js"></script>

        <link rel="icon" type="image/png" href="https://static.junge-piraten.de/favicon.png" />

        <script type="text/javascript">
            $(function () {
                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText: {
                        today: "heute",
                        month: "Monat",
                        week: "Woche",
                        day: "Tag"
                    },
                    firstDay: 1,
                    allDayText: "ganztägig",
                    axisFormat: "HH:mm",
                    columnFormat: "ddd dd.MM.",
                    titleFormat: {
                        month: "MMMM yyyy",
                        week: "dd.[ MMM][ yyyy]{ '&#8212;' dd. MMM yyyy}",
                        day: "dddd, dd.MM.yyyy"
                    },
                    monthNames: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
                    monthNamesShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
                    dayNames: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
                    dayNamesShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
                    timeFormat: 'HH:mm{ - HH:mm}',
                    height: window.innerHeight,
                    events: '/<?php print($data); ?>.json'
                });
            });
            $(window).resize(function () {
                $('#calendar').fullCalendar("option", "height", window.innerHeight);
            });
        </script>
    </head>

    <body style="width:100%; margin:0px; font-family:sans-serif;">
        <div id='calendar'></div>
    </body>
</html>

