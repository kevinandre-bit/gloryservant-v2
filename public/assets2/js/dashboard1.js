$(function () {
    "use strict";




// chart 1
var pct = window.attendancePct || 0;
var week0 = window.week0Hours|| 0;
var week1 = window.week1Hours|| 0;
var week2 = window.week2Hours|| 0;
var week3 = window.week3Hours|| 0;
var week4 = window.week4Hours|| 0;
var week5 = window.week5Hours|| 0;
var week6 = window.week6Hours|| 0;
var week7 = window.week7Hours|| 0;
var week8 = window.week8Hours|| 0;


var week0Att = window.week0Att|| 0;
var week1Att = window.week1Att|| 0;
var week2Att = window.week2Att|| 0;
var week3Att = window.week3Att|| 0;
var week4Att = window.week4Att|| 0;
var week5Att = window.week5Att|| 0;
var week6Att = window.week6Att|| 0;
var week7Att = window.week7Att|| 0;
var week8Att = window.week8Att|| 0;
var week9Att = window.week9Att|| 0;
var week10Att = window.week10Att|| 0;
var week11Att = window.week11Att|| 0;

var week0Start = window.week0Start|| 0;
var week1Start = window.week1Start|| 0;
var week2Start = window.week2Start|| 0;
var week3Start = window.week3Start|| 0;
var week4Start = window.week4Start|| 0;
var week5Start = window.week5Start|| 0;
var week6Start = window.week6Start|| 0;
var week7Start = window.week7Start|| 0;
var week8Start = window.week8Start|| 0;
var week9Start = window.week9Start|| 0;
var week10Start = window.week10Start|| 0;
var week11Start = window.week11Start|| 0;

var month0Clicks = window.month0Clicks || 0;
var month1Clicks = window.month1Clicks || 0;
var month2Clicks = window.month2Clicks || 0;
var month3Clicks = window.month3Clicks || 0;
var month4Clicks = window.month4Clicks || 0;
var month5Clicks = window.month5Clicks || 0;
var month6Clicks = window.month6Clicks || 0;
var month7Clicks = window.month7Clicks || 0;
var month8Clicks = window.month8Clicks || 0;


var devotionWeek0 = window.devotionWeek0 || 0;
var devotionWeek1 = window.devotionWeek1 || 0;
var devotionWeek2 = window.devotionWeek2 || 0;
var devotionWeek3 = window.devotionWeek3 || 0;
var devotionWeek4 = window.devotionWeek4 || 0;
var devotionWeek5 = window.devotionWeek5 || 0;
var devotionWeek6 = window.devotionWeek6 || 0;
var devotionWeek7 = window.devotionWeek7 || 0;
var devotionWeek8 = window.devotionWeek8 || 0;

var options = {
    series: [ pct ], 
    chart: {
        height: 180,
        type: 'radialBar',
        toolbar: {
            show: false
        }
    },
    plotOptions: {
        radialBar: {
            startAngle: -115,
            endAngle: 115,
            hollow: {
                margin: 0,
                size: '80%',
                background: 'transparent',
                image: undefined,
                imageOffsetX: 0,
                imageOffsetY: 0,
                position: 'front',
                dropShadow: {
                    enabled: false,
                    top: 3,
                    left: 0,
                    blur: 4,
                    opacity: 0.24
                }
            },
            track: {
                background: 'rgba(0, 0, 0, 0.1)',
                strokeWidth: '67%',
                margin: 0, // margin is in pixels
                dropShadow: {
                    enabled: false,
                    top: -3,
                    left: 0,
                    blur: 4,
                    opacity: 0.35
                }
            },

            dataLabels: {
                show: true,
                name: {
                    offsetY: -10,
                    show: false,
                    color: '#888',
                    fontSize: '17px'
                },
                value: {
                    offsetY: 10,
                    color: '#111',
                    fontSize: '24px',
                    show: true,
                }
            }
        }
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'horizontal',
            shadeIntensity: 0.5,
            gradientToColors: ['#ffd200'],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 100]
        }
    },
    colors: ["#ee0979"],
    stroke: {
        lineCap: 'round'
    },
    labels: ['Total Orders'],
};

var chart = new ApexCharts(document.querySelector("#chart1"), options);
chart.render();




 // chart 2

 var options = {
    series: [{
        name: "Net Sales",
        data: [week8, week7, week6, week5, week4, week3, week2, week1, week0]
    }],
    chart: {
        //width:150,
        height: 105,
        type: 'area',
        sparkline: {
            enabled: !0
        },
        zoom: {
            enabled: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 3,
        curve: 'smooth'
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            gradientToColors: ['#0866ff'],
            shadeIntensity: 1,
            type: 'vertical',
            opacityFrom: 0.5,
            opacityTo: 0.0,
            //stops: [0, 100, 100, 100]
        },
    },

    colors: ["#02c27a"],
    tooltip: {
        theme: "dark",
        fixed: {
            enabled: !1
        },
        x: {
            show: !1
        },
        y: {
            title: {
                formatter: function (e) {
                    return ""
                }
            }
        },
        marker: {
            show: !1
        }
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
    }
};

var chart = new ApexCharts(document.querySelector("#chart2"), options);
chart.render();




    // chart 3

    var options = {
        series: [{
            name: "Net Sales",
            data: [ month2Clicks, month1Clicks, month0Clicks]
        }],
        chart: {
            //width:150,
            height: 120,
            type: 'bar',
            sparkline: {
                enabled: !0
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 1,
            curve: 'smooth',
            color: ['transparent']
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#7928ca'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            },
        },
        colors: ["#ff0080"],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                borderRadiusApplication: 'around',
                borderRadiusWhenStacked: 'last',
                columnWidth: '45%',
            }
        },

        tooltip: {
            theme: "dark",
            fixed: {
                enabled: !1
            },
            x: {
                show: !1
            },
            y: {
                title: {
                    formatter: function (e) {
                        return ""
                    }
                }
            },
            marker: {
                show: !1
            }
        },
        xaxis: {
            categories: ['May', 'Jun', 'Jul'],
            axisBorder: { show: true },   // ← draw the x-axis line
            axisTicks:  { show: true },   // ← draw the x-axis ticks
            labels:     { show: true }  
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart3"), options);
    chart.render();




    // chart 4

    var options = {
        series: [{
            name: "Net Sales",
            data: [4, 25, 14, 34, 10, 39]
        }],
        chart: {
            //width:150,
            height: 105,
            type: 'line',
            sparkline: {
                enabled: !0
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 3,
            curve: 'straight'
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#00f2fe'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            },
        },

        colors: ["#ee0979"],
        tooltip: {
            theme: "dark",
            fixed: {
                enabled: !1
            },
            x: {
                show: !1
            },
            y: {
                title: {
                    formatter: function (e) {
                        return ""
                    }
                }
            },
            marker: {
                show: !1
            }
        },
        markers: {
            show: !1,
            size: 5,
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart4"), options);
    chart.render();



    
    // chart 5

    var options = {
        series: [{
            name: "Volunteers",
            data: [week11Att, week10Att, week9Att, week8Att, week7Att, week6Att, week5Att, week4Att, week3Att, week2Att, week1Att, week0Att]
        }],
        chart: {
            foreColor: "#9ba7b2",
            height: 280,
            type: 'bar',
            toolbar: {
                show: !1
            },
            sparkline: {
                enabled: !1
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 1,
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                borderRadiusApplication: 'around',
                borderRadiusWhenStacked: 'last',
                columnWidth: '45%',
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#009efd'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            },
        },
        colors: ["#2af598"],
        grid: {
            show: true,
            borderColor: 'rgba(255, 255, 255, 0.1)',
        },
        xaxis: {
            categories: [week11Start, week10Start, week9Start, week8Start, week7Start, week6Start, week5Start, week4Start, week3Start, week2Start, week1Start, week0Start],
        },
        tooltip: {
            theme: "dark",
            marker: {
                show: !1
            }
        },
    };

    var chart = new ApexCharts(document.querySelector("#chart5"), options);
    chart.render();



    
    // chart 6
    var options = {
        series: [58, 25, 25],
        chart: {
            height: 290,
            type: 'donut',
        },
        legend: {
            position: 'bottom',
            show: !1
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#ee0979', '#17ad37', '#ec6ead'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 1,
                opacityTo: 1,
                //stops: [0, 100, 100, 100]
            },
        },
        colors: ["#ff6a00", "#98ec2d", "#3494e6"],
        dataLabels: {
            enabled: !1
        },
        plotOptions: {
            pie: {
                donut: {
                    size: "85%"
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 270
                },
                legend: {
                    position: 'bottom',
                    show: !1
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#chart6"), options);
    chart.render();




 // chart 7
 var options = {
    series: [{
        name: "Total Accounts",
        data: [4, 10, 25, 12, 25, 18, 40, 22, 7]
    }],
    chart: {
        //width:150,
        height: 105,
        type: 'area',
        sparkline: {
            enabled: !0
        },
        zoom: {
            enabled: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 3,
        curve: 'smooth'
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            gradientToColors: ['#fc185a'],
            shadeIntensity: 1,
            type: 'vertical',
            opacityFrom: 0.8,
            opacityTo: 0.2,
            //stops: [0, 100, 100, 100]
        },
    },

    colors: ["#ffc107"],
    tooltip: {
        theme: "dark",
        fixed: {
            enabled: !1
        },
        x: {
            show: !1
        },
        y: {
            title: {
                formatter: function (e) {
                    return ""
                }
            }
        },
        marker: {
            show: !1
        }
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
    }
};

var chart = new ApexCharts(document.querySelector("#chart7"), options);
chart.render();



 // chart 8

 var options = {
    series: [{
        name: "Total Sales",
        data: [devotionWeek8, devotionWeek7, devotionWeek6, devotionWeek5, devotionWeek4, devotionWeek3, devotionWeek2, devotionWeek1, devotionWeek0]
    }],
    chart: {
        //width:150,
        height: 210,
        type: 'area',
        sparkline: {
            enabled: !0
        },
        zoom: {
            enabled: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 3,
        curve: 'straight'
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            gradientToColors: ['#17ad37'],
            shadeIntensity: 1,
            type: 'vertical',
            opacityFrom: 0.7,
            opacityTo: 0.0,
            //stops: [0, 100, 100, 100]
        },
    },
    colors: ["#98ec2d"],
    tooltip: {
        theme: "dark",
        fixed: {
            enabled: !1
        },
        x: {
            show: !1
        },
        y: {
            title: {
                formatter: function (e) {
                    return ""
                }
            }
        },
        marker: {
            show: !1
        }
    },
    markers: {
        show: !1,
        size: 5,
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
    }
};

var chart = new ApexCharts(document.querySelector("#chart8"), options);
chart.render();






});