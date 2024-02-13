

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">

 <link rel="stylesheet" href="homepage.css">
 <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,800;1,900&display=swap" rel="stylesheet">
 <title>Academic Registration</title>
</head>
<body>
 <header>
    <div class="container"> 
      <!-- Logo -->
      <img src="download.png" alt="Academic Registration Logo" class="logo">

      <!-- Navigation -->
      <nav>
        <ul>
          <!-- Dashboard -->
          <li><?php
           session_start();
            if ($_SESSION['role'] === 'Student') {
             echo '<li><a href="student_dashboard.php"><ion-icon name="grid-outline"></ion-icon>Dashboard</a></li>';
            } elseif ($_SESSION['role'] === 'admin') {    
              echo '<li><a href="admin.php"><ion-icon name="grid-outline"></ion-icon>Dashboard</a></li>';
            } else{
              echo '<li><a href="teacher_dashboard.php"><ion-icon name="grid-outline"></ion-icon>Dashboard</a></li>';
            }
            ?>

          <!-- Contacts -->
          <li><a href="contact.php"><ion-icon name="chatbox-ellipses-outline"></ion-icon>Contacts </a></li>

          <!-- Logout -->
          <li><a href="logout.php"><ion-icon name="power-outline"></ion-icon>Logout</a></li>
      
          <!-- End of Navigation -->
        </ul>
      </nav>
    </div>
 </header>

 <main>
    <!-- Hero Section -->
    <section class="hero">
      <img src="image.jpg"> 
    </section>
 </main>

 <!-- Footer -->
 <footer id="myFooter">
    <div class="container">
      <div class="footer-content">
        <!-- Footer Logo -->
        <div class="footer-logo">
          <img src="download.png" alt="Company Logo">
        </div>

        <!-- Footer Info -->
        <div class="footer-info">
          <p><ion-icon name="location-outline"></ion-icon>Address: nabateieh </p>                   
          <p><ion-icon name="mail-outline"></ion-icon>Email: Lu.Support@ul.edu.lb</p>                   
          <p><ion-icon name="call-outline"></ion-icon>Phone: +961-76-123 445</p>
        </div>

        <!-- Footer Rights -->
        <div class="rights">
          <p>&copy; 2023 Academic Registration. All rights reserved.</p>
        </div>
      </div>
    </div>
 </footer>

 <!-- Footer Reveal Script -->
 <script>
    document.addEventListener("DOMContentLoaded", function() {
      var footer = document.getElementById("myFooter");

      window.addEventListener("scroll", function() {
        var currentScroll = window.scrollY;

        if (currentScroll > 100) {
          footer.classList.add("reveal");
        }else {
          footer.classList.remove("reveal");
        }
      });
    });
 </script>

 <!-- Logo Opacity Script -->
 <script>
    var header = document.querySelector('header');
    var logo = document.querySelector('.logo');
    var lastScroll = 0;

    window.addEventListener('scroll', function() {
      var currentScroll = window.scrollY;

      if (currentScroll > lastScroll) {
        logo.style.opacity = '0';
      } else {
        logo.style.opacity = '1';
      }

      lastScroll = currentScroll;
    });
 </script>

 <!-- Scripts -->
 <script src="homepage.js"></script>
 <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
 <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
