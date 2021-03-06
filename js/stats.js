/**
 * Created by yanni on 22.06.2016.
 */
var json = {all: [], students: [], visitors: [], logs: []};
function loadStats() {
    // Get the CSV and create the chart
    $.getJSON('stats.php?action=getData', function (data) {

        data["all"].forEach(function(element, index, array) {
            json.all.push({x: new Date(element[0]), y:element[1]})
        });
        data["students"].forEach(function(element, index, array) {
            json.students.push({x: new Date(element[0]), y:element[1]})
        });
        data["visitors"].forEach(function(element, index, array) {
            json.visitors.push({x: new Date(element[0]), y:element[1]})
        });

        $('#stat').highcharts({
            chart: {
                type: 'areaspline'
            },
            title: {
                text: 'Schlopolis Besucher/Bürgerzahlen'
            },
            xAxis: {
                type: "datetime",
                title: {
                    text: null
                }
            },
            yAxis: {
                showEmpty: false,
                floor: 0,
                allowDecimals: false,
                title: {
                    text: 'Personen'
                }
            },
            tooltip: {
                pointFormat: '{point.y:,.0f} Personen'
            },
            plotOptions: {
                areaspline: {
                    marker: {
                        enabled: true,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    },
                    fillOpacity: .5
                }
            },
            series: [{
                name: 'Alle',
                data: json.all
            }, {
                name: 'Schüler',
                data: json.students
            }, {
                name: 'Besucher',
                data: json.visitors
            }]
        });
    });
	$.getJSON('stats.php?action=getDataLogsPerMinute', function (data) {

        data["all"].forEach(function(element, index, array) {
            json.logs.push({x: new Date(element[0]), y:element[1]})
        });
        $('#stat2').highcharts({
            chart: {
                type: 'areaspline'
            },
            title: {
                text: 'Schlopolis Scan pro Minute'
            },
            xAxis: {
                type: "datetime",
                title: {
                    text: null
                }
            },
            yAxis: {
                showEmpty: false,
                floor: 0,
                allowDecimals: false,
                title: {
                    text: 'Scans/min'
                }
            },
            tooltip: {
                pointFormat: '{point.y:,.0f} Scans/min'
            },
            plotOptions: {
                areaspline: {
                    marker: {
                        enabled: true,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    },
                    fillOpacity: .5
                }
            },
            series: [{
                name: 'Scans',
                data: json.logs
            }]
        });
    });
}

$(document).ready(function() {
    loadStats();
});