<!DOCTYPE html>
<html>
<head>
	<!-- *******  Link To CSS Style Sheet  ******* -->
	<link rel="stylesheet" type="text/css" href="contact.css">

	<!-- *******  Font Awesome Icons Link  ******* -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>

	<!-- *******  Link To Goggle Fonts  *******  -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,800;1,900&display=swap" rel="stylesheet">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Contact Section</title>
</head>
<body>
	<header>
<!--<div class="container"> 
      <img src="download.png" alt="Academic Registration Logo"  class="logo">
-->
<nav class="navbar">
        <ul>
		
            <li><a href="homepage.php"><ion-icon name="home-outline"></ion-icon>Home</a></li>
          <li><?php
           session_start();
        
            if ($_SESSION['role'] === 'student') {
                echo '<li><a href="student_dashboard.html"><ion-icon name="grid-outline"></ion-icon>Dashboard</a></li>';
            } elseif ($_SESSION['role'] === 'admin') {
                echo '<li><a href="admin.php"><ion-icon name="grid-outline"></ion-icon>Dashboard</a></li>';
            } else{
                echo '<li><a href="teacher_dashboard.php"><ion-icon name="grid-outline"></ion-icon>Dashboard</a></li>';
            }
            ?>
			
            <li><a href="logout.php"><ion-icon name="power-outline"></ion-icon>	Logout</a></li>
       
        </ul>
    </nav>
			
		</header>
		</div>

	<div class="container">
		<main class="row">
			
			<!--  *******   Left Section (Column) Starts   *******  -->

			<section class="col left">
				
				<!--  *******   Title Starts   *******  -->

				<div class="contactTitle">
					<h2>Contact Us</h2>
					<p>

"Feel free to get in touch with us! Whether you have questions, feedback, or just want to say hello, we're here to assist you."</p>
				</div>

				<!--  *******   Title Ends   *******  -->

				<!--  *******   Contact Info Starts   *******  -->

							<div class="contactInfo">
								
							<div class="iconGroup">
				<div class="icon">
					<i class="fa-solid fa-phone"></i>
				</div>
				<div class="details">
					<span>Phone</span>
					<a href="https://wa.me/961123546">+961 123 546</a>
				</div>
			</div>


									<div class="iconGroup">
					<div class="icon">
						<i class="fa-solid fa-envelope"></i>
					</div>
					<div class="details">
						<span>Email</span>
						<a href="mailto:Lu.Support@ul.edu.lb">Lu.Support@ul.edu.lb</a>
					</div>
				</div>


					<div class="iconGroup">
					<div class="icon">
					
							<i class="fa-solid fa-location-dot"></i>
				
					</div>
					<div class="details">
						<span>Location</span>
						<span><a href="https://www.google.com/maps/search/?api=1&query=9FHW+4PM  Nabatieh" target="_blank">Nabatieh</a></span>
					</div>
				</div>

				</div>

				<!--  *******   Contact Info Ends   *******  -->

				<!--  *******   Social Media Starts   *******  -->

				<div class="socialMedia">
					<a href="https://www.facebook.com/ul.edu.lb"><i class="fa-brands fa-facebook-f"></i></a>
					<a href="#"><i class="fa-brands fa-twitter"></i></a>
					<a href="#"><i class="fa-brands fa-instagram"></i></a>
					<a href="https://www.linkedin.com/school/lebanese.university/"><i class="fa-brands fa-linkedin-in"></i></a>
				</div>

				<!--  *******   Social Media Ends   *******  -->

			</section>

			<!--  *******   Left Section (Column) Ends   *******  -->

			<!--  *******   Right Section (Column) Starts   *******  -->

			<section class="col right">
				
				<!--  *******   Form Starts   *******  -->

				<form class="messageForm" method="POST" action="contact.php">
					
					<div class="inputGroup halfWidth">
						<input type="text" name="name" required="required">
						<label>Your Name</label>
					</div>

					<div class="inputGroup halfWidth">
						<input type="email" name="email" required="required">
						<label>Email</label>
					</div>

					<div class="inputGroup fullWidth">
						<input type="text" name="subject" required="required">
						<label>Subject</label>
					</div>

					<div class="inputGroup fullWidth">
						<textarea required="required" name="message"></textarea>
						<label>Tell Us The problem</label>
					</div>

					<div class="inputGroup fullWidth">
						<button type="submit" name="send_message">Send Message</button>
					</div>

				</form>
				
				
				<!--  *******   Form Ends   *******  -->
			</section>

			<!--  *******   Right Section (Column) Ends   *******  -->

		</main>
	</div>
	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>