<h2 class="page-title">My Dashboard</h2>

<p style="color: #6c757d; margin-top: -15px; margin-bottom: 25px;">
    Welcome back, <strong><?= $full_name ?></strong>! Here's your ticket overview.
</p>

<!-- ===== STAT CARDS ===== -->
<div class="section-title">Ticket Status Overview</div>
<div class="dashboard-grid">
    <div class="card">
        <h4>Total Tickets</h4>
        <div class="card-number"><?= $total ?></div>
    </div>
    <div class="card">
        <h4>New</h4>
        <div class="card-number" style="color: #2b5fd9;"><?= $new ?></div>
    </div>
    <div class="card">
        <h4>In Progress</h4>
        <div class="card-number" style="color: #c77700;"><?= $inprogress ?></div>
    </div>
    <div class="card">
        <h4>Resolved</h4>
        <div class="card-number" style="color: #1f8f4e;"><?= $resolved ?></div>
    </div>
    <div class="card">
        <h4>Cancelled</h4>
        <div class="card-number" style="color: #d93025;"><?= $cancelled ?></div>
    </div>
</div>

<div class="section-title">Priority Overview</div>
<div class="dashboard-grid">
    <div class="card">
        <h4>Low</h4>
        <div class="card-number" style="color: #3455db;"><?= $low ?></div>
    </div>
    <div class="card">
        <h4>Medium</h4>
        <div class="card-number" style="color: #c77700;"><?= $medium ?></div>
    </div>
    <div class="card">
        <h4>High</h4>
        <div class="card-number" style="color: #d93025;"><?= $high ?></div>
    </div>
    <div class="card">
        <h4>Critical</h4>
        <div class="card-number" style="color: #fff; background: #ff4d4f; border-radius: 4px; padding: 2px 8px; display:inline-block;"><?= $critical ?></div>
    </div>
</div>

<!-- ===== CHARTS ===== -->
<div class="section-title">Charts</div>
<div class="csr-charts-grid">

    <div class="card">
        <h4>Ticket Status</h4>
        <canvas id="statusPieChart"></canvas>
    </div>

    <div class="card">
        <h4>Priority Breakdown</h4>
        <canvas id="priorityBarChart"></canvas>
    </div>

</div>

<!-- ===== RECENT TICKETS ===== -->
<div class="section-title">Recent Tickets</div>
<table class="admin-table">
    <thead>
        <tr>
            <th>Ticket Code</th>
            <th>Client</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($recent_tickets)): ?>
            <?php foreach ($recent_tickets as $ticket): ?>
                <tr>
                    <td><?= $ticket->ticket_code ?></td>
                    <td><?= $ticket->client_name ?></td>
                    <td><?= $ticket->category ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($ticket->priority) ?>">
                            <?= $ticket->priority ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= strtolower(str_replace(' ', '', $ticket->ticket_status)) ?>">
                            <?= $ticket->ticket_status ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y h:i A', strtotime($ticket->created_at)) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No tickets yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- ===== CHART JS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pie Chart — Ticket Status
    new Chart(document.getElementById('statusPieChart'), {
        type: 'pie',
        data: {
            labels: ['New', 'In Progress', 'Resolved', 'Cancelled'],
            datasets: [{
                data: [<?= $new ?>, <?= $inprogress ?>, <?= $resolved ?>, <?= $cancelled ?>],
                backgroundColor: ['#2b5fd9', '#c77700', '#1f8f4e', '#d93025'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Bar Chart — Priority
    new Chart(document.getElementById('priorityBarChart'), {
        type: 'bar',
        data: {
            labels: ['Low', 'Medium', 'High', 'Critical'],
            datasets: [{
                label: 'Tickets',
                data: [<?= $low ?>, <?= $medium ?>, <?= $high ?>, <?= $critical ?>],
                backgroundColor: ['#3455db', '#c77700', '#d93025', '#ff4d4f'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>