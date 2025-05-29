<?php
require '../includes/config.php'; // Adjust path as needed
$successMessage = '';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and escape user input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);

    try {
        // Prepare and bind
        $stmt = $pdo->prepare("INSERT INTO contact_form_submissions (first_name, last_name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
        
        // Execute the statement
        if ($stmt->execute([$first_name, $last_name, $email, $phone, $message])) {
            $successMessage = "Message sent successfully! we will reply soon!";
            
        } else {
            $successMessage = "Error: Could not send message.";
        }
    } catch (PDOException $e) {
        $successMessage = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Contact</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/contactcss.css">
    <link rel="icon" type="image/png" href="../images/image (4).png" >
  </head>
  <style>
    /* Footer */
.footer {
    background: #0D4715;
    color: white;
    padding: 4rem 2rem 2rem;
    
}

.footer .container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer .detail h3 {
    margin-bottom: 1rem;
}

.footer .social {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.footer .social a {
    color: white;
    font-size: 1.5rem;
}

.footer .about-us h4 {
    margin-bottom: 1rem;
}

.footer .about-us ul {
    list-style: none;
}

.footer .about-us ul li {
    margin-bottom: 0.5rem;
}

.footer .about-us ul li a {
    color: #ccc;
    text-decoration: none;
}

.footer .copyright {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    color: #ccc;
}

.footer .copyright a {
    color: #ccc;
    text-decoration: none;
    margin-left: 1rem;
}
  </style>
  <body>
    
    <section class = "contact-section">
      <div class = "contact-bg">
        <h3>Contact us today for a customized cleaning solution!</h3>
        <h2>contact us</h2>
        <div class = "line">
          <div></div>
          <div></div>
          <div></div>
        </div>
        <p class = "text">CleanAura Cleaning Services offers top-quality residential and commercial cleaning. Our expert team uses eco-friendly products to ensure spotless spaces. From deep cleaning to regular maintenance, we guarantee a fresh and inviting environment with professional care.</p>
      </div>


      <div class = "contact-body">
        <div class = "contact-info">
          <div>
            <span><i class = "fas fa-mobile-alt"></i></span>
            <span>Phone No.</span>
            <span class = "text">+213 5555555</span>
          </div>
          <div>
            <span><i class = "fas fa-envelope-open"></i></span>
            <span>E-mail</span>
            <span class = "text">cleanora@gmail.com</span>
          </div>
          <div>
            <span><i class = "fas fa-map-marker-alt"></i></span>
            <span>Address</span>
            <span class = "text">algeria</span>
          </div>
          <div>
            <span><i class = "fas fa-clock"></i></span>
            <span>Opening Hours</span>
            <span class = "text">24/7</span>
          </div>
        </div>

        <div class = "contact-form">
        <form action="" method="POST"> <!-- Action set to self -->
        <?php if ($successMessage): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
                <div>
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                </div>
                <div>
                    <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                    <input type="tel" name="phone" class="form-control" placeholder="Phone" required>
                </div>
                <textarea name="message" rows="5" placeholder="Message" class="form-control" required></textarea>
                <input type="submit" class="send-btn" value="Send Message">
            </form>

          <div>
            <img src = "../images/contactus.jpg" alt = "">
          </div>
        </div>
      </div>
      <div class="mapouter">
            <div class="gmap_canvas">
                <iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=670&amp;height=363&amp;hl=en&amp;q=University 08 Mai 1945 Guelmar&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
            </div>
        </div>
    
    </section>
    <footer>
        <section class="footer">
            <div class="container">
                <div class="detail">
                    <h3>Cleanora</h3>
                    <p>At Cleanora, we provide top-quality cleaning services tailored to your needs. Whether it's residential, commercial, or industrial cleaning, our dedicated team ensures a spotless and hygienic environment.</p>
                    <h5>Get in Touch</h5>
                    <a href="mailto:example@mail.com">cleanora@mail.com</a>
                    <div class="social">
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>
                <div class="about-us">
                    <h4>Help</h4>
                    <ul>
                        <li><a href="#FAQ">FAQ</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Site Map</a></li>
                    </ul>
                </div>
                <div class="about-us">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#services">Gallery</a></li>
                        <li><a href="#">Offers</a></li>
                    </ul>
                </div>
            </div>

            <div class="copyright">
                <div>
                    &copy; 2024 Cleanora, Inc. All rights reserved.
                </div>
                <div>
                    &copy; Developed by NextGen Coders.
                </div>
                <div>
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>
        </section>
    </footer>
    

  </body>
</html>