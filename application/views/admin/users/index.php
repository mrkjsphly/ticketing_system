<?php
$error   = $this->session->flashdata('error');
$success = $this->session->flashdata('success');
$roles   = ['SUPERADMIN', 'TL', 'TECH', 'CSR', 'ACCOUNTING'];
?>

<h2>User Management</h2>

<div class="action-buttons">
    <button class="btn-primary" onclick="openModal()">
        + Create User
    </button>

    <button class="btn-secondary" onclick="openTeamModal()">
        + Create Team
    </button>
</div>

<br><br>

<?php if ($success): ?>
    <p style="color:green;">
        <?= $success ?>
    </p>
<?php endif; ?>

<div class="table-controls">
    <form method="get" action="<?= site_url('admin/user') ?>">

        <input type="text"
            name="search"
            placeholder="Search name or username..."
            value="<?= $this->input->get('search') ?>">

        <select name="role">
            <option value="">All Roles</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r ?>" <?= $this->input->get('role') == $r ? 'selected' : '' ?>>
                    <?= $r ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status">
            <option value="">All Status</option>
            <option value="1" <?= $this->input->get('status') === '1' ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= $this->input->get('status') === '0' ? 'selected' : '' ?>>Disabled</option>
        </select>

        <button type="submit" class="btn-primary">Search</button>

        <a href="<?= site_url('admin/user') ?>" class="btn-secondary">
            Reset
        </a>

    </form>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Role</th>
            <th>Team</th>
            <th>Created</th>
            <th>Status</th>
            <th style="width:160px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= htmlspecialchars($user->full_name) ?></td>
                    <td><?= htmlspecialchars($user->username) ?></td>
                    <td>
                        <span class="role-badge">
                            <?= $user->role ?>
                        </span>
                    </td>
                    <td><?= $user->team_name ?? 'Unassigned' ?></td>
                    <td><?= date('M d, Y', strtotime($user->created_at)) ?></td>
                    <td>
                        <?php if ($user->is_active): ?>
                            <span class="status-badge active">Active</span>
                        <?php else: ?>
                            <span class="status-badge disabled">Disabled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);"
                            class="btn-table edit"
                            onclick="openEditModal(
                                '<?= $user->id ?>',
                                '<?= htmlspecialchars($user->full_name) ?>',
                                '<?= htmlspecialchars($user->role) ?>',
                                '<?= $user->team_id ?>'
                            )">
                            Edit
                        </a>

                        <a class="btn-table toggle"
                            href="<?= site_url('admin/user/toggle/' . $user->id) ?>"
                            onclick="return confirmDisable('<?= $user->full_name ?>', <?= $user->is_active ?>)">
                            <?= $user->is_active ? 'Disable' : 'Enable' ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">
                    No users found.
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

<!-- ================= CREATE USER MODAL ================= -->

<div id="createUserModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Create New User</h3>

        <?php if ($error): ?>
            <p style="color:red; margin-bottom:10px;">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form action="<?= site_url('admin/user/store') ?>" method="post">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="usernameInput" required>
                <small id="usernameFeedback" style="display:block; margin-top:5px;"></small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
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

            <div class="form-group">
                <label>Team</label>
                <select name="team_id">
                    <option value="">Select Team</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team->id ?>">
                            <?= $team->team_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create User</button>
            </div>

        </form>
    </div>
</div>


<!-- ================= EDIT USER MODAL ================= -->

<div id="editUserModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Edit User</h3>

        <form id="editForm" method="post">

            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" id="edit_full_name" required>
            </div>

            <div class="form-group">
                <label>Password (Leave blank to keep old Password)</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" id="edit_role" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r ?>"><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Team</label>
                <select name="team_id" id="edit_team">
                    <option value="">Select Team</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team->id ?>">
                            <?= $team->team_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Update User</button>
            </div>

        </form>
    </div>
</div>


<!-- ================= CREATE TEAM MODAL ================= -->

<div id="createTeamModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Create New Team</h3>

        <form action="<?= site_url('admin/team/store') ?>" method="post">

            <div class="form-group">
                <label>Team Name</label>
                <input type="text" name="team_name" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeTeamModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create Team</button>
            </div>

        </form>
    </div>
</div>


<?php if ($error): ?>
    <script>
        window.onload = function() {
            document.getElementById("createUserModal").style.display = "flex";
        };
    </script>
<?php endif; ?>