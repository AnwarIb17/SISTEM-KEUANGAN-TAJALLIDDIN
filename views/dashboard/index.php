<?php
$title = 'Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h1 class="mb-0">Dashboard</h1>
    </div>

    <!-- Summary Cards -->
    <!-- Baris 1: 3 card -->
    <div class="row g-4 mb-4 align-items-stretch">
        <!-- 1. Infaq Bulan Ini -->
        <div class="col-12 col-md-4">
            <div class="stat-card" style="cursor:pointer;" onclick="window.location.href='infaq'">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Infaq Bulan Ini</h6>
                        <h3><?= number_format($infaqBulanan, 0, ',', '.') ?></h3>
                        <p class="small mb-0" style="color:var(--muted);"><?= date('F Y') ?></p>
                    </div>
                    <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                </div>
            </div>
        </div>
        <!-- 2. Total Infaq (Keseluruhan) -->
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Total Infaq</h6>
                        <h3><?= number_format($infaqTotal, 0, ',', '.') ?></h3>
                        <p class="small mb-0" style="color:var(--muted);">Keseluruhan</p>
                    </div>
                    <div class="icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>
        </div>
        <!-- 3. Total Keuangan Alumni -->
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Keuangan Alumni</h6>
                        <h3><?= number_format($alumniTotal, 0, ',', '.') ?></h3>
                        <p class="small mb-0" style="color:var(--muted);">Total Pembayaran Program</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-graduate"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Baris 2: 3 card -->
    <div class="row g-4 mb-4 align-items-stretch">
        <!-- 4. Pemasukkan -->
        <div class="col-12 col-md-4">
            <div class="stat-card" style="cursor:pointer;" onclick="window.location.href='income'">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Pemasukkan</h6>
                        <h3><?= number_format($pemasukkan, 0, ',', '.') ?></h3>
                        <p class="small mb-0" style="color:var(--muted);"><?= date('F Y') ?></p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-trend-up"></i></div>
                </div>
            </div>
        </div>
        <!-- 5. Pengeluaran -->
        <div class="col-12 col-md-4">
            <div class="stat-card" style="cursor:pointer;" onclick="window.location.href='expense'">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Pengeluaran</h6>
                        <h3><?= number_format($pengeluaran, 0, ',', '.') ?></h3>
                        <p class="small mb-0" style="color:var(--muted);"><?= date('F Y') ?></p>
                    </div>
                    <div class="icon"><i class="fas fa-receipt"></i></div>
                </div>
            </div>
        </div>
        <!-- 6. Sisa -->
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Sisa</h6>
                        <h3><?= number_format($sisa, 0, ',', '.') ?></h3>
                        <p class="small mb-0" style="color:var(--muted);">Infaq + Pemasukkan - Pengeluaran</p>
                    </div>
                    <div class="icon"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Grafik Infaq Per Bulan (<?= $selectedYear ?? date('Y') ?>)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="infaqChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Pengeluaran Per Kategori</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($expenseByCategory)): ?>
                    <div class="chart-container">
                        <canvas id="expenseChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-pie fa-3x mb-3" style="color:var(--muted);opacity:.4;"></i>
                        <p style="color:var(--muted);">Belum ada data pengeluaran bulan ini</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(amount);
    }

    // Shared chart styling
    Chart.defaults.color = '#0e0e0e';
    Chart.defaults.borderColor = 'rgba(14,14,14,.12)';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;

    const infaqChartCtx = document.getElementById('infaqChart').getContext('2d');
    const infaqChart = new Chart(infaqChartCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Total Infaq',
                data: <?= json_encode($chartData) ?>,
                borderColor: '#2138f5',
                backgroundColor: 'rgba(33,56,245,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2138f5',
                pointBorderColor: '#0e0e0e',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#161413',
                    borderColor: 'rgba(255,255,255,.1)',
                    borderWidth: 1,
                    titleColor: '#e8e3d8',
                    bodyColor: '#8d8678',
                    padding: 10,
                    callbacks: { label: function(context) { return formatCurrency(context.raw); } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } },
                    grid: { drawBorder: false }
                },
                x: { grid: { display: false } }
            }
        }
    });

    <?php if (!empty($expenseByCategory)): ?>
    const expenseChartCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseChartCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map(fn($c) => $c['name'], $expenseByCategory)) ?>,
            datasets: [{
                data: <?= json_encode(array_map(fn($c) => $c['total'], $expenseByCategory)) ?>,
                backgroundColor: ['#2138f5','#ffd23f','#9be7c4','#ff6a5c','#7c6cf0','#e8a87c'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 14 } },
                tooltip: {
                    backgroundColor: '#161413',
                    borderColor: 'rgba(255,255,255,.1)',
                    borderWidth: 1,
                    titleColor: '#e8e3d8',
                    bodyColor: '#8d8678',
                    padding: 10,
                    callbacks: { label: function(context) { return context.label + ': ' + formatCurrency(context.raw); } }
                }
            }
        }
    });
    <?php endif; ?>
    </script>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
