<?php

// Retrieve and sanitize form data and email to empty string if not set
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';

?>

<!DOCTYPE html>
<html lang="lt">
  <head>
    <meta charset="UTF-8" />
    <title>Apmokėjimas - Fotokursas.lt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="verify-paysera" content="67286da624b639b8633b9cb2630a4cd1" />
    <link
      rel="stylesheet"
      id="css"
      href="styles.css"
      type="text/css"
      media="all" />
    <style>
      body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        background-color: aliceblue;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        flex-direction: column;
      }
      .container {
        max-width: 1200px;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
      }
      .user-details, .order-details {
        flex: 1 1 400px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      h2 {
        text-align: center;
        color: #333333;
        margin-top: 0;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }
      table th,
      table td {
        border: 1px solid #dddddd;
        padding: 12px;
        text-align: left;
      }
      table th {
        background-color: #f2f2f2;
      }
      .total-row {
        font-weight: bold;
      }
      a {
        color: #fc9002;
        text-decoration: none;
      }
      input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        margin-top: 5px;
        margin-bottom: 10px;
        font-size: 16px;
      }
      input[type="text"]:focus {
        outline: none;
        border-color: #fc9002;
        box-shadow: 0 0 5px #fc9002;
      }
      .button {
        width: 100px;
        padding: 10px;
        border: 1px solid black;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #292929;
        color: #fff;
        cursor: pointer;
        margin-top: 15px;
        display: block;
        margin: 15px auto;
      }
      .button:hover {
        color: black;
        background-color: white;
        transition: background-color 0.4s ease;
      }
      .btn {
        padding: 6px;
        width: 100px;
        border: 0;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #9d6a89;
        color: white;
        text-align: center;
        cursor: pointer;
        margin-top: 25px;
        display: block;
        margin-left: auto;
        margin-right: auto;
      }
      .btn:hover {
        background-color: #401f3e;
        color: #fff;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="user-details">
        <h2>Pirkėjo duomenys</h2>
        <form method="post" action="redirect.php" autocomplete="on">
          <div>
            <label for="name">Vardas</label>
            <input type="text" id="name" name="name" required />
          </div>
          <div>
            <label for="surname">Pavardė</label>
            <input type="text" id="surname" name="surname" required />
          </div>
          <div>
            <label for="email">El.paštas</label>
            <input type="text" id="email" name="email" required readonly value="<?php echo htmlspecialchars($email); ?>" style="background-color: #f0f0f0; color: #a0a0a0;">
          </div>
          <div>
            <label for="phone">Telefonas</label>
            <input type="text" id="phone" name="phone" required />
          </div>
          <div>
            <input type="checkbox" name="terms" id="terms" required />
            <label for="terms">Aš perskaičiau ir sutinku su puslapio <a href="payserapolicy.html">sąlygomis ir taisyklėmis</a> <abbr class="required" title="privaloma"></abbr></label>
          </div>
          <button
            type="submit"
            class="button"
            name="place_order"
            id="place_order"
            value="Apmokėti"
            data-value="Apmokėti">
            Apmokėti
          </button>
        </form>
      </div>
      <div class="order-details">
        <h2>Jūsų užsakymas</h2>
        <table>
          <tr>
            <td>Būsiu fotografas (11 savaičių mokymų)</td>
            <td>489.00€</td>
          </tr>
          <tr>
            <td>Suma</td>
            <td>489.00€</td>
          </tr>
          <tr class="total-row">
            <td>Viso</td>
            <td>489.00€</td>
          </tr>
        </table>
      </div>
    </div>
    <a
      class="btn"
      href="https://heroku-calculator-a6b0384b6190.herokuapp.com/courses"
    >Back</a>
  </body>
</html>
