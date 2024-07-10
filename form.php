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
        padding: 20px;
      }
      .container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
      }
      .user-details {
        flex: 1 1 400px;
        margin-right: 20px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }
      .order-details {
        flex: 1 1 calc(50% - 40px);
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        margin-left: 5px;
        border: 1px solid black;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #292929;
        color: #fff;
        cursor: pointer;
        margin-top: 15px;
      }
      .button:hover {
        color: black;
        background-color: white;
        transition: background-color 0.4s ease;
      }
      .btn {
        padding: 6px;
        width: 100px;
        margin-bottom: 70px;
        border: 0;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #9d6a89;
        color: white;
        text-align: center;
        cursor: pointer;
        margin-top: 25px;
        margin-left: 470px;
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
          <tr>
            <td>Vardas</td>
            <td><input type="text" id="name" name="name" required /></td>
          </tr>
          <tr>
            <td>Pavardė</td>
            <td><input type="text" id="surname" name="surname" required /></td>
          </tr>
          <tr>
            <td>El.paštas</td>
            <input type="text" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
          </tr>
          <tr>
            <td>Telefonas</td>
            <td><input type="text" id="phone" name="phone" required /></td>
          </tr>
          <tr>
            <td>
              <input type="checkbox" name="terms" id="terms" required />
              <span
                >Aš perskaičiau ir sutinku su puslapio
                <a href="payserapolicy.html">sąlygomis ir taisyklėmis</a>
                <abbr class="required" title="privaloma"></abbr>
              </span>
            </td>
          </tr>
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
    <div class="container">
      <a
        class="btn"
        href="https://heroku-calculator-a6b0384b6190.herokuapp.com/courses"
        >Back</a
      >
    </div>
  </body>
</html>
