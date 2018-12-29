
function createChartByRangeDate(id, title, subtitle, lineTitle, xTitle, xyData){

    Highcharts.chart((id ? id : 'grafic-container'), {
        chart: {
            type: 'spline'
        },
        title: {
            text: (title ? title : '')
        },

        subtitle: {
            text: (subtitle ? subtitle : '')
        },

        xAxis: {
            title: {
                text: 'Fecha'
            },
            type: 'datetime',
            dateTimeLabelFormats:{
                day:"%d/%m",
                month:"%m",
                year:"%d/%m/%Y"
            },
            tickInterval: 0,
        },
        yAxis: {
            title: {
                text: 'Visitas'
            }
        },
        series: [{
            name: 'Nro Visitas',
            data: xyData,
        }],
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%d/%m/%Y}: {point.y} visitas'

        },
        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
    });
}

function createChartByDay(id, title, subtitle, lineTitle, xTitle, xyData){
    Highcharts.chart((id ? id : 'grafic-container'), {
        chart: {
            type: 'spline'
        },
        title: {
            text: (title ? title : '')
        },

        subtitle: {
            text: (subtitle ? subtitle : '')
        },

        xAxis: {
            title: {
                text: 'Horas'
            },
            type: 'datetime',
            dateTimeLabelFormats:{
                day:"%H",
                month:"%m",
                year:"%d/%m/%Y",
            },
            tickInterval: 3600 * 1000, // Establece el intervalo es decir una hora.
            labels: 
            {
                format: '{value:%H:%M}', //Establece el formato de la hora.
                step: 1  // Establece los labels cada cuanto saltos se mostraran.
            }
        },
        yAxis: {
            title: {
                text: 'Visitas'
            }
        },
        series: [{
            name: 'Nro Visitas',
            data: xyData,
        }],
        plotOptions: {series: {marker: {radius: 3, enabled: true, enabledThreshold: 3}}},
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%d/%m/%Y - %H:%M}: {point.y} visitas'
        },
    });
}



function createChart(id, title, subtitle, lineTitle, xData, yData){


    Highcharts.chart((id ? id : 'grafic-container'), {
        /*chart: {
           type: 'column'
       },*/
        title: {
            text: (title ? title : '')
        },

        subtitle: {
            text: (subtitle ? subtitle : '')
        },

        yAxis: {
            title: {
                text: ''
            }
        },

        xAxis: {
            dateTimeLabelFormats:{
                day:"%d/%m/%Y",
                month:"%d/%m/%Y",
                week:"%d/%m/%Y",
                year:"%d/%m/%Y"
            }
        },
        /*legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                },
                pointStart: 2010
            }
        },*/

        /*series: [{
            name: 'Installation',
            data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
        }, {
            name: 'Manufacturing',
            data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
        }, {
            name: 'Sales & Distribution',
            data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
        }, {
            name: 'Project Development',
            data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
        }, {
            name: 'Other',
            data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
        }],*/

        data: {
            dateFormat: "dd/mm/YYYY",
            columns: [
                ([null]).concat(xData),
                ([lineTitle]).concat(yData)
            ]
        }



        /*responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
*/
    });
}




function createPieChart(id, title, subtitle, lineTitle, chartData){


    Highcharts.chart((id ? id : 'grafic-container'), {
        chart: {
            type: 'pie'
        },
        title: {
            text: (title ? title : '')
        },

        subtitle: {
            text: (subtitle ? subtitle : '')
        },

        tooltip: {
            pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Cantidad',
            colorByPoint: true,
            data: chartData
        }]

    });
}