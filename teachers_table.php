<table class="table">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        
        require_once 'db_connection.php';
        
        $sql = "SELECT * FROM users WHERE role='teacher'";
        $results = $conn->query($sql);
        if (!$results) {
            die("Invalid query: " . $conn->error);
        }
        while ($row = $results->fetch_assoc()) {
            echo "
            <tr>
                <td>$row[user_id]</td>
                <td>$row[username]</td>
                <td>$row[password]</td>
                <td>$row[email]</td>
                <td>
                    <a class='btn btn-primary btn-sm' href='/website/edit_user.php?id=$row[user_id]'>Edit</a>
                    <a class='btn btn-danger btn-sm' href='/website/delete_user.php?id=$row[user_id]'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>
