// Set new default font family and font color to mimic Bootstrap's default styling
(Chart.defaults.global.defaultFontFamily = "Nunito"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + "").replace(",", "").replace(" ", "");
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = typeof thousands_sep === "undefined" ? "," : thousands_sep,
        dec = typeof dec_point === "undefined" ? "." : dec_point,
        s = "",
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return "" + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || "").length < prec) {
        s[1] = s[1] || "";
        s[1] += new Array(prec - s[1].length + 1).join("0");
    }
    return s.join(dec);
}

// Area Chart Panitia: Event per bulan oleh user login
// Pastikan ada variabel JS global: window.userIdLogin (isi id user login)
document.addEventListener("DOMContentLoaded", function () {
    function renderEmptyChart(message) {
        var canvas = document.getElementById("myAreaChart");
        if (!canvas) return;
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.font = "16px Nunito, Arial, sans-serif";
        ctx.fillStyle = "#858796";
        ctx.textAlign = "center";
        ctx.fillText(message, canvas.width / 2, canvas.height / 2);
    }
    const userId = window.userIdLogin;
    if (!userId) {
        renderEmptyChart("User belum login atau ID tidak ditemukan");
        console.error("userIdLogin tidak ditemukan di window");
        return;
    }
    fetch(
        `http://localhost:3000/api/events/count/by-month?coordinator=${userId}`
    )
        .then((res) => {
            if (!res.ok)
                throw new Error("Gagal fetch data event: " + res.status);
            return res.json();
        })
        .then((eventCounts) => {
            if (
                !Array.isArray(eventCounts) ||
                eventCounts.every((v) => v === 0)
            ) {
                renderEmptyChart("Belum ada event yang dibuat tahun ini");
                return;
            }
            var canvas = document.getElementById("myAreaChart");
            if (!canvas) {
                console.error('Element with id "myAreaChart" not found.');
                return;
            }
            if (window.myLineChart) window.myLineChart.destroy();
            var ctxArea = canvas.getContext("2d");
            window.myLineChart = new Chart(ctxArea, {
                type: "line",
                data: {
                    labels: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec",
                    ],
                    datasets: [
                        {
                            label: "Event Dibuat",
                            lineTension: 0.3,
                            backgroundColor: "rgba(78, 115, 223, 0.05)",
                            borderColor: "rgba(78, 115, 223, 1)",
                            pointRadius: 3,
                            pointBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointBorderColor: "rgba(78, 115, 223, 1)",
                            pointHoverRadius: 3,
                            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: eventCounts,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0,
                        },
                    },
                    scales: {
                        xAxes: [
                            {
                                gridLines: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    maxTicksLimit: 12,
                                },
                            },
                        ],
                        yAxes: [
                            {
                                ticks: {
                                    min: 0,
                                    precision: 0,
                                    callback: function (value) {
                                        return value;
                                    },
                                },
                                gridLines: {
                                    color: "rgb(234, 236, 244)",
                                    zeroLineColor: "rgb(234, 236, 244)",
                                    drawBorder: false,
                                    borderDash: [2],
                                    zeroLineBorderDash: [2],
                                },
                            },
                        ],
                    },
                    legend: {
                        display: true,
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: "#6e707e",
                        titleFontSize: 14,
                        borderColor: "#dddfeb",
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: true,
                        intersect: false,
                        mode: "index",
                        caretPadding: 10,
                        callbacks: {
                            label: function (tooltipItem, chart) {
                                var datasetLabel =
                                    chart.datasets[tooltipItem.datasetIndex]
                                        .label || "";
                                return datasetLabel + ": " + tooltipItem.yLabel;
                            },
                        },
                    },
                },
            });
        })
        .catch((err) => {
            renderEmptyChart("Gagal mengambil data event");
            console.error(err);
        });
});
