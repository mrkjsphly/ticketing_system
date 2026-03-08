<h2 class="page-title">Activity Logs</h2>

<div class="table-controls">
    <form method="get" action="<?= site_url('admin/activity_logs') ?>">

        <input type="text"
               name="search"
               placeholder="Search action..."
               value="<?= $this->input->get('search') ?>">

        <input type="date"
               name="date_from"
               value="<?= $this->input->get('date_from') ?>">

        <input type="date"
               name="date_to"
               value="<?= $this->input->get('date_to') ?>">

        <button type="submit" class="btn-primary">Filter</button>

        <a href="<?= site_url('admin/activity_logs') ?>" class="btn-secondary">
            Reset
        </a>

    </form>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>

        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <span class="role-badge">
                            <?= htmlspecialchars($log->full_name) ?>
                        </span>
                    </td>

                    <td>
                        <?= htmlspecialchars($log->action) ?>
                    </td>

                    <td>
                        <?= date('M d, Y h:i A', strtotime($log->created_at)) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center;">
                    No activity found.
                </td>
            </tr>
        <?php endif; ?>

    </tbody>
</table>

<?php if (!empty($pagination)): ?>
    <div class="pagination-wrapper">
        <?= $pagination ?>
    </div>
<?php endif; ?>