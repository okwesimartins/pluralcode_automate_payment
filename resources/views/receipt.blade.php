

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
             .email {
        max-width: 480px;
        margin: 1rem auto;
        border-radius: 10px;
        border-top:  #25067C 2px solid;
        border-bottom:  #25067C 2px solid;
        box-shadow: 0 2px 18px rgba(0, 0, 0, 0.2);
        padding: 1.5rem;
        font-family: Arial, Helvetica, sans-serif;
      }
      .email .email-head {
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
        padding-bottom: 1rem;
      }
      .email .email-head .head-img {
        max-width: 240px;
        padding: 0 0.5rem;
        display: block;
        margin: 0 auto;
      }

      .email .email-head .head-img img {
        width: 100%;
      }
      .email-body .invoice-icon {
        max-width: 80px;
        margin: 1rem auto;
      }
      .email-body .invoice-icon img {
        width: 100%;
      }

      .email-body .body-text {
        padding: 2rem 0 1rem;
        text-align: center;
        font-size: 1.15rem;
      }
      .email-body .body-text.bottom-text {
        padding: 2rem 0 1rem;
        text-align: center;
        font-size: 0.8rem;
      }
      .email-body .body-text .body-greeting {
        font-weight: bold;
        margin-bottom: 1rem;
      }

      .email-body .body-table {
        text-align: left;
      }
      .email-body .body-table table {
        width: 100%;
        font-size: 1.1rem;
      }
      .email-body .body-table table .total {
        background-color: #E5E5E5;
        border-radius: 8px;
        color: #25067C;
      }
      .email-body .body-table table .item {
        border-radius: 8px;
        color: #d74034;
      }
      .email-body .body-table table th,
      .email-body .body-table table td {
        padding: 10px;
      }
      .email-body .body-table table tr:first-child th {
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
      }
      .email-body .body-table table tr td:last-child {
        text-align: right;
      }
      .email-body .body-table table tr th:last-child {
        text-align: right;
      }
      .email-body .body-table table tr:last-child th:first-child {
        border-radius: 8px 0 0 8px;
      }
      .email-body .body-table table tr:last-child th:last-child {
        border-radius: 0 8px 8px 0;
      }
      .email-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.2);
      }
      .email-footer .footer-text {
        font-size: 0.8rem;
        text-align: center;
        padding-top: 1rem;
      }
      .email-footer .footer-text a {
        color: #25067C;
      }
    </style>
</head>
<body>
    <div class="email">
      <div class="email-head">
        <div class="head-img">
          <img
            src="https://wpfystatic.b-cdn.net/rahul/email.png"
            alt="damnitrahul-logo"
          />
        </div>
      </div>
      <div class="email-body">
        <div class="body-text">
          <div class="body-greeting">
            Hi, Rahul!
          </div>
          Your order has been successfully completed and delivered to You!
        </div>
        <div class="invoice-icon">
          <img src="https://wpfystatic.b-cdn.net/rahul/billl.png" alt="invoice-icon" />
        </div>
        <div class="body-table">
          <table>
            <tr class="item">
              <th>Service Provided</th>
              <th>Amount</th>
            </tr>
            <tr>
              <td>Custom Graphic/Illustration</td>
              <td>₹1500</td>
            </tr>
            <tr>
              <td>Custom Graphic/Illustration</td>
              <td>₹1500</td>
            </tr>
            <tr>
              <td>Custom Graphic/Illustration</td>
              <td>₹1500</td>
            </tr>
            <tr class="total">
              <th>Total</th>
              <th>₹1500</th>
            </tr>
          </table>
        </div>
        <div class="body-text bottom-text">
          Thank You for giving me the opportunity to work on this project. I
          hope the product met your expectations. I look forward to working with
          You &#708;_&#708;
        </div>
      </div>
      <div class="email-footer">
        <div class="footer-text">
          &copy; <a href="https://damnitrahul.com/"  target="_blank">damnitrahul.com</a>
        </div>
      </div>
    </div>
    
</body>
</html>
