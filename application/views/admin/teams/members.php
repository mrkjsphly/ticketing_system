<h2 class="page-title">Team Members — <?= htmlspecialchars($team->team_name) ?></h2>

<div style="margin-bottom: 20px;">
    <a href="<?= site_url('admin/teams') ?>" class="btn-secondary">&larr; Back to Teams</a>
</div>

<div class="card" style="margin-bottom: 25px; display: inline-flex; gap: 30px; padding: 15px 25px;">
    <div>
        <span style="font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:0.8px;">Team</span>
        <div style="font-weight:600; font-size:15px;"><?= htmlspecialchars($team->team_name) ?></div>
    </div>
    <div>
        <span style="font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:0.8px;">Role</span>
        <div><span class="role-badge"><?= $team->role ?></span></div>
    </div>
    <div>
        <span style="font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:0.8px;">Members</span>
        <div style="font-weight:600; font-size:15px;"><?= count($members) ?></div>
    </div>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($members)): ?>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?= $member->id ?></td>
                    <td><?= htmlspecialchars($member->full_name) ?></td>
                    <td><?= htmlspecialchars($member->username) ?></td>
                    <td><span class="role-badge"><?= $member->role ?></span></td>
                    <td>
                        <?php if ($member->is_active): ?>
                            <span class="status-badge active">Active</span>
                        <?php else: ?>
                            <span class="status-badge disabled">Disabled</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M d, Y', strtotime($member->created_at)) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center; color:#6c757d; padding:30px;">
                    No members in this team yet.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>