<h2 class="page-title">Accounting Dashboard</h2>

<!-- TICKET STATUS -->
<h3 class="section-title">Ticket Status Overview</h3>

<div class="dashboard-grid status-grid">
    <div class="card">
        <h4>Total Assigned</h4>
        <div class="card-number"><?= $total ?></div>
    </div>
    <div class="card">
        <h4>Endorsed</h4>
        <div class="card-number" style="color: #7c3aed;"><?= $endorsed ?></div>
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

<!-- PRIORITY -->
<h3 class="section-title">Priority Overview</h3>

<div class="dashboard-grid priority-grid">
    <div class="card">
        <h4>Critical</h4>
        <div class="card-number" style="color: #ff4d4f;"><?= $critical ?></div>
    </div>
    <div class="card">
        <h4>High</h4>
        <div class="card-number" style="color: #d93025;"><?= $high ?></div>
    </div>
    <div class="card">
        <h4>Medium</h4>
        <div class="card-number" style="color: #c77700;"><?= $medium ?></div>
    </div>
    <div class="card">
        <h4>Low</h4>
        <div class="card-number" style="color: #2b5fd9;"><?= $low ?></div>
    </div>
</div>

<!-- RECENT TICKETS -->
<h3 class="section-title">Recent Assigned Tickets</h3>

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
                        <?php $statusClass = strtolower(str_replace(' ', '', $ticket->ticket_status)); ?>
                        <span class="badge badge-<?= $statusClass ?>">
                            <?= $ticket->ticket_status ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y h:i A', strtotime($ticket->created_at)) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center; color:#6c757d; padding:30px;">
                    No tickets assigned yet.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>