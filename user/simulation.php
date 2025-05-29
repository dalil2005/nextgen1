<?php
require_once '../includes/config.php'; // Keep database connection for price lookup
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $clientID = $_SESSION['client_id']; // Keep this for reference but won't save to DB
    $serviceCategory = $_POST['category'];
    $propertyCondition = $_POST['condition'];
    $paymentMethod = $_POST['payment'];
    $cleaningDate = $_POST['cleaningDate']; 
    
    // Get address components
    $city = $_POST['city'];
    $municipality = $_POST['municipality'];
    $buildingAddress = $_POST['building_address'];
    
    $price = 0;

    try {
        // Get pricing from database based on service category
        if ($serviceCategory == 'private') {
            $numberOfRooms = $_POST['rooms'];
            $stmt = $pdo->prepare("SELECT PricePerRoom FROM PrivateService WHERE RoomType = :condition");
            $stmt->bindParam(':condition', $propertyCondition);
            $stmt->execute();
            $pricePerRoom = $stmt->fetchColumn();
            $price = $pricePerRoom * $numberOfRooms;
            
            // Store private service specific details
            $_SESSION['order_rooms'] = $numberOfRooms;
            $_SESSION['order_details'] = $numberOfRooms . ' rooms';
            
        } else {
            $numberOfFloors = $_POST['floors'];
            $areaSize = $_POST['area'];

            $stmt = $pdo->prepare("SELECT PricePer50m2 FROM ProfessionalService WHERE BuildingType = :condition");
            $stmt->bindParam(':condition', $propertyCondition);
            $stmt->execute();
            $pricePer50m2 = $stmt->fetchColumn();
            $price = ($areaSize / 50) * $pricePer50m2;
            
            // Store professional service specific details
            $_SESSION['order_floors'] = $numberOfFloors;
            $_SESSION['order_area'] = $areaSize;
            $_SESSION['order_details'] = $areaSize . ' m² area, ' . $numberOfFloors . ' floors';
        }

        // Store all order details in session variables
        $_SESSION['order_price'] = $price;
        $_SESSION['order_category'] = $serviceCategory;
        $_SESSION['order_condition'] = $propertyCondition;
        $_SESSION['order_date'] = $cleaningDate;
        $_SESSION['order_payment'] = $paymentMethod;
        $_SESSION['order_address'] = $city . ', ' . $municipality . ', ' . $buildingAddress;
        $_SESSION['order_status'] = 'pending';
        
        // Redirect to confirmation page
        header('Location: ../user/confirmation.php');
        exit;

    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        echo "An error occurred while retrieving pricing information. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Simulation</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/styleS3.css">
    <link rel="stylesheet" href="../css/styleS2.css">
    <link rel="icon" type="image/png" href="../images/image (4).png">
    <script src="https://kit.fontawesome.com/32c6516436.js" crossorigin="anonymous"></script>
    <style>
      :root {
    --primary-color: #15c455;
    --primary-dark: #15c455;
    --text-color: #333;
    --background-color: rgba(255, 255, 255, 0.95);
    --white: #ffffff;
    --gray-light: #f1f1f1;
    --border-radius: 12px;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}
.header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    padding: 2rem 1rem;
    text-align: center;
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 6px #6d9a78;
}

.header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px #5a8464;
}

.breadcrumb {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.breadcrumb a {
    color: var(--white);
    text-decoration: none;
    transition: var(--transition);
}

.breadcrumb a:hover {
    opacity: 0.8;
}

.container {
    max-width: 850px;
    margin: 10px 10px;
    padding: 8 10;
    position: relative;
    z-index: 1;
}

.form-container {
    background: var(--background-color);
    padding: 2.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    backdrop-filter: blur(10px);
}
.subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.required-fields {
    text-align: center;
    font-size: 0.9rem;
    color: #888;
    margin-bottom: 2rem;
}

.quote-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.service-categories {
    margin-bottom: 2rem;
}

.category-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.category-card {
    position: relative;
}

.category-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.category-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.category-icon {
    font-size: 20px;
    margin-bottom: 1rem;
}
.category-icon span i{
    width: 20px;
}


.category-content h4 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.category-content p {
    font-size: 0.9rem;
    color: #666;
}

.category-card input[type="radio"]:checked + .category-content {
    border: 2px solid var(--primary-color);
    transform: translateY(-4px);
    box-shadow: 0 8px 16px #8FBB99;
}

.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1rem;
    display: none;
}

.private-services,
.professional-services,
.private-fields,
.professional-fields {
    display: none;
}

input[name="category"][value="private"]:checked ~ .private-services,
input[name="category"][value="private"]:checked ~ .private-fields {
    display: grid;
}

input[name="category"][value="professional"]:checked ~ .professional-services,
input[name="category"][value="professional"]:checked ~ .professional-fields {
    display: grid;
}

.service-card {
    position: relative;
}

.service-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.service-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
    height: 100%;
}

.service-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.service-label h3 {
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.service-label p {
    font-size: 0.9rem;
    color: #666;
}

.service-card input[type="radio"]:checked + .service-label {
    border: 2px solid var(--primary-color);
    transform: translateY(-4px);
    box-shadow: 0 8px 16px #8FBB99;
}

.form-group {
    flex: 1;
}

.form-row {
    display: flex;
    gap: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 500;
    font-size: 1.1rem;
}

.icon {
    margin-right: 0.5rem;
}

input, .styled-select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #ddd;
    border-radius: var(--border-radius);
    font-size: 1rem;
    background-color: var(--white);
    transition: var(--transition);
}

input:focus, .styled-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.styled-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}

.condition-options, .payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.condition-card, .payment-card {
    position: relative;
    border: 2px solid #D3D3D3;
    border-radius: 15px;
}

.condition-card input[type="radio"],
.payment-card input[type="radio"] {
    position: absolute;
    opacity: 0;
   
}

.condition-content, .payment-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.25rem;
    background: var(--white);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.condition-icon, .payment-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.condition-card input[type="radio"]:checked + .condition-content,
.payment-card input[type="radio"]:checked + .payment-content {
    border: 4px solid var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.submit-button {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    border: none;
    border-radius: 2rem;
    padding: 1.25rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
    margin-top: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.submit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px #8FBB99;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .container {
        margin-top: -2rem;
    }
    
    .form-container {
        padding: 1.5rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }

    .category-options {
        grid-template-columns: 1fr;
    }

    .service-grid {
        grid-template-columns: 1fr;
    }

    .condition-options, .payment-options {
        grid-template-columns: 1fr;
    }
}
.form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .container {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<section class="contact-section">
    <div class="contact-bg">
        <h3>Spotless spaces, hassle-free! Book our cleaning service today!</h3>
        <h2>Simulation</h2>
        <p class="text">CleanAura Cleaning Services offers top-quality residential and commercial cleaning...</p>
    </div>

    <div class="form-container">
        <h2>Get Your Personalized Cleaning Quote</h2>
        <form class="quote-form" id="quoteForm" method="POST" action="">
            <div class="service-categories">
                <h3>Select Service Category</h3>
                <div class="category-options">
                    <label class="category-card">
                        <input type="radio" name="category" value="private" required>
                        <span class="category-content">
                            <span class="category-icon"><i class="fa-solid fa-house fa-lg" style="color: #47f069;"></i></span>
                            <h4>Private Services</h4>
                            <p>For homes and residential properties</p>
                        </span>
                    </label>
                    <label class="category-card">
                        <input type="radio" name="category" value="professional">
                        <span class="category-content">
                            <span class="category-icon"><i class="fa-solid fa-building fa-lg" style="color: #4bec73;"></i></span>
                            <h4>Professional Services</h4>
                            <p>For businesses and commercial properties</p>
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-row private-fields" style="display: none;">
                <div class="form-group">
                    <label>Number of Rooms</label>
                    <input type="number" name="rooms" placeholder="Number of rooms" min="1">
                </div>
            </div>

            <div class="form-row professional-fields" style="display: none;">
                <div class="form-group">
                    <label>Number of Floors</label>
                    <input type="number" name="floors" placeholder="Number of floors" min="1">
                </div>
                <div class="form-group">
                    <label>Area Size (m²)</label>
                    <input type="number" name="area" placeholder="Area size in square meters" min="1">
                </div>
            </div>

            <div class="form-group">
                <label>Property Condition</label>
                <div class="condition-options">
                    <label class="condition-card">
                        <input type="radio" name="condition" value="furnished" required>
                        <span class="condition-content">Furnished</span>
                    </label>
                    <label class="condition-card">
                        <input type="radio" name="condition" value="empty">
                        <span class="condition-content">Empty</span>
                    </label>
                    <label class="condition-card">
                        <input type="radio" name="condition" value="old">
                        <span class="condition-content">Old Building</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <div class="payment-options">
                    <label class="payment-card">
                        <input type="radio" name="payment" value="online" required>
                        <span class="payment-content">Online Payment</span>
                    </label>
                    <label class="payment-card">
                        <input type="radio" name="payment" value="cash">
                        <span class="payment-content">Cash on Delivery</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-calendar icon"></i>Preferred Cleaning Date</label>
                <input type="date" name="cleaningDate" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label><i class="fa-solid fa-location-dot icon"></i>Address Details</label>
                <div class="form-row">
                <div class="form-group">
        <label for="city">city</label>
        <select id="city" name="city" required>
            <option value="" disabled selected>Select Wilaya</option>
            <option value="Adrar">01 - Adrar</option>
            <option value="Chlef">02 - Chlef</option>
            <option value="Laghouat">03 - Laghouat</option>
            <option value="Oum El Bouaghi">04 - Oum El Bouaghi</option>
            <option value="Batna">05 - Batna</option>
            <option value="Bejaia">06 - Bejaia</option>
            <option value="Biskra">07 - Biskra</option>
            <option value="Bechar">08 - Bechar</option>
            <option value="Blida">09 - Blida</option>
            <option value="Bouira">10 - Bouira</option>
            <option value="Tamanrasset">11 - Tamanrasset</option>
            <option value="Tebessa">12 - Tebessa</option>
            <option value="Tlemcen">13 - Tlemcen</option>
            <option value="Tiaret">14 - Tiaret</option>
            <option value="Tizi Ouzou">15 - Tizi Ouzou</option>
            <option value="Alger">16 - Alger</option>
            <option value="Djelfa">17 - Djelfa</option>
            <option value="Jijel">18 - Jijel</option>
            <option value="Setif">19 - Setif</option>
            <option value="Saida">20 - Saida</option>
            <option value="Skikda">21 - Skikda</option>
            <option value="Sidi Bel Abbes">22 - Sidi Bel Abbes</option>
            <option value="Annaba">23 - Annaba</option>
            <option value="Guelma">24 - Guelma</option>
            <option value="Constantine">25 - Constantine</option>
            <option value="Medea">26 - Medea</option>
            <option value="Mostaganem">27 - Mostaganem</option>
            <option value="MSila">28 - M'Sila</option>
            <option value="Mascara">29 - Mascara</option>
            <option value="Ouargla">30 - Ouargla</option>
            <option value="Oran">31 - Oran</option>
            <option value="El Bayadh">32 - El Bayadh</option>
            <option value="Illizi">33 - Illizi</option>
            <option value="Bordj Bou Arreridj">34 - Bordj Bou Arreridj</option>
            <option value="Boumerdes">35 - Boumerdes</option>
            <option value="El Tarf">36 - El Tarf</option>
            <option value="Tindouf">37 - Tindouf</option>
            <option value="Tissemsilt">38 - Tissemsilt</option>
            <option value="El Oued">39 - El Oued</option>
            <option value="Khenchela">40 - Khenchela</option>
            <option value="Souk Ahras">41 - Souk Ahras</option>
            <option value="Tipaza">42 - Tipaza</option>
            <option value="Mila">43 - Mila</option>
            <option value="Ain Defla">44 - Ain Defla</option>
            <option value="Naama">45 - Naama</option>
            <option value="Ain Temouchent">46 - Ain Temouchent</option>
            <option value="Ghardaia">47 - Ghardaia</option>
            <option value="Relizane">48 - Relizane</option>
            <option value="Timimoun">49 - Timimoun</option>
            <option value="Bordj Badji Mokhtar">50 - Bordj Badji Mokhtar</option>
            <option value="Ouled Djellal">51 - Ouled Djellal</option>
            <option value="Béni Abbès">52 - Béni Abbès</option>
            <option value="In Salah">53 - In Salah</option>
            <option value="In Guezzam">54 - In Guezzam</option>
            <option value="Touggourt">55 - Touggourt</option>
            <option value="Djanet">56 - Djanet</option>
            <option value="El MGhair">57 - El M'Ghair</option>
            <option value="El Meniaa">58 - El Meniaa</option>
        </select>
    </div>
    <div class="form-group">
        <label for="municipality">Municipality</label>
        <select id="municipality" name="municipality" required>
            <option value="" disabled selected>Select Municipality</option>
        </select>
    </div>
                </div>
                <div class="form-group">
                    <input type="text" name="building_address" placeholder="Building Address (Street, Number, etc.)" required>
                </div>
            </div>
            
            <button type="submit" class="submit-button">Request Cleaning Service</button>
        </form>
    </div>

    <div class="contact-footer">
        <h3>Follow Us</h3>
        <div class="social-links">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
            <a href="#" class="fab fa-youtube"></a>
        </div>
    </div>
</section>

<script>
    // JavaScript to toggle visibility of fields based on selected category
    document.querySelectorAll('input[name="category"]').forEach((elem) => {
        elem.addEventListener("change", function(event) {
            const privateFields = document.querySelector('.private-fields');
            const professionalFields = document.querySelector('.professional-fields');

            if (event.target.value === 'private') {
                privateFields.style.display = 'flex';
                professionalFields.style.display = 'none';
            } else {
                privateFields.style.display = 'none';
                professionalFields.style.display = 'flex';
            }
        });
    });
    const algeriaData = {
        "Adrar": ["Adrar", "Bouda", "Ouled Ahmed Timmi", "Reggane", "Timimoun", "Tsabit", "Zaouiet Kounta", "Aoulef", "Tamekten", "Tamest", "Tit"],
        "Chlef": ["Chlef", "Tenes", "Beni Haoua", "El Karimia", "Tadjena", "Taougrite", "Beni Bouattab", "Sobha", "Harchoun", "Ouled Fares", "Sidi Akkacha", "Boukadir", "Beni Rached", "Oued Fodda", "Abou El Hassan"],
        "Laghouat": ["Laghouat", "Aflou", "Ksar El Hirane", "Hassi R'Mel", "Ain Madhi", "Gueltat Sidi Saad", "Brida", "El Assafia", "Hadj Mechri", "Sebgag", "Tadjemout", "Tadjrouna"],
        "Oum El Bouaghi": ["Oum El Bouaghi", "Ain Beida", "Ain M'Lila", "F'kirina", "Ain Kercha", "Ain Babouche", "Berriche", "Ouled Hamla", "Dhalaa", "Ain Zitoun", "Souk Naamane", "Sigus", "El Belala", "Ain Fakroun"],
        "Batna": ["Batna", "Arris", "N'Gaous", "Merouana", "Seriana", "Menaa", "Tazoult", "Timgad", "Barika", "Djezzar", "Tkout", "Ain Touta", "Theniet El Abed", "Tighanimine", "Chemora"],
        "Bejaia": ["Bejaia", "Akbou", "Seddouk", "Tichy", "Amizour", "El Kseur", "Tazmalt", "Aokas", "Adekar", "Akfadou", "Chemini", "Souk El Tenine", "Taskriout", "Ighil Ali", "Barbacha"],
        "Biskra": ["Biskra", "Ouled Djellal", "Sidi Khaled", "Sidi Okba", "Tolga", "Zeribet El Oued", "El Kantara", "El Outaya", "Djemorah", "M'Chouneche", "Foughala", "El Hadjeb", "Lioua", "Bouchagroune", "Chetma"],
        "Bechar": ["Bechar", "Abadla", "Beni Abbes", "Kenadsa", "Mechraa Houari Boumediene", "Taghit", "Lahmar", "Mogheul", "Beni Ounif", "Erg Ferradj", "Meridja", "Tabelbala", "Boukais"],
        "Blida": ["Blida", "Boufarik", "Bougara", "El Affroun", "Meftah", "Ouled Yaich", "Bouinan", "Chebli", "Chiffa", "Guerrouaou", "Larbaa", "Mouzaia", "Oued El Alleug", "Soumaa", "Beni Tamou"],
        "Bouira": ["Bouira", "Ain Bessam", "Kadiria", "Lakhdaria", "M'Chedallah", "Sour El Ghozlane", "Bechloul", "Bir Ghbalou", "Dirah", "El Asnam", "Haizer", "Bordj Okhriss", "Taguedit", "Ahnif", "Chorfa"],
        "Tamanrasset": ["Tamanrasset", "In Salah", "In Ghar", "Abalessa", "Idles", "Tazrouk", "In Amguel", "In Guezzam", "Tin Zaouatine"],
        "Tebessa": ["Tebessa", "Bir El Ater", "Cheria", "El Aouinet", "El Kouif", "El Ma Labiodh", "Negrine", "Ouenza", "El Ogla", "Hammamet", "Morsott", "Bir El Mokadem", "Boulhaf Dir", "Thlidjene", "Ain Zerga"],
        "Tlemcen": ["Tlemcen", "Beni Mester", "Chetouane", "Ain Tallout", "Bab El Assa", "Beni Boussaid", "Beni Snous", "Ghazaouet", "Hennaya", "Maghnia", "Mansourah", "Nedroma", "Ouled Mimoun", "Remchi", "Sebdou"],
        "Tiaret": ["Tiaret", "Ain Deheb", "Ain Kermes", "Dahmouni", "Frenda", "Hamadia", "Ksar Chellala", "Medroussa", "Mecheria", "Mellakou", "Oued Lili", "Rahouia", "Sougueur", "Mahdia", "Sebaine"],
        "Tizi Ouzou": ["Tizi Ouzou", "Ain El Hammam", "Azazga", "Beni Douala", "Beni Yenni", "Boghni", "Draa Ben Khedda", "Draa El Mizan", "Iferhounene", "Larbaa Nath Irathen", "Maatkas", "Mekla", "Ouacif", "Ouadhia", "Tizi Gheniff"],
        "Alger": ["Alger Centre", "Bab El Oued", "Bab Ezzouar", "Baraki", "Belouizdad", "Bir Mourad Rais", "Birtouta", "Bordj El Bahri", "Bordj El Kiffan", "Dar El Beida", "Djasr Kasentina", "Draria", "El Harrach", "El Mouradia", "Hussein Dey", "Hydra", "Kouba", "Mohammadia", "Oued Smar", "Reghaia", "Rouiba", "Saoula", "Zeralda"],
        "Djelfa": ["Djelfa", "Ain El Ibel", "Ain Oussera", "Birine", "Charef", "El Idrissia", "Hassi Bahbah", "Messaad", "Dar Chioukh", "Deldoul", "El Guedid", "Faidh El Botma", "Had Sahary", "Sidi Laadjel", "Zaccar"],
        "Jijel": ["Jijel", "Chahna", "El Aouana", "El Milia", "Taher", "Texenna", "Ziama Mansouriah", "Bordj Tahar", "Djimla", "El Ancer", "Eraguene", "Ouled Yahia Khedrouche", "Selma Benziada", "Settara", "Sidi Abdelaziz"],
        "Setif": ["Setif", "Ain Azel", "Ain El Kebira", "Ain Oulmene", "Amoucha", "Beni Aziz", "Beni Ourtilane", "Bouandas", "Bougaa", "Djemila", "El Eulma", "Guenzet", "Hammam Sokhna", "Maoklane", "Salah Bey"],
        "Saida": ["Saida", "Ain El Hadjar", "Doui Thabet", "Moulay Larbi", "Ouled Brahim", "Sidi Amar", "Sidi Boubekeur", "Youb", "Ain Sekhouna", "El Hassasna", "Maamora", "Sidi Ahmed", "Tircine"],
        "Skikda": ["Skikda", "Azzaba", "Collo", "El Hadaiek", "El Harrouch", "Tamalous", "Ain Kechra", "Beni Zid", "Bin El Ouiden", "Djendel Saadi Mohamed", "Emjez Edchich", "Es Sebt", "Filfila", "Kerkera", "Oued Zehour"],
        "Sidi Bel Abbes": ["Sidi Bel Abbes", "Ain El Berd", "Ben Badis", "Mostefa Ben Brahim", "Sfisef", "Telagh", "Tenira", "Tessala", "Ain Thrid", "Amarnas", "Belarbi", "Boudjebaa El Bordj", "Boukhanefis", "Chetouane Belaila", "Hassi Dahou"],
        "Annaba": ["Annaba", "Berrahal", "El Bouni", "El Hadjar", "Ain Berda", "Chetaibi", "Cheurfa", "El Eulma", "Eulma", "Oued El Aneb", "Seraidi", "Sidi Amar", "Treat"],
        "Guelma": ["Guelma", "Ain Ben Beida", "Ain Makhlouf", "Beni Mezline", "Bouchegouf", "Bouhamdane", "Bou Hachana", "Bouati Mahmoud", "Djeballah Khemissi", "Hammam Debagh", "Hammam N'Bail", "Heliopolis", "Houari Boumediene", "Oued Zenati", "Roknia"],
        "Constantine": ["Constantine", "Ain Abid", "Ain Smara", "Didouche Mourad", "El Khroub", "Hamma Bouziane", "Ibn Badis", "Ibn Ziad", "Messaoud Boudjeriou", "Ouled Rahmoune", "Zighoud Youcef", "Beni Hamidene"],
        "Medea": ["Medea", "Ain Boucif", "Berrouaghia", "Chahbounia", "El Azizia", "Ksar El Boukhari", "Ouamri", "Ouled Antar", "Seghouane", "Tablat", "Ain Ouksir", "Aissaouia", "Aziz", "Baata", "Benchicao"],
        "Mostaganem": ["Mostaganem", "Ain Nouissy", "Ain Tadles", "Bouguirat", "Hassi Mameche", "Kheir Eddine", "Mesra", "Sidi Ali", "Sidi Lakhdar", "Achaacha", "Ain Boudinar", "Ain Sidi Cherif", "El Hassiane", "Fornaka", "Hadjadj"],
        "MSila": ["M'Sila", "Ain El Hadjel", "Ain El Melh", "Beni Ilmane", "Bou Saada", "Chellal", "Hammam Dalaa", "Magra", "Sidi Aissa", "Berhoum", "Djebel Messaad", "Khoubana", "Medjedel", "Ouanougha", "Ouled Derradj"],
        "Mascara": ["Mascara", "Ain Fares", "Ain Fekan", "Bouhanifia", "El Bordj", "Ghriss", "Hachem", "Mohammadia", "Oued El Abtal", "Sig", "Tighennif", "Zahana", "El Ghomri", "El Keurt", "El Menaouer"],
        "Ouargla": ["Ouargla", "Ain Beida", "El Borma", "Hassi Messaoud", "N'Goussa", "Rouissat", "Sidi Khouiled", "Taibet", "Touggourt", "El Hadjira", "El Allia", "Bennaceur", "Blidet Amor", "Megarine", "M'Naguer"],
        "Oran": ["Oran", "Ain Turk", "Arzew", "Bethioua", "Bir El Djir", "Es Senia", "Gdyel", "Hassi Bounif", "Mers El Kebir", "Misserghin", "Oued Tlelat", "Bousfer", "El Ancor", "El Braya", "El Kerma"],
        "El Bayadh": ["El Bayadh", "Bougtob", "Brezina", "Boualem", "Chellala", "El Abiodh Sidi Cheikh", "Rogassa", "Ain El Orak", "Arbaouat", "Bougtoub", "Boussemghoun", "Cheguig", "El Bnoud", "El Kheiter", "Ghassoul"],
        "Illizi": ["Illizi", "Djanet", "Bordj El Houasse", "Bordj Omar Driss", "Debdeb", "In Amenas"],
        "Bordj Bou Arreridj": ["Bordj Bou Arreridj", "Ain Taghrout", "Bir Kasdali", "Bordj Ghedir", "Bordj Zemoura", "El Achir", "El Hamadia", "El Mansourah", "Medjana", "Ras El Oued", "Sidi Embarek", "Ain Tesra", "Ben Daoud", "Belimour", "Djaafra"],
        "Boumerdes": ["Boumerdes", "Baghlia", "Bordj Menaiel", "Boudouaou", "Dellys", "Isser", "Naciria", "Thenia", "Tidjelabine", "Ain Tagourait", "Ammal", "Beni Amrane", "Ben Choud", "Corso", "Djinet"],
        "El Tarf": ["El Tarf", "Ben M'Hidi", "Besbes", "Bougous", "Bouhadjar", "Chefia", "Drean", "El Aioun", "El Kala", "Ain Kerma", "Asfour", "Berrihane", "Bougous", "Chebaita Mokhtar", "Chihani"],
        "Tindouf": ["Tindouf", "Oum El Assel"],
        "Tissemsilt": ["Tissemsilt", "Ammari", "Bordj Bou Naama", "Beni Chaib", "Khemisti", "Lazharia", "Lardjem", "Ouled Bessam", "Theniet El Had", "Boucaid", "Layoune", "Maalcem", "Melaab", "Ouled Bessam", "Sidi Abed"],
        "El Oued": ["El Oued", "Bayadha", "Debila", "Djamaa", "Guemar", "Hassi Khalifa", "Magrane", "Mih Ouansa", "Reguiba", "Robbah", "Taibet", "Taleb Larbi", "Hassani Abdelkrim", "Ourmes", "Sidi Amrane"],
        "Khenchela": ["Khenchela", "Ain Touila", "Bouhmama", "Chechar", "Djellal", "El Hamma", "Kais", "Ouled Rechache", "Babar", "Baghai", "Chelia", "El Mahmal", "Ensigha", "Khirane", "M'Sara"],
        "Souk Ahras": ["Souk Ahras", "Sedrata", "Mdaourouch", "Taoura", "Haddada", "Khedara", "Merahna", "Ouled Driss", "Mechroha", "Ouled Moumen", "Bir Bouhouche", "Sidi Fredj", "Safel El Ouiden", "Tiffech", "Zaarouria"],
        "Tipaza": ["Tipaza", "Ahmer El Ain", "Bourkika", "Cherchell", "Damous", "Fouka", "Gouraya", "Hadjout", "Kolea", "Sidi Amar", "Aghbal", "Ain Tagourait", "Beni Milleuk", "Bouharoun", "Chaiba"],
        "Mila": ["Mila", "Ain Beida Harriche", "Chelghoum Laid", "Ferdjioua", "Oued Athmania", "Tadjenanet", "Teleghma", "Terrai Bainen", "Tassadane Haddada", "Ain Tine", "Amira Arres", "Bouhatem", "Derradji Bousselah", "El Mechira", "Grarem Gouga"],
        "Ain Defla": ["Ain Defla", "Ain Lechiakh", "Ain Soltane", "El Abadia", "El Attaf", "Djendel", "Khemis Miliana", "Miliana", "Rouina", "Bordj Emir Khaled", "Djemaa Ouled Cheikh", "El Amra", "El Hassania", "Hammam Righa", "Mekhatria"],
        "Naama": ["Naama", "Ain Sefra", "Asla", "Mecheria", "Moghrar", "Mekmen Ben Amar", "Tiout", "Sfissifa", "Kasdir", "Ain Ben Khelil", "Djeniene Bourezg"],
        "Ain Temouchent": ["Ain Temouchent", "Ain Kihal", "Ain Tolba", "Beni Saf", "El Malah", "Hammam Bouhadjar", "El Amria", "Hassi El Ghella", "Oulhaca El Gheraba", "Ouled Kihal", "Sidi Benadda", "Sidi Boumediene", "Sidi Safi", "Tamzoura", "Terga"],
        "Ghardaia": ["Ghardaia", "Berriane", "Bounoura", "El Atteuf", "El Guerrara", "Metlili", "Zelfana", "Daya Ben Dahoua", "El Meniaa", "Hassi Fehal", "Hassi Gara", "Mansoura", "Sebseb"],
        "Relizane": ["Relizane", "Ammi Moussa", "Djidiouia", "El Hamadna", "El Matmar", "Mazouna", "Mendes", "Oued Rhiou", "Yellel", "Zemmoura", "Ain Rahma", "Beni Dergoun", "Beni Zentis", "Had Chekala", "Kalaa"],
        "Timimoun": ["Timimoun", "Ouled Said", "Ouled Aissa", "Metarfa", "Tinerkouk", "Ksar Kaddour", "Charouine", "Talmine", "Ouled Rached", "Deldoul"],
        "Bordj Badji Mokhtar": ["Bordj Badji Mokhtar", "Timiaouine"],
        "Ouled Djellal": ["Ouled Djellal", "Doucen", "Sidi Khaled", "Ras El Miaad", "Besbes", "Chaiba"],
        "Béni Abbès": ["Béni Abbès", "Igli", "El Ouata", "Tabelbala", "Taghit", "Tamtert", "Kerzaz", "Timoudi", "Beni Ikhlef", "Ksabi"],
        "In Salah": ["In Salah", "Foggaret Ezzaouia", "Inghar"],
        "In Guezzam": ["In Guezzam", "Tin Zaouatine"],
        "Touggourt": ["Touggourt", "Nezla", "Tebesbest", "Zaouia El Abidia", "Sidi Slimane", "Megarine", "Taibet", "Temacine", "Blidet Amor", "El Hadjira"],
        "Djanet": ["Djanet", "Bordj El Houasse"],
        "El MGhair": ["El M'Ghair", "Sidi Khelil", "Oum Touyour", "Still", "Djamaa", "Sidi Amrane", "Tendla", "M'Rara"],
        "El Meniaa": ["El Meniaa", "Hassi Gara", "Hassi Fehal"]
    };
    
    const wilayaSelect = document.getElementById('city');
    const municipalitySelect = document.getElementById('municipality');
    
    wilayaSelect.addEventListener('change', () => {
        const selectedWilaya = wilayaSelect.value;
        const municipalities = algeriaData[selectedWilaya] || [];
        
        // Clear previous Municipality options
        municipalitySelect.innerHTML = '<option value="" disabled selected>Select Municipality</option>';
        
        // Populate Municipality dropdown
        municipalities.forEach(municipality => {
            const option = document.createElement('option');
            option.value = municipality;
            option.textContent = municipality;
            municipalitySelect.appendChild(option);
        });
    });
</script>

</body>
</html>