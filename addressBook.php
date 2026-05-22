<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Book</title>
    <link rel="stylesheet" href="css/addressBook.css">
</head>

<body>
    <?php
    include "header.php";

    $userID = $_SESSION['ID'] ?? null;
    auth(["customer"], $userID);

    if (!isset($userID)) {
        header("Location: login.php");
        exit;
    }
    //fetch user
    $stmt = $_db->prepare("SELECT * FROM users WHERE userID = ?");
    $stmt->execute([$userID]);
    $usersData = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $_db->prepare("SELECT * FROM address_book WHERE userID = ?");
    $stmt->execute([$userID]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="container">
        <aside class="sidebar">
            <div class="profile">
                <img src="<?= $usersData["profile_image"] ?>" alt="User Image">
                <p><?= $usersData["userName"] ?></p>
            </div>
            <nav>
                <ul>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="addressBook.php">Address Book</a></li>
                    <li><a href="orderHistory.php">Orders</a></li>
                    <li class="logout"><a href="logout.php">Log Out</a></li>
                </ul>
            </nav>
        </aside>

        <div class="content">
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'deleted'): ?>
                    <div class="alert success" id="successMsg">address successfully deleted.</div>
                <?php elseif ($_GET['msg'] === 'added'): ?>
                    <div class="alert success" id="successMsg">address successfully added.</div>
                <?php elseif ($_GET['msg'] === 'updated'): ?>
                    <div class="alert success" id="successMsg">address successfully updated.</div>
                <?php elseif ($_GET['msg'] === 'error'): ?>
                    <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
                <?php endif; ?>
            <?php endif; ?>

            <h2>MY ADDRESS BOOK <button class="btn" onclick="toggleForm()">ADD ADDRESS</button></h2>

            <?php foreach ($addresses as $addr): ?>
                <div class="section">
                    <p><strong><?= htmlspecialchars($addr['recipientName']) ?></strong></p>
                    <p><?= htmlspecialchars($addr['phoneNumber']) ?></p>
                    <p>
                        <?= htmlspecialchars($addr['shippingAddress']) ?><br>
                        <?= htmlspecialchars($addr['postalCode']) ?>, <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?><br>
                        <?= htmlspecialchars($addr['country']) ?>
                    </p>
                    <button
                        class="edit-btn"
                        data-id="<?= $addr['addressID'] ?>"
                        data-name="<?= htmlspecialchars($addr['recipientName']) ?>"
                        data-phone="<?= htmlspecialchars($addr['phoneNumber']) ?>"
                        data-city="<?= htmlspecialchars($addr['city']) ?>"
                        data-state="<?= htmlspecialchars($addr['state']) ?>"
                        data-postal="<?= htmlspecialchars($addr['postalCode']) ?>"
                        data-country="<?= htmlspecialchars($addr['country']) ?>"
                        data-address="<?= htmlspecialchars($addr['shippingAddress']) ?>">Change</button>
                    |
                    <form action="delete_address.php" method="POST" style="display:inline;">
                        <input type="hidden" name="addressID" value="<?= $addr['addressID'] ?>">
                        <button type="submit" onclick="return confirm('Delete this address?')">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="addressModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleForm()">&times;</span>
            <form action="add_address.php" method="POST" class="address-form">
                <h3 id="modal-title">Add New Address</h3>
                <input type="hidden" name="userId" value="<?= $userID ?>" />
                <input type="hidden" name="addressId" id="addressId" />
                <input type="text" name="fullName" id="fullName" placeholder="Full Name" required>
                <input type="text" name="phone" id="phone" placeholder="Phone Number" required>
                <input type="text" name="city" id="city" placeholder="City" required>
                <input type="text" name="state" id="state" placeholder="State" required>
                <input type="text" name="postalCode" id="postalCode" placeholder="Postal Code" required>
                <input type="text" name="country" id="country" placeholder="Country" required>
                <label> isDefault <input type="checkbox" name="makeDefault"></label>
                <textarea name="address" id="address" placeholder="Street Address" rows="5" cols="45" required></textarea>
                <button type="submit" class="btn">Save Address</button>
            </form>
        </div>
    </div>
    <script src="js/app.js"></script>
</body>

</html>