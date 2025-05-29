<li>
    <a href="../admin/admin.php" class="<?php echo ($currentPage == 'admin.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-dashboard-64.png" alt="Dashboard" style="width: 50px; height: 50px;">
        <p>Dashboard</p>
    </a>
</li>
<li>
    <a href="../admin/orders.php" class="<?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-order-history-64.png" alt="Orders" style="width: 50px; height: 50px;">
        <p>Orders</p>
    </a>
</li>
<li>
    <a href="../admin/payment.php" class="<?php echo ($currentPage == 'payment.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-payment-history-64.png" alt="payment" style="width: 50px; height: 50px;">
        <p>payment history</p>
    </a>
</li>
<li>
    <a href="../admin/Requests.php" class="<?php echo ($currentPage == 'Requests.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-purchase-order-64.png" alt="Requests" style="width: 50px; height: 50px;">
        <p>Requests</p>
        <div class="count">
            <span><?php echo htmlspecialchars($workerCountp); ?></span>
        </div>
    </a>
</li>
<li>
    <a href="../admin/users.php" class="<?php echo ($currentPage == 'users.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-user-account-64.png" alt="Users" style="width: 50px; height: 50px;">
        <p>Users</p>
    </a>
</li>
<li>
    <a href="../admin/updateprices.php" class="<?php echo ($currentPage == 'updateprices.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-wrench-64.png" alt="Update Prices" style="width: 50px; height: 50px;">
        <p>Update Prices</p>
    </a>
</li>
<li>
    <a href="../admin/addadmin.php" class="<?php echo ($currentPage == 'addadmin.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-add-administrator-64.png" alt="Update Prices" style="width: 50px; height: 50px;">
        <p>Add admin</p>
    </a>
</li>

<li>
    <a href="../admin/note.php" class="<?php echo ($currentPage == 'note.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-send-64.png" alt="Send Note" style="width: 50px; height: 50px;">
        <p>Send Note</p>
    </a>
</li>
<li>
    <a href="../admin/contacts.php" class="<?php echo ($currentPage == 'contacts.php') ? 'active' : ''; ?>">
        <img src="../images/icons8-receive-64.png" alt="Contacts" style="width: 50px; height: 50px;">
        <p>Contacts</p>
    </a>
</li>
<li class="fas">
    <a href="../admin/logout.php">
        <img src="../images/icons8-log-out-64.png" alt="Log Out" style="width: 50px; height: 50px;">
        <p>Log out</p>
    </a>
</li>
