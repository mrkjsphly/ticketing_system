<?php
$error   = $this->session->flashdata('error');
$success = $this->session->flashdata('success');
$roles   = ['TECH', 'ACCOUNTING', 'CSR', 'TL'];
?>

<h2 class="page-title">Team Management</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <button class="btn-primary" onclick="openCreateTeamModal()">+ Create Team</button>
</div>

<?php if ($success): ?>
    <p style="color: #1f8f4e; margin-bottom: 15px;"><?= $success ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color: #d93025; margin-bottom: 15px;"><?= $error ?></p>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Team Name</th>
            <th>Role</th>
            <th>Members</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($teams)): ?>
            <?php foreach ($teams as $team): ?>
                <tr>
                    <td><?= $team->id ?></td>
                    <td><?= htmlspecialchars($team->team_name) ?></td>
                    <td><span class="role-badge"><?= $team->role ?></span></td>
                    <td><?= $team->member_count ?></td>
                    <td><?= date('M d, Y', strtotime($team->created_at)) ?></td>
                    <td>
                        <a href="<?= site_url('admin/teams/members/' . $team->id) ?>"
                            class="btn-table edit">
                            Members
                        </a>
                        <a href="javascript:void(0);"
                            class="btn-table edit"
                            onclick="openTeamEditModal('<?= $team->id ?>', '<?= htmlspecialchars($team->team_name) ?>', '<?= $team->role ?>')">
                            Edit
                        </a>
                        <a href="<?= site_url('admin/teams/delete/' . $team->id) ?>"
                            class="btn-table toggle"
                            onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($team->team_name) ?>? This cannot be undone.')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center; color:#6c757d; padding:30px;">
                    No teams found.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<!-- CREATE TEAM MODAL -->
<div id="createTeamModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Create New Team</h3>

        <form action="<?= site_url('admin/teams/store') ?>" method="post">

            <div class="form-group">
                <label>Team Name</label>
                <input type="text" name="team_name" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r ?>"><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeCreateTeamModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create Team</button>
            </div>

        </form>
    </div>
</div>


<!-- EDIT TEAM MODAL -->
<div id="editTeamModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Edit Team</h3>

        <form id="editTeamForm" method="post">

            <div class="form-group">
                <label>Team Name</label>
                <input type="text" name="team_name" id="edit_team_name" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" id="edit_team_role" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r ?>"><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeTeamEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Update Team</button>
            </div>

        </form>
    </div>
</div>


<script>
    function openCreateTeamModal() {
        document.getElementById('createTeamModal').style.display = 'flex';
    }

    function closeCreateTeamModal() {
        document.getElementById('createTeamModal').style.display = 'none';
    }

    function openTeamEditModal(id, name, role) {
        document.getElementById('edit_team_name').value = name;
        document.getElementById('edit_team_role').value = role;
        document.getElementById('editTeamForm').action = "<?= site_url('admin/teams/update/') ?>" + id;
        document.getElementById('editTeamModal').style.display = 'flex';
    }

    function closeTeamEditModal() {
        document.getElementById('editTeamModal').style.display = 'none';
    }
</script>