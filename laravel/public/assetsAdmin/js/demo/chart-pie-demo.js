// Set new default font family and font color to mimic Bootstrap's default styling
(Chart.defaults.global.defaultFontFamily = "Nunito"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

// Pie Chart Example (Dynamic from API, safer DOM load)

// Pie Chart Example (Dynamic from API, robust DOM & error handling)
function renderPieChart() {
    var el = document.getElementById("myPieChart");
    if (!el) {
        setTimeout(renderPieChart, 200);
        return;
    }
    fetch("http://localhost:3000/api/users/role-counts")
        .then((response) => response.json())
        .then((data) => {
            if (
                window.myPieChart &&
                typeof window.myPieChart.destroy === "function"
            )
                window.myPieChart.destroy();
            var ctxPie = el.getContext("2d");
            window.myPieChart = new Chart(ctxPie, {
                type: "doughnut",
                data: {
                    labels: ["Keuangan", "Panitia"],
                    datasets: [
                        {
                            data: [data.keuangan, data.panitia],
                            backgroundColor: ["#4e73df", "#1cc88a"],
                            hoverBackgroundColor: ["#2e59d9", "#17a673"],
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
            console.error("Pie chart fetch/chart error:", err);
        });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", renderPieChart);
} else {
    renderPieChart();
}
