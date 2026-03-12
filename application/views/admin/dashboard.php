<h2 class="page-title">Admin Dashboard</h2>

<!-- USER OVERVIEW -->
<h3 class="section-title">User Overview</h3>

<div class="dashboard-grid">

    <div class="card">
        <h4>Total Users</h4>
        <div class="card-number"><?= $total_users ?></div>
    </div>

    <?php if (!empty($role_counts)): ?>
        <?php foreach ($role_counts as $role => $count): ?>
            <div class="card">
                <h4><?= htmlspecialchars($role) ?></h4>
                <div class="card-number"><?= $count ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="card">
        <h4>Active Users</h4>
        <div class="card-number"><?= $total_active ?></div>
    </div>

    <div class="card">
        <h4>Disabled Users</h4>
        <div class="card-number"><?= $total_disabled ?></div>
    </div>

</div>


<!-- TICKET STATUS -->
<h3 class="section-title">Ticket Status Overview</h3>

<div class="dashboard-grid">
    <div class="card">
        <h4>Total Tickets</h4>
        <div class="card-number"><?= $total_tickets ?></div>
    </div>

    <div class="card">
        <h4>Open</h4>
        <div class="card-number"><?= $open_tickets ?></div>
    </div>

    <div class="card">
        <h4>Endorsed</h4>
        <div class="card-number" style="color: #7c3aed;"><?= $endorsed ?></div>
    </div>

    <div class="card">
        <h4>In Progress</h4>
        <div class="card-number"><?= $inprogress ?></div>
    </div>

    <div class="card">
        <h4>Resolved</h4>
        <div class="card-number"><?= $resolved ?></div>
    </div>

    <div class="card">
        <h4>Cancelled</h4>
        <div class="card-number"><?= $cancelled ?></div>
    </div>
</div>


<!-- PRIORITY -->
<h3 class="section-title">Priority Overview</h3>

<div class="dashboard-grid small-grid">

    <div class="card">
        <h4>Critical</h4>
        <div class="card-number"><?= $critical_priority ?></div>
    </div>

    <div class="card">
        <h4>High</h4>
        <div class="card-number"><?= $high_priority ?></div>
    </div>

    <div class="card">
        <h4>Medium</h4>
        <div class="card-number"><?= $medium_priority ?></div>
    </div>

    <div class="card">
        <h4>Low</h4>
        <div class="card-number"><?= $low_priority ?></div>
    </div>
</div>


<!-- RECENT TICKETS -->
<h3 class="section-title">Recent Tickets</h3>

<table class="admin-table">
    <thead>
        <tr>
            <th>Ticket Code</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Team</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($recent_tickets)): ?>
            <?php foreach ($recent_tickets as $ticket): ?>
                <tr>
                    <td><?= $ticket->ticket_code ?></td>
                    <td><?= $ticket->ticket_status ?></td>
                    <td><?= $ticket->priority ?></td>
                    <td><?= $ticket->team_name ?? 'Unassigned' ?></td>
                    <td><?= $ticket->created_at ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No recent tickets.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<!-- RECENT ACTIVITY -->
<h3 class="section-title">Recent Activity</h3>

<table class="admin-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($recent_logs)): ?>
            <?php foreach (array_slice($recent_logs, 0, 5) as $log): ?>
                <tr>
                    <td><?= $log->full_name ?></td>
                    <td><?= $log->action ?></td>
                    <td><?= $log->created_at ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No recent activity.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>