<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CleanAura | Online Payment</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/styleS3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <script src="https://kit.fontawesome.com/32c6516436.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styleP.css">
    <link rel="icon" type="image/png" href="../images/image (4).png" >

</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

form {
    max-width: 600px;
    margin: 40px auto;
    padding: 30px;
    background-color: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.title {
    text-align: center;
    color: #333;
    margin-bottom: 25px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.inputBox {
    margin-bottom: 20px;
}

.inputBox span {
    display: block;
    color: #666;
    margin-bottom: 8px;
    font-weight: 600;
}

.inputBox img {
    max-width: 100%;
    height: auto;
    margin-bottom: 15px;
    border-radius: 8px;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 5px rgba(74, 144, 226, 0.3);
}

.flex {
    display: flex;
    gap: 15px;
}

.flex .inputBox {
    flex: 1;
}

.submit-btn {
    width: 100%;
    padding: 15px;
    background-color: #4a90e2;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #357abd;
}

@media screen and (max-width: 480px) {
    form {
        margin: 20px 15px;
        padding: 20px;
    }

    .flex {
        flex-direction: column;
        gap: 15px;
    }

    input {
        padding: 10px;
        font-size: 14px;
    }

    .submit-btn {
        padding: 12px;
        font-size: 16px;
    }
}

</style>
  <body>
    
    <section class = "contact-section">
      <div class = "contact-bg">
        <br>
        <h3>Spotless spaces, hassle-free! Book our cleaning service today!</h3>
        <h2>Simulation</h2>
        <div class = "line">
            <div></div>
            <div></div>
            <div></div>
          </div>
        <p class = "text">CleanAura Cleaning Services offers top-quality residential and commercial cleaning. Our expert team uses eco-friendly products to ensure spotless spaces. From deep cleaning to regular maintenance, we guarantee a fresh and inviting environment with professional care.</p>
        </div>
        <form action="">
    
            <div class="row">
    
               
    
                <div class="col">
    
                    <h3 class="title">payment</h3>
    
                    <div class="inputBox">
                        <span>cards accepted :</span>
                        <img src="../images/Screenshot 2025-03-26 212510.png" alt="" width="80px" height="110px">
                    </div>
                    <div class="inputBox">
                        <span>name on card :</span>
                        <input type="text" placeholder="abdenour ounas">
                    </div>
                    <div class="inputBox">
                        <span>credit card number :</span>
                        <input type="number" placeholder="1111-2222-3333-4444">
                    </div>
                    <div class="inputBox">
    <span>exp date :</span>
    <div class="flex">
        <input type="number" placeholder="DD" min="1" max="31">
        <input type="text" placeholder="Month (e.g., January)">
        <input type="number" placeholder="YYYY" min="2024" max="2035">
    </div>
</div>

                        <div class="inputBox">
                            <span>CVV :</span>
                            <input type="text" placeholder="1234">
                        </div>
                    </div>
    
                </div>
        
            </div>
    
            <input type="submit" value="proceed to checkout" class="submit-btn">
    
        </form> 
      
    </div>
      <div class = "contact-footer">
        <h3>Follow Us</h3>
        <div class = "social-links">
          <a href = "#" class = "fab fa-facebook-f"></a>
          <a href = "#" class = "fab fa-twitter"></a>
          <a href = "#" class = "fab fa-instagram"></a>
          <a href = "#" class = "fab fa-linkedin"></a>
          <a href = "#" class = "fab fa-youtube"></a>
        </div>
      </div>
    </section>

    

  </body>
</html>