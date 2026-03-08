</main> <!-- content -->
</div> <!-- layout -->
</div> <!-- admin-wrapper -->
<script>
    function openModal() {
        document.getElementById('createUserModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('createUserModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('createUserModal');
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>

<script>
function openEditModal(id, full_name, role, team_id) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_full_name').value = full_name;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_team').value = team_id;
    document.getElementById('editForm').action =
        "<?= site_url('admin/user/update/') ?>" + id;
    document.getElementById('editUserModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}
</script>

<script>
function openRoleModal() {
    document.getElementById('createRoleModal').style.display = 'flex';
}

function closeRoleModal() {
    document.getElementById('createRoleModal').style.display = 'none';
}

function openTeamModal() {
    document.getElementById('createTeamModal').style.display = 'flex';
}

function closeTeamModal() {
    document.getElementById('createTeamModal').style.display = 'none';
}
</script>

<script>
const usernameInput = document.getElementById('usernameInput');
const feedback = document.getElementById('usernameFeedback');
const submitBtn = document.querySelector('#createUserModal button[type="submit"]');

if (usernameInput) {
    usernameInput.addEventListener('keyup', function () {
        const username = this.value.trim();

        if (username.length < 3) {
            feedback.textContent = '';
            submitBtn.disabled = false;
            return;
        }

        fetch("<?= site_url('admin/user/check-username') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "username=" + encodeURIComponent(username)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "taken") {
                feedback.textContent = "Username already taken";
                feedback.style.color = "red";
                submitBtn.disabled = true;
            } else if (data.status === "available") {
                feedback.textContent = "Username available";
                feedback.style.color = "green";
                submitBtn.disabled = false;
            }
        });
    });
}
</script>

<script>
function confirmDisable(name, isActive) {
    if (isActive == 1) {
        return confirm("Are you sure you want to disable " + name + "?");
    } else {
        return confirm("Are you sure you want to enable " + name + "?");
    }
}
</script>

</body>
</html>