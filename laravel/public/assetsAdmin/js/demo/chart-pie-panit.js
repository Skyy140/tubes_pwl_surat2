// Set new default font family and font color to mimic Bootstrap's default styling
(Chart.defaults.global.defaultFontFamily = "Nunito"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

function renderPieChartPanit() {
    var el = document.getElementById("myPieChart");
    if (!el) {
        setTimeout(renderPieChartPanit, 200);
        return;
    }
    const userId = window.userIdLogin;
    if (!userId) {
        // Tampilkan pesan error di chart
        var ctx = el.getContext("2d");
        ctx.clearRect(0, 0, el.width, el.height);
        ctx.font = "16px Nunito, Arial, sans-serif";
        ctx.fillStyle = "#858796";
        ctx.textAlign = "center";
        ctx.fillText("User belum login", el.width / 2, el.height / 2);
        return;
    }
    fetch(
        `http://localhost:3000/api/events/count/by-category?coordinator=${userId}`
    )
        .then((response) => response.json())
        .then((data) => {
            if (
                window.myPieChart &&
                typeof window.myPieChart.destroy === "function"
            )
                window.myPieChart.destroy();
            const labels = Object.keys(data);
            const values = Object.values(data);
            if (labels.length === 0) {
                var ctx = el.getContext("2d");
                ctx.clearRect(0, 0, el.width, el.height);
                ctx.font = "16px Nunito, Arial, sans-serif";
                ctx.fillStyle = "#858796";
                ctx.textAlign = "center";
                ctx.fillText("Belum ada event", el.width / 2, el.height / 2);
                return;
            }
            var ctxPie = el.getContext("2d");
            window.myPieChart = new Chart(ctxPie, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: [
                                "#4e73df",
                                "#1cc88a",
                                "#36b9cc",
                                "#f6c23e",
                                "#e74a3b",
                                "#858796",
                                "#5a5c69",
                            ],
                            hoverBackgroundColor: [
                                "#2e59d9",
                                "#17a673",
                                "#2c9faf",
                                "#dda20a",
                                "#be2617",
                                "#6c757d",
                                "#343a40",
                            ],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: "#dddfeb",
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true,
                    },
                    cutoutPercentage: 80,
                },
            });
        })
        .catch((err) => {
            var ctx = el.getContext("2d");
            ctx.clearRect(0, 0, el.width, el.height);
            ctx.font = "16px Nunito, Arial, sans-serif";
            ctx.fillStyle = "#858796";
            ctx.textAlign = "center";
            ctx.fillText("Gagal mengambil data", el.width / 2, el.height / 2);
            console.error("Pie chart panit fetch/chart error:", err);
        });
}

document.addEventListener("DOMContentLoaded", renderPieChartPanit);
