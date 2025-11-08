document.addEventListener("DOMContentLoaded", () => {
    
    // Grafik untuk pendapatan toko
    (function(){
        const orderChartLabels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"];
        const orderChartData = [10, 25, 18, 32, 22, 40];

        const ctx = document.getElementById('chartPendapatan');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: orderChartLabels,
                datasets: [{
                    label: 'Total Order per Bulan',
                    data: orderChartData,
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    })();

    // Grafik untuk total users | total orders
    (function () {
        const ctxBar = document.getElementById('chartUserVsOrder');
        if (!ctxBar) return;

        // Data dummy sek 
        const barChartLabels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"];
        const registeredUsersData = [50, 80, 120, 150, 170, 200];
        const ordersData = [10, 25, 40, 60, 55, 75];

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: barChartLabels,
                datasets: [
                    {
                        label: "User Terdaftar",
                        data: registeredUsersData,
                        borderWidth: 1,
                        backgroundColor: "rgba(54, 162, 235, 0.7)",
                        borderColor: "rgba(54, 162, 235, 1)"
                    },
                    {
                        label: "Total Order",
                        data: ordersData,
                        borderWidth: 1,
                        backgroundColor: "rgba(255, 99, 132, 0.7)",
                        borderColor: "rgba(255, 99, 132, 1)"
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 20
                        }
                    }
                }
            }
        });
    })();

    // Grafik categoty terlaris
    (function () {
        const ctxPie = document.getElementById('chartCategorySales');
        if (!ctxPie) return;

        // Dummy wak
        const categoryLabels = ["CPU", "PC Bundling", "Monitor", "Periferal", "RAM"];
        const categoryData = [120, 90, 150, 80, 40];

        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: "Sales by Category",
                    data: categoryData,
                    backgroundColor: [
                        "rgba(255, 99, 132, 0.8)",
                        "rgba(54, 162, 235, 0.8)",
                        "rgba(255, 206, 86, 0.8)",
                        "rgba(75, 192, 192, 0.8)",
                        "rgba(153, 102, 255, 0.8)"
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1800,
                    easing: "easeOutQuart"
                },
                plugins: {
                    legend: {
                        position: "right"
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) =>
                                `${context.label}: ${context.raw} penjualan`
                        }
                    }
                }
            }
        });
    })();
});