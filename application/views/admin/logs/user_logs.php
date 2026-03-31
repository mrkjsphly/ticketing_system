<?php
$success = $this->session->flashdata('success');
?>

<h2 class="page-title">Activity Logs</h2>

<?php if ($success): ?>
    <p style="color: #1f8f4e; margin-bottom: 15px;"><?= $success ?></p>
<?php endif; ?>

<!-- TABS -->
<div style="display:flex; gap:0; margin-bottom: 20px; border-bottom: 2px solid #e3e6f0;">
    <a href="<?= site_url('admin/activity_logs') ?>"
        style="padding: 10px 20px; font-weight:600; color:#6c757d; text-decoration:none;">
        System Logs
    </a>
    <a href="<?= site_url('admin/activity_logs/user_logs') ?>"
        style="padding: 10px 20px; font-weight:600; color:#2b5fd9; border-bottom: 2px solid #2b5fd9; margin-bottom:-2px; text-decoration:none;">
        User Logs
    </a>
</div>

<!-- FILTERS -->
<div class="table-controls">
    <form method="get" action="<?= site_url('admin/activity_logs/user_logs') ?>" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin:0;">
        <input type="text" name="search" placeholder="Search activity, ticket or user..."
            value="<?= htmlspecialchars($this->input->get('search') ?? '') ?>" style="width:260px;">
        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
        <input type="date" name="date_to"   value="<?= htmlspecialchars($date_to) ?>">
        <button type="submit" class="btn-primary">Filter</button>
        <a href="<?= site_url('admin/activity_logs/user_logs') ?>" class="btn-secondary">Reset</a>
    </form>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Ticket</th>
            <th>Activity</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><span class="role-badge"><?= htmlspecialchars($log->full_name ?? 'System') ?></span></td>
                    <td><?= htmlspecialchars($log->ticket_code ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($log->activity) ?></td>
                    <td><?= date('M d, Y h:i A', strtotime($log->created_at)) ?></td>
                    <td>
                        <a href="<?= site_url('admin/activity_logs/delete_user_log/' . $log->id) ?>"
                            class="btn-table toggle"
                            onclick="return confirm('Delete this log entry?')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center; color:#6c757d; padding:30px;">
                    No user activity found.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($pagination)): ?>
    <div class="pagination-wrapper"><?= $pagination ?></div>
<?php endif; ?>