<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <title>amoSrm task</title>
</head>
<body>
<div id="container">
    <h1>&bull; amo SRM task &bull;</h1>
    <div class="underline">
    </div>
    <form action="send-contact.php" method="post" id="contact_form">
        <div class="name">
            <label for="name"></label>
            <input type="text" placeholder="name" name="name" id="name_input" required>
        </div>
        <div class="email">
            <label for="email"></label>
            <input type="email" placeholder="e-mail" name="email" id="email_input" required>
        </div>
        <div class="telephone">
            <label for="name"></label>
            <input type="text" placeholder="number" name="telephone" id="telephone_input" required>
        </div>
        <div class="price">
            <label for="name"></label>
            <input type="text" placeholder="price" name="price" id="price_input" required>
        </div>
        <div class="submit">
            <input type="submit" value="Send Message" id="form_button"/>
        </div>
    </form>
</div>
</body>
</html>