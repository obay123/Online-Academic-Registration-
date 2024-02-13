<table class="table">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
       
        require_once 'db_connection.php';
        
        $sql = "SELECT * FROM users WHERE role='admin'";
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
              
            </tr>";
        }
        ?>
    </tbody>
</table>
