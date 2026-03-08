
<h2>Create Ticket</h2>

<form method="post" action="<?php echo site_url('tickets/store'); ?>">

    <label>Requester Name</label><br>

    <input type="text" name="requester_name" required>
    <br><br>

    <label>Contact Info</label><br>
    <input type="text" name="contact_info">
    <br><br>

    <label>Client</label><br>
    <select name="client_id" required>
        <option value="">Select Client</option>
        <option value="1">ELSECO</option>
        <option value="2">PCAB</option>
        <option value="3">STL</option>
        <option value="4">PEZA</option>
    </select>
    <br><br>

    <label>Category</label><br>
    <select name="category" required>
        <option value="">Select Category</option>
        <option value="POS">POS</option>
        <option value="Platform">Platform</option>
        <option value="Server">Server</option>
        <option value="Others">Others</option>
    </select>
    <br><br>

    <label>Priority</label><br>
    <select name="priority" required>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
        <option value="Critical">Critical</option>
    </select>
    <br><br>

    <label>Ticket Type</label><br>
    <select name="ticket_type" required>
        <option value="Issue">Issue</option>
        <option value="Request">Request</option>
        <option value="Account Creation">Account Creation</option>
    </select>
    <br><br>

    <label>Channel</label><br>
    <select name="channel">
        <option value="Viber">Viber</option>
        <option value="Messenger">Messenger</option>
        <option value="Email">Email</option>
        <option value="Call">Call</option>
    </select>
    <br><br>

    <label>Subject</label><br>
    <input type="text" name="subject" required>
    <br><br>

    <label>Description</label><br>
    <textarea name="description" rows="5" cols="50"></textarea>
    <br><br>

    <button type="submit">Submit Ticket</button>

</form>

<br>
<a href="<?php echo site_url('tickets'); ?>">Back to Tickets</a>

<?php $this->load->view('csr/layout/footer'); ?>